<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Position;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class PositionManagement extends Component
{
    use WithPagination;
    public $name = '';
    public $description = '';
    public $selectedDepartmentId = '';
    public $departments = [];
    public $isEditting = false;
    public $selectedId = '';
    public function mount()
    {
        $this->departments = Department::all();
    }
    #[Title('Position Management')]
    public function render()
    {
        return view('livewire.admin.position-management',[
            'positions' => Position::latest()->paginate(10)
        ]);
    }
    public function closeModal()
    {
        $this->reset(['name', 'description', 'selectedDepartmentId','selectedId','isEditting']);
    }
    public function addPosition()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions,name'],
            'description' => ['required', 'string', 'max:255'],
            'selectedDepartmentId' => ['required', 'exists:departments,id'],
        ]);

        Position::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'department_id' => $validated['selectedDepartmentId'],
        ]);
        $this->closeModal();
        $this->modal('position')->close();
        Toaster::success('Position added successfully');
        $this->resetPage();
    }
    public function openEditModal($positionId)
    {
        $positionData = Position::find($positionId);
        $this->selectedId = $positionData->id;
        $this->name = $positionData->name;
        $this->description = $positionData->description;
        $this->selectedDepartmentId = $positionData->department_id;
        $this->isEditting = true;
        $this->modal('position')->show();
    }
    public function updatePosition()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions,name'],
            'description' => ['required', 'string', 'max:255'],
            'selectedDepartmentId' => ['required', 'exists:departments,id'],
        ]);

        $positionData = Position::find($this->selectedId);
        $positionData->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'department_id' => $validated['selectedDepartmentId'],
        ]);
        $this->closeModal();
        $this->modal('position')->close();
        Toaster::success('Position updated successfully');
    }
    public function openDeleteModal($positionId, $positionName)
    {
        $this->selectedId = $positionId;
        $this->name = $positionName;
        $this->modal('delete-position')->show();
    }
    public function deletePosition()
    {
        $positionData = Position::find($this->selectedId);
        $positionData->delete();
        $this->closeModal();
        $this->modal('delete-position')->close();
        Toaster::success('Position deleted successfully');
        $this->resetPage();
    }
}
