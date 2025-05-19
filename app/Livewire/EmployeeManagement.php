<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class EmployeeManagement extends Component
{
    use WithPagination;
    public $email = '';
    public $password = '';
    public $fullName = '';
    public $address = '';
    public $phone = '';
    public $hireDate = '';
    public $selectedDepartmentId = '';
    public $selectedPositionId = '';
    public $bankName = '';
    public $bankAccount = '';
    public $npwp = '';
    public $salary = '';
    public $payFrequency = 'monthly';
    public $effectiveDate = '';
    public $positions = [];
    public $isEditting = false;
    public $employeeId = null;

    public function mount()
    {
        $this->hireDate = now()->format('Y-m-d');
    }
    #[Title('Employee Management')]
    public function render()
    {
        return view('livewire.admin.employee-management', [
            'departments' => Department::latest()->get(),
            // 'positions' => Position::latest()->get(),
            'employees' => Employee::latest()->paginate(10)
        ]);
    }
    public function updatePositions()
    {
        $this->selectedPositionId = '';
        $this->positions = Position::where('department_id', $this->selectedDepartmentId)->get();
    }
    public function closeModal()
    {
        $this->reset();
        $this->hireDate = now()->format('Y-m-d');
    }
    public function openModal($modalType, $employeeId = null)
    {
        $this->isEditting = $modalType === 'edit';
        if ($this->isEditting || $modalType === 'view') {
            $this->employeeId = $employeeId;
            $employee = Employee::find($employeeId);
            if ($employee) {
                $this->email = $employee->user->email;
                $this->password = $employee->user->password;
                $this->fullName = $employee->full_name;
                $this->address = $employee->address;
                $this->phone = $employee->phone;
                $this->hireDate = $employee->hire_date;
                $this->bankName = $employee->bank_name;
                $this->bankAccount = $employee->bank_account_number;
                $this->npwp = $employee->npwp;
                // Assuming the position_id is stored in the employee table
                // You can adjust this logic based on your actual data structure
                $this->selectedPositionId = $employee->position_id;
                $this->selectedDepartmentId = $employee->position->department_id;
                $this->positions = Position::where('department_id', $this->selectedDepartmentId)->get();
                $this->salary = $employee->salary->amount;
                $this->payFrequency = $employee->salary->pay_frequency;
                $this->effectiveDate = $employee->salary->effective_date;
            }
        }
        if ($modalType === 'view') {
            $this->modal('view-modal')->show();
            return;
        }
        $this->modal('main-modal')->show();
    }
    public function save()
    {
        $employee = null;
        $user = null;
        if ($this->isEditting) {
            $employee = Employee::find($this->employeeId);
            if ($employee) {
                $user = $employee->user;
            }
        }
        $this->validate([
            'email' => 'required|email|max:255|unique:users,email,' . ($user ? $user->id : 'NULL'),
            'password' => 'required|string|min:8|max:255',
            'fullName' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'hireDate' => 'required|date',
            'selectedDepartmentId' => 'required|exists:departments,id',
            'selectedPositionId' => 'required|exists:positions,id',
            'bankName' => 'required',
            'bankAccount' => 'required',
            'npwp' => 'nullable|string|max:20',
            'salary' => 'required|numeric|min:0',
            'payFrequency' => 'required|string|in:monthly,weekly',
            'effectiveDate' => 'required|date',
        ]);

        if ($this->isEditting) {
            if ($employee) {
                DB::beginTransaction();
                try {
                    $user = $employee->user;
                    $employee->update([
                        'full_name' => $this->fullName,
                        'address' => $this->address,
                        'phone' => $this->phone,
                        'hire_date' => $this->hireDate,
                        'bank_name' => $this->bankName,
                        'bank_account_number' => $this->bankAccount,
                        'npwp' => $this->npwp,
                        'position_id' => $this->selectedPositionId,
                    ]);
                    if ($user) {
                        $user->update([
                            'name' => $this->fullName,
                            // Assuming you want to update the email only if it's changed
                            // You can adjust this logic based on your actual requirements
                            // If you want to always update the email, uncomment the line below
                            // 'email' => $this->email,
                            // If you want to update the email only if it's changed, use the line below
                            'email' => $this->email !== $user->email ? $this->email : $user->email,
                            // Assuming you want to update the password only if it's changed
                            // You can adjust this logic based on your actual requirements
                            'password' => $this->password !== $user->password ? bcrypt($this->password) : $user->password,
                            // If you want to always update the password, uncomment the line below
                            // 'password' => bcrypt($this->password),
                        ]);
                    }
                    // Update salary logic here
                    $employee->salary()->update([
                        'amount' => $this->salary,
                        'pay_frequency' => $this->payFrequency,
                        'effective_date' => $this->effectiveDate,
                    ]);
                    DB::commit();
                    Toaster::success('Employee updated successfully');
                    $this->modal('main-modal')->close();
                    $this->closeModal();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Toaster::error('Failed to update employee. Please try again.');
                    // throw $e;
                }
            }
        } else {
            DB::beginTransaction();
            try {
                // Create user logic here
                $user = User::create([
                    'name' => $this->fullName,
                    'email' => $this->email,
                    'password' => bcrypt($this->password),
                ]);
                // Create employee logic here
                $employee = Employee::create([
                    'full_name' => $this->fullName,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'hire_date' => $this->hireDate,
                    'bank_name' => $this->bankName,
                    'bank_account_number' => $this->bankAccount,
                    'npwp' => $this->npwp,
                    'position_id' => $this->selectedPositionId,
                ]);
                // Associate user with employee
                $user->employee()->associate($employee);
                $user->save();
                // Create salary logic here
                $employee->salary()->create([
                    'amount' => $this->salary,
                    'pay_frequency' => $this->payFrequency,
                    'effective_date' => $this->effectiveDate,
                ]);
                DB::commit();
                Toaster::success('Employee created successfully');
                $this->modal('main-modal')->close();
                $this->closeModal();
                $this->resetPage();
            } catch (\Exception $e) {
                DB::rollBack();
                Toaster::error('Failed to create employee. Please try again.');
                throw $e;
            }
        }
    }
    public function openDeleteModal($employeeId, $employeeName)
    {
        $this->employeeId = $employeeId;
        $this->fullName = $employeeName;
        $this->modal('delete-modal')->show();
    }
    public function delete()
    {
        $employee = Employee::find($this->employeeId);
        if ($employee) {
            DB::beginTransaction();
            try {
                $user = $employee->user;
                if ($user) {
                    $user->delete();
                }
                $employee->salary()->delete();
                $employee->delete();
                DB::commit();
                Toaster::success('Employee deleted successfully');
                $this->modal('delete-modal')->close();
                $this->closeModal();
                $this->resetPage();
            } catch (\Exception $e) {
                DB::rollBack();
                Toaster::error('Failed to delete employee. Please try again.');
            }
        }
    }
}
