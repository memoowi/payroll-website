<?php

use App\Livewire\CompanySetting;
use App\Livewire\DepartmentManagement;
use App\Livewire\EmployeeManagement;
use App\Livewire\LeaveManagement;
use App\Livewire\Payroll;
use App\Livewire\PositionManagement;
use App\Livewire\Report;
use App\Livewire\RequestLeave;
use App\Livewire\SalaryComponent;
use App\Livewire\TaxSetting;
use App\Livewire\TimeAttendance;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::redirect('/', 'dashboard')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/company-setting', CompanySetting::class)->name('company-settings');
    Route::get('/departments', DepartmentManagement::class)->name('department-management');
    Route::get('/positions', PositionManagement::class)->name('positions');
    Route::get('/salary-components', SalaryComponent::class)->name('salary-components');
    Route::get('/tax-settings', TaxSetting::class)->name('tax-settings');
    // Not Done
    Route::get('/employee-management', EmployeeManagement::class)->name('employee-management');
    Route::get('/payroll', Payroll::class)->name('payrolls');
    Route::get('/time-and-attendance', TimeAttendance::class)->name('time-attendances');
    Route::get('/leave-management', LeaveManagement::class)->name('leave-management');
    Route::get('/reports', Report::class)->name('reports');

});

// Employee routes
Route::middleware(['auth', 'role:employee'])->name('employee.')->group(function () {
    Route::get('/request-leave', RequestLeave::class)->name('request-leave');
});

require __DIR__.'/auth.php';
