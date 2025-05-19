<?php

namespace App\Livewire;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class RequestLeave extends Component
{
    use WithPagination;
    public $isEditting = false;
    public $leaveRequestId = '';
    public $leaveType = '';
    public $startDate = '';
    public $endDate = '';
    public $reason = '';
    public function render()
    {
        return view('livewire.employee.request-leave', [
            'leaveRequests' => Auth::user()->employee->leaveRequests()->latest()->paginate(10),
        ]);
    }
    public function openModal($id = null)
    {
        if ($id) {
            $this->isEditting = true;
            $this->leaveRequestId = $id;
            $leaveRequest = LeaveRequest::find($id);
        }
        $this->modal('main-modal')->show();
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function save()
    {
        $this->validate([
            'leaveType' => 'required|string|in:sick,vacation,personal,other',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($this->isEditting) {
            $leaveRequest = LeaveRequest::find($this->leaveRequestId);
            if($leaveRequest->status !== 'pending') {
                Toaster::error('You can only edit leave requests on pending.');
                return;
            }
            $leaveRequest->update([
                'leave_type' => $this->leaveType,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'reason' => $this->reason,
            ]);
        } else {
            LeaveRequest::create([
                'employee_id' => Auth::user()->employee->id,
                'leave_type' => $this->leaveType,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'reason' => $this->reason,
            ]);
        }

        Toaster::success('Leave request saved successfully!');
        $this->modal('main-modal')->close();
        $this->closeModal();
    }
}
