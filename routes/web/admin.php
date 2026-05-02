<?php

use App\Http\Controllers\Admin\ReminderSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Di sinilah rute-rute khusus untuk PBI yang berkaitan dengan Admin (Actor: Admin).
| Jangan lupa gunakan prefix dan middleware jika diperlukan.
|
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // Contoh untuk PBI-XXX
    Route::get('/settings/reminders', [ReminderSettingsController::class, 'edit'])->name('settings.reminders.edit');
    Route::post('/settings/reminders', [ReminderSettingsController::class, 'update'])->name('settings.reminders.update');
});
