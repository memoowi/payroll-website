<?php

use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    Route::get('/company-setting', [CompanySettingController::class, 'index'])->name('company-settings');

});
require __DIR__.'/auth.php';
