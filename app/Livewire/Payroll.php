<?php

namespace App\Livewire;

use App\Models\Allowance;
use App\Models\CompanySetting;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Payroll as ModelsPayroll;
use App\Models\Tax;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Payroll extends Component
{
    use WithPagination;
    public $periodStart = '';
    public $periodEnd = '';
    public $paymentDate = '';
    public $notes = '';
    public $isEditting = false;
    public $selectedPayrollId = '';
    #[Title('Payroll Management')]
    public function render()
    {
        return view('livewire.admin.payroll', [
            'payrolls' => ModelsPayroll::latest()->paginate(10),
            'allowances' => Allowance::all(),
            'deductions' => Deduction::all(),
        ]);
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function openModal($id = null)
    {
        if ($id) {
            $this->selectedPayrollId = $id;
            $this->isEditting = true;
            $payroll = ModelsPayroll::find($id);
            $this->periodStart = $payroll->payroll_period_start;
            $this->periodEnd = $payroll->payroll_period_end;
            $this->paymentDate = $payroll->payment_date;
            $this->notes = $payroll->notes;
        } else {
            $this->isEditting = false;
        }
        $this->modal('main-modal')->show();
    }
    public function save()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date',
            'paymentDate' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);
        if ($this->isEditting) {
            $payroll = ModelsPayroll::find($this->selectedPayrollId);
            $payroll->update([
                'payroll_period_start' => $this->periodStart,
                'payroll_period_end' => $this->periodEnd,
                'payment_date' => $this->paymentDate,
                'notes' => $this->notes,
            ]);
        } else {
            ModelsPayroll::create([
                'payroll_period_start' => $this->periodStart,
                'payroll_period_end' => $this->periodEnd,
                'payment_date' => $this->paymentDate,
                'notes' => $this->notes,
            ]);
        }
        Toaster::success('Payroll saved successfully!');
        $this->closeModal();
        $this->modal('main-modal')->close();
        $this->resetPage();
    }
    public function openDeleteModal($id)
    {
        $this->selectedPayrollId = $id;
        $this->modal('delete-modal')->show();
    }
    public function delete()
    {
        $payroll = ModelsPayroll::find($this->selectedPayrollId);
        if ($payroll) {
            $payroll->delete();
            Toaster::success('Payroll deleted successfully!');
        } else {
            Toaster::error('Payroll not found!');
        }
        $this->closeModal();
        $this->modal('delete-modal')->close();
        $this->resetPage();
    }

    // Generate Payroll Details
    public $selectedAllowances = [];
    public $selectedDeductions = [];
    public function openGenerateModal($id)
    {
        $payroll = ModelsPayroll::find($id);
        if (!$payroll) {
            Toaster::error('Payroll not found!');
            return;
        }
        $this->selectedPayrollId = $id;
        $this->periodStart = $payroll->payroll_period_start;
        $this->periodEnd = $payroll->payroll_period_end;
        $this->paymentDate = $payroll->payment_date;
        $this->notes = $payroll->notes;
        $this->modal('generate-modal')->show();
    }
    public function generate()
    {

        try {
            $this->validate([
                'selectedAllowances' => 'array|exists:allowances,id',
                'selectedDeductions' => 'array|exists:deductions,id',
            ]);
            // dd($this->selectedAllowances, $this->selectedDeductions);

            $payroll = ModelsPayroll::find($this->selectedPayrollId);
            if (!$payroll) {
                Toaster::error('Payroll not found!');
                return;
            }

            $companySetting = CompanySetting::first();
            if (!$companySetting) {
                Toaster::error('Company settings not found!');
                return;
            }
            $checkInTime = $companySetting->check_in_time;
            $workingDays = $companySetting->working_days;

            $employees = Employee::all();

            $fixedAllowances = Allowance::where('rule', 'fixed')->whereIn('id', $this->selectedAllowances)->get();
            $percentageAllowances = Allowance::where('rule', 'percentage')->whereIn('id', $this->selectedAllowances)->get();

            $deductions = Deduction::whereIn('id', $this->selectedDeductions)->get();
            // Get required deductions
            $lateDeduction = Deduction::where('name', 'Late Arrival')->first();
            $absenceDeduction = Deduction::where('name', 'Absence Without Notice')->first();
            $unapprovedLeaveDeduction = Deduction::where('name', 'Unapproved Leave')->first();

            if (!$lateDeduction) {
                Toaster::error("'Late Arrival' deduction not found!");
                return;
            }
            if (!$absenceDeduction) {
                Toaster::error("'Absence Without Notice' deduction not found!");
                return;
            }
            if (!$unapprovedLeaveDeduction) {
                Toaster::error("'Unapproved Leave' deduction not found!");
                return;
            }

            $taxes = Tax::all();
            // taxes threshold is string with format '0-10000000' so need to convert it to array
            $taxes = $taxes->map(function ($tax) {
                $thresholds = explode('-', $tax->threshold);
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'rate' => $tax->rate,
                    'min' => (float)$thresholds[0],
                    'max' => (float)$thresholds[1],
                ];
            });

            // dd($taxes);

            if ($employees->isEmpty()) {
                Toaster::error('No employees found to generate payroll details.');
                return;
            }

            DB::beginTransaction();

            // Loop through each employee and calculate their payroll details
            foreach ($employees as $employee) {
                $basicSalary = $employee->salary->amount;
                $totalAllowances = $fixedAllowances->sum('amount');
                $grossSalary = $basicSalary + $totalAllowances;

                // Calculate percentage allowances based on gross salary
                foreach ($percentageAllowances as $allowance) {
                    $totalAllowances += $allowance->amount * $basicSalary;
                }

                // Calculate total deductions based on employee's attendance
                $calculatedLateDeductionAmount = 0;
                $calculatedAbsenceWithoutNoticeDeductionAmount = 0;
                $calculatedUnapprovedLeaveDeductionAmount = 0;

                // LATE ARRIVAL
                $attendance = $employee->attendances()
                    ->whereBetween('attendance_date', [$payroll->payroll_period_start, $payroll->payroll_period_end])
                    ->get();

                foreach ($attendance as $record) {
                    // Check for late arrivals
                    if ($record->check_in && Carbon::parse($record->check_in)->gt(Carbon::parse($checkInTime))) {
                        $calculatedLateDeductionAmount += $lateDeduction->amount;
                    }
                }

                // ABSENCE WITHOUT NOTICE & UNAPPROVED LEAVE
                $absenceWithoutNoticeDaysCount = 0;
                $unapprovedLeaveRequestDaysCount = 0;

                $periodIterator = CarbonPeriod::create($payroll->payroll_period_start, $payroll->payroll_period_end);
                $expectedWorkDatesStrings = [];

                foreach ($periodIterator as $date) {
                    $isWorkingDay = false;
                    if ($workingDays == 5 && $date->isWeekday()) { // Mon-Fri
                        $isWorkingDay = true;
                    } elseif ($workingDays == 6 && !$date->isSunday()) { // Mon-Sat
                        $isWorkingDay = true;
                    } elseif ($workingDays == 7) { // All days
                        $isWorkingDay = true;
                    }

                    if ($isWorkingDay) {
                        $expectedWorkDatesStrings[] = $date->toDateString();
                    }
                }

                if (!empty($expectedWorkDatesStrings)) {
                    $actualAttendanceDates = $employee->attendances()
                        ->whereIn('attendance_date', $expectedWorkDatesStrings) 
                        ->pluck('attendance_date')
                        ->map(fn($attDate) => Carbon::parse($attDate)->toDateString())
                        ->unique()->toArray();

                    $employeeLeaveRequestsInPeriod = $employee->leaveRequests()
                        ->where(function ($query) use ($payroll) {
                            $query->where('start_date', '<=', $payroll->payroll_period_end)
                                ->where('end_date', '>=', $payroll->payroll_period_start);
                        })->get();

                    foreach ($expectedWorkDatesStrings as $expectedDateStr) {
                        $currentExpectedDate = Carbon::parse($expectedDateStr);

                        // 1. Check if employee was present
                        if (in_array($expectedDateStr, $actualAttendanceDates)) {
                            continue; // Present, no absence deduction for this day
                        }

                        // Employee is ABSENT on this $expectedDateStr. Now check leave requests.
                        $leaveStatusForDay = null; // null, 'approved', 'unapproved_request'

                        foreach ($employeeLeaveRequestsInPeriod as $lr) {
                            if ($currentExpectedDate->betweenIncluded(Carbon::parse($lr->start_date), Carbon::parse($lr->end_date))) {
                                if ($lr->status === 'approved') {
                                    $leaveStatusForDay = 'approved';
                                    break; // Approved leave found, highest precedence for this check
                                } elseif (in_array($lr->status, ['pending', 'rejected'])) {
                                    $leaveStatusForDay = 'unapproved_request';
                                    // Don't break, an approved one could still exist for this day (though unlikely)
                                    // But if logic is that any non-approved request means "unapproved request day", this is fine.
                                }
                            }
                        }

                        if ($leaveStatusForDay === 'approved') {
                            // On approved leave, so no penalty for "Absence Without Notice" or "Unapproved Leave Request"
                            // (Potential for "Approved Unpaid Leave" deduction if that's a separate category and selected)
                            continue;
                        } elseif ($leaveStatusForDay === 'unapproved_request') {
                            $unapprovedLeaveRequestDaysCount++;
                        } else {
                            // Absent, and no leave request (approved, pending, or rejected) covers this day
                            $absenceWithoutNoticeDaysCount++;
                        }
                    }
                }

                dd($calculatedLateDeductionAmount,$absenceWithoutNoticeDaysCount, $unapprovedLeaveRequestDaysCount);

                $calculatedAbsenceWithoutNoticeDeductionAmount = $absenceWithoutNoticeDaysCount * $absenceDeduction->amount;
                $calculatedUnapprovedLeaveDeductionAmount = $unapprovedLeaveRequestDaysCount * $unapprovedLeaveDeduction->amount;

                // Calculate fixed other deductions
                $fixedOtherDeductionsAmount = $deductions->sum('amount');
                // Calculate total deductions
                $totalDeductions = $fixedOtherDeductionsAmount +
                    $calculatedLateDeductionAmount +
                    $calculatedAbsenceWithoutNoticeDeductionAmount +
                    $calculatedUnapprovedLeaveDeductionAmount;

                // Calculate total taxes based on basic salary
                $totalTaxes = 0;
                foreach ($taxes as $tax) {
                    if ($basicSalary >= $tax['min'] && $basicSalary <= $tax['max']) {
                        $totalTaxes += $basicSalary * $tax['rate'];
                    }
                }

                // Calculate net salary
                $netSalary = $grossSalary - ($totalDeductions + $totalTaxes);

                // Create or update payroll details for the employee
                $payroll->payrollDetails()->updateOrCreate(
                    ['employee_id' => $employee->id],
                    [
                        'basic_salary' => $basicSalary,
                        'total_allowances' => $totalAllowances,
                        'gross_salary' => $grossSalary,
                        'total_deductions' => $totalDeductions,
                        'total_taxes' => $totalTaxes,
                        'net_salary' => $netSalary,
                        'payment_status' => 'unpaid',
                    ]
                );
            }

            DB::commit();


            Toaster::success('Payroll details generated successfully!');
            $this->modal('generate-modal')->close();
        } catch (\Exception $e) {
            Toaster::error('Error generating payroll details: ' . $e->getMessage());
            DB::rollBack();
        }
    }
}
