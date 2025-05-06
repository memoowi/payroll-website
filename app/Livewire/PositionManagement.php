<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Position;
use Livewire\Attributes\Title;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class PositionManagement extends Component
{
    public $name = '';
    public $description = '';
    public $selectedDepartmentId = '';
    public $departments = [];
    public function mount()
    {
        $this->departments = Department::all();
    }
    #[Title('Position Management')]
    public function render()
    {
        return view('livewire.admin.position-management');
    }
    public function closeModal()
    {
        $this->reset();
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
        $this->modal('add-position')->close();
        Toaster::success('Position added successfully');
    }
}
