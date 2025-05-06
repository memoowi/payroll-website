<?php

use App\Livewire\CompanySetting;
use App\Livewire\DepartmentManagement;
use App\Livewire\PositionManagement;
use App\Livewire\SalaryComponent;
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
Route::middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/company-setting', CompanySetting::class)->name('company-settings');
    Route::get('/departments', DepartmentManagement::class)->name('department-management');
    Route::get('/positions', PositionManagement::class)->name('positions');
    Route::get('/salary-components', SalaryComponent::class)->name('salary-components');

});
require __DIR__.'/auth.php';
