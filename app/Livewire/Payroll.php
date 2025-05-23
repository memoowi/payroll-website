<?php

namespace App\Livewire;

use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Payroll as ModelsPayroll;
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
        return view('livewire.admin.payroll',[
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
}
