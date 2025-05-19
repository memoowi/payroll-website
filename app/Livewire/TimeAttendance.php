<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Overtime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class TimeAttendance extends Component
{
    use WithPagination;
    public $selectedYearMonth = null;
    public $monthLinks = [];
    public function mount()
    {
        $this->generateMonthLinks();
    }
    public function generateMonthLinks()
    {
        $monthsData = Attendance::select(
            DB::raw('YEAR(attendance_date) as year'),
            DB::raw('MONTH(attendance_date) as month_number')
        )
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month_number', 'desc')
            ->get();

        $this->monthLinks = []; // Reset before populating
        foreach ($monthsData as $data) {
            if ($data->year && $data->month_number) {
                try {
                    $date = Carbon::createFromDate($data->year, $data->month_number, 1);
                    $this->monthLinks[] = [
                        'value' => $date->format('Y-m'), // e.g., 2023-05
                        'display' => $date->format('F Y'), // e.g., May 2023
                    ];
                } catch (\Exception $e) {
                    $this->monthLinks[] = [
                        'value' => null,
                        'display' => 'Invalid Date',
                    ];
                }
            }
        }
    }
    public function applyMonthFilter($yearMonth)
    {
        $this->selectedYearMonth = $yearMonth;
        $this->resetPage('attendancesPage');
    }

    public function clearMonthFilter()
    {
        $this->selectedYearMonth = null;
        $this->resetPage('attendancesPage');
    }
    #[Title('Time & Attendance')]
    public function render()
    {
        $attendancesQuery = Attendance::query()->with('employee');

        if ($this->selectedYearMonth) {
            try {
                $year = Carbon::createFromFormat('Y-m', $this->selectedYearMonth)->year;
                $month = Carbon::createFromFormat('Y-m', $this->selectedYearMonth)->month;
                $attendancesQuery->whereYear('attendance_date', $year)
                    ->whereMonth('attendance_date', $month);
            } catch (\Exception $e) {
                $this->selectedYearMonth = null; 
            }
        }

        $attendances = $attendancesQuery->orderBy('attendance_date', 'desc')->paginate(5, ['*'], 'attendancesPage');

        $overtimes = Overtime::latest()->paginate(10, ['*'], 'overtimesPage');

        return view('livewire.admin.time-attendance', [
            'attendances' => $attendances,
            'overtimes' => $overtimes,
            'monthLinks' => $this->monthLinks,
            'selectedYearMonthFilter' => $this->selectedYearMonth, 
        ]);
    }
}
