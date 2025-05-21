<?php

namespace App\Livewire;

use App\Models\LeaveRequest;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class LeaveManagement extends Component
{
    use WithPagination;
    public $selectedId = '';
    public $status = '';
    public $leaveRequestData = null;
    #[Title('Leave Management')]
    public function render()
    {
        return view('livewire.admin.leave-management',[
            'leaveRequests' => LeaveRequest::latest()->paginate(10),
        
        ]);
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function openViewModal($id)
    {
        $data = LeaveRequest::find($id);
        if(!$data) {
            Toaster::error('Leave request not found.');
            return;
        }
        $this->leaveRequestData = $data;
        $this->modal('view-modal')->show();
    }
    public function openStatusModal($id)
    {
        $data = LeaveRequest::find($id,['id','status']);
        if(!$data) {
            Toaster::error('Leave request not found.');
            return;
        }
        $this->status = $data->status;
        $this->selectedId = $data->id;
        $this->modal('status-modal')->show();
    }
    public function save()
    {
        $this->validate([
            'status' => 'required|string|in:approved,rejected,pending',
        ]);
        $leaveRequest = LeaveRequest::find($this->selectedId);
        if(!$leaveRequest) {
            Toaster::error('Leave request not found.');
            return;
        }
        $leaveRequest->update([
            'status' => $this->status,
            'approval_date' => $this->status === 'approved' ? now() : null,
        ]);
        Toaster::success('Leave request status updated successfully.');
        $this->modal('status-modal')->close();
        $this->closeModal();
    }
}