<?php

namespace App\Livewire;

use App\Models\Allowance;
use App\Models\Deduction;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class SalaryComponent extends Component
{
    use WithPagination;
    public $selectedId = '';
    public $name = '';
    public $description = '';
    public $amount = '';
    public $rule = '';
    public $isEditting = false;
    public $modalType;
    #[Title('Salary Component')]
    public function render()
    {
        $allowances = Allowance::latest()->paginate(5);
        return view('livewire.admin.salary-component', [
            'allowances' => Allowance::latest()->paginate(5, ['id', 'name', 'amount', 'rule'], 'allowances'),
            'deductions' => Deduction::latest()->paginate(5, ['id', 'name', 'amount'], 'deductions'),
        ]);
    }
    public function openModal($modalType, $selectedId = null)
    {
        $this->modalType = $modalType;
        if ($selectedId) {
            $this->isEditting = true;
            if ($modalType == 'allowance') {
                $allowance = Allowance::find($selectedId);
                $this->selectedId = $allowance->id; // save id
                $this->name = $allowance->name;
                $this->description = $allowance->description;
                $this->amount = $allowance->amount;
                $this->rule = $allowance->rule;
            } else {
                $deduction = Deduction::find($selectedId);
                $this->selectedId = $deduction->id; // save id
                $this->name = $deduction->name;
                $this->description = $deduction->description;
                $this->amount = $deduction->amount;
            }
        } else {
            $this->isEditting = false;
        }
        $this->modal('main-modal')->show();
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function addAllowance()
    {
        $valdiated = $this->validate([
            'name' => ['required', 'min:3', 'max:255', 'string', 'unique:allowances,name'],
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric'],
            'rule' => ['required', 'in:fixed,percentage'],
        ]);

        $allowance = Allowance::create($valdiated);
        Toaster::success('Allowance added successfully');
        $this->closeModal();
        $this->modal('main-modal')->close();
        $this->resetPage('allowances');
    }
    public function updateAllowance()
    {
        $valdiated = $this->validate([
            'name' => ['required', 'min:3', 'max:255', 'string', 'unique:allowances,name,' . $this->selectedId],
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric'],
            'rule' => ['required', 'in:fixed,percentage'],
        ]);

        $allowance = Allowance::find($this->selectedId);
        $allowance->update($valdiated);
        Toaster::success('Allowance updated successfully');
        $this->closeModal();
        $this->modal('main-modal')->close();
    }
    public function openDeleteModal($modalType, $selectedId, $selectedName)
    {
        $this->modalType = $modalType;
        $this->selectedId = $selectedId;
        $this->name = $selectedName;
        $this->modal('delete-modal')->show();
    }
    public function deleteAllowance()
    {
        Allowance::find($this->selectedId)->delete();
        Toaster::success('Allowance deleted successfully');
        $this->closeModal();
        $this->modal('delete-modal')->close();
        $this->resetPage('allowances');
    }
    public function addDeduction()
    {
        $valdiated = $this->validate([
            'name' => ['required', 'min:3', 'max:255', 'string', 'unique:deductions,name'],
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric'],
        ]);

        $deduction = Deduction::create($valdiated);
        Toaster::success('Deduction added successfully');
        $this->closeModal();
        $this->modal('main-modal')->close();
        $this->resetPage('deductions');
    }
    public function updateDeduction()
    {
        $valdiated = $this->validate([
            'name' => ['required', 'min:3', 'max:255', 'string', 'unique:deductions,name,' . $this->selectedId],
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric'],
        ]);

        $deduction = Deduction::find($this->selectedId);
        $deduction->update($valdiated);
        Toaster::success('Deduction updated successfully');
        $this->closeModal();
        $this->modal('main-modal')->close();
    }
    public function deleteDeduction()
    {
        Deduction::find($this->selectedId)->delete();
        Toaster::success('Deduction deleted successfully');
        $this->closeModal();
        $this->modal('delete-modal')->close();
        $this->resetPage('deductions');
    }
}
