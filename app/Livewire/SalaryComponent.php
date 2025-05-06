<?php

namespace App\Livewire;

use App\Models\Allowance;
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
    public $isEditAllowance = false;
    #[Title('Salary Component')]
    public function render()
    {
        $allowances = Allowance::latest()->paginate(5);
        return view('livewire.admin.salary-component',[
            'allowances' => Allowance::latest()->paginate(5)
            // 'deductions' => Deduction::latest()->paginate(5),
        ]);
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function addAllowance()
    {
        $valdiated = $this->validate([
           'name' => ['required','min:3','max:255','string','unique:allowances,name'], 
           'description' => ['required','string','max:1000'],
           'amount' => ['required','numeric'],
           'rule' => ['required','in:fixed,percentage'],
        ]);

        $allowance = Allowance::create($valdiated);
        Toaster::success('Allowance added successfully');
        $this->closeModal();
        $this->modal('allowance')->close();
    }
    public function editModalAllowance($allowanceId)
    {
        $allowance = Allowance::find($allowanceId);
        $this->selectedId = $allowance->id; // save id
        $this->name = $allowance->name;
        $this->description = $allowance->description;
        $this->amount = $allowance->amount;
        $this->rule = $allowance->rule;
        $this->isEditAllowance = true;
        $this->modal('allowance')->show();
    }
    public function updateAllowance()
    {
        $valdiated = $this->validate([
            'name' => ['required','min:3','max:255','string','unique:allowances,name,'.$this->selectedId], 
            'description' => ['required','string','max:1000'],
            'amount' => ['required','numeric'],
            'rule' => ['required','in:fixed,percentage'],
         ]);
 
         $allowance = Allowance::find($this->selectedId);
         $allowance->update($valdiated);
         Toaster::success('Allowance updated successfully');
         $this->closeModal();
         $this->modal('allowance')->close();
    }
    public function openDeleteAllowanceModal($allowanceId, $allowanceName)
    {
        $this->selectedId = $allowanceId;
        $this->name = $allowanceName;
        $this->modal('delete-allowance')->show();
    }
    public function deleteAllowance()
    {
        Allowance::find($this->selectedId)->delete();
        Toaster::success('Allowance deleted successfully');
        $this->closeModal();
        $this->modal('delete-allowance')->close();
    }
}
