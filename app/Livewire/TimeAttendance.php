<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Overtime;
use Livewire\Attributes\Title;
use Livewire\Component;

class TimeAttendance extends Component
{
    #[Title('Time & Attendance')]
    public function render()
    {
        return view('livewire.admin.time-attendance', [
            'attendances' => Attendance::latest()->paginate(10, ['*'], 'attendances'),
            'overtimes' => Overtime::latest()->paginate(10, ['*'], 'overtimes'),
        ]);
    }
    public function getAttendances()
    {
        $attendances = Attendance::latest()->get();
        return $attendances;
    }
}
