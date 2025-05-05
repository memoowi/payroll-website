<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class DepartmentManagement extends Component
{
    use WithPagination;
    // public $departments = [];
    public $selectedId = '';
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
        Toaster::success('Department added successfully');
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function openDeleteModal($departmentId, $departmentName)
    {
        $this->selectedId = $departmentId;
        $this->name = $departmentName;
        $this->modal('delete-department')->show();
    }
    public function deleteDepartment()
    {
        $departmentData = Department::find($this->selectedId);
        $departmentData->delete();
        $this->dispatch('deleted-department');
        $this->closeModal();
        $this->modal('delete-department')->close();
        Toaster::success('Department deleted successfully');
    }
    public function openEditModal($departmentId)
    {
        $departmentData = Department::find($departmentId);
        $this->selectedId = $departmentData->id;
        $this->name = $departmentData->name;
        $this->description = $departmentData->description;
        $this->modal('edit-department')->show();
    }
    public function updateDepartment()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        $departmentData = Department::find($this->selectedId);
        $departmentData->update($validated);
        $this->dispatch('updated-department');
        $this->closeModal();
        $this->modal('edit-department')->close();
        Toaster::success('Department updated successfully');
    }
}
