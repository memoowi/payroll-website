<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentManagement extends Component
{
    use WithPagination;
    // public $departments = [];
    public $selected;
    public $name = '';
    public $description = '';
    #[Title('Department Management')]
    public function render()
    {
        return view('livewire.admin.department-management',[
            'departments' => Department::paginate(10)
        ]);
    }
    #[On(['added-department', 'deleted-department'])]
    public function refreshTable()
    {
        $this->resetPage();
    }
    public function addDepartment()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        Department::create($validated);
        $this->dispatch('added-department');
        $this->closeModal();
        $this->modal('add-department')->close();
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function deleteDepartment($departmentId)
    {
        $departmentData = Department::find($departmentId);
        $departmentData->delete();
        $this->dispatch('deleted-department');
        $this->closeModal();
        $this->modal('delete-department')->close();
    }
}
