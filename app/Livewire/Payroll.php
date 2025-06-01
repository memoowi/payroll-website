<?php

namespace App\Livewire;

use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Payroll as ModelsPayroll;
use App\Models\Tax;
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
            if (!$absenceDeduction ) {
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

                // Calculate total deductions
                $totalDeductions = $deductions->sum('amount');

                // Calculate total deductions based on employee's attendance
                // Assuming attendance is 5 days a week

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
                $payrollDetail = $payroll->details()->updateOrCreate(
                    ['employee_id' => $employee->id],
                    [
                        'basic_salary' => $basicSalary,
                        'total_allowances' => $totalAllowances,
                        'gross_salary' => $grossSalary,
                        'total_deductions' => $totalDeductions,
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
