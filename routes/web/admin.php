<?php

use App\Http\Controllers\Admin\EskalasiController;
use App\Http\Controllers\Admin\ReminderSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DosenController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('dosen', DosenController::class);
    // Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // Contoh untuk PBI-XXX
    Route::get('/settings/reminders', [ReminderSettingsController::class, 'edit'])->name('settings.reminders.edit');
    Route::post('/settings/reminders', [ReminderSettingsController::class, 'update'])->name('settings.reminders.update');

    Route::get('/eskalasi', [EskalasiController::class, 'index'])->name('eskalasi.index');
});
