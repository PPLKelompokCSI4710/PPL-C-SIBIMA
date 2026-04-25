<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mahasiswa Routes
|--------------------------------------------------------------------------
|
| Rute-rute khusus untuk PBI yang berkaitan dengan Mahasiswa.
|
*/

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    // Dummy route untuk melihat implementasi frontend
    Route::get('/bimbingan/reminder', function () {
        return \Inertia\Inertia::render('Mahasiswa/Bimbingan/Reminder');
    })->name('bimbingan.reminder');

    Route::get('/bimbingan/progress-reminder', [\App\Http\Controllers\Mahasiswa\ProgressReminderController::class, 'index'])->name('bimbingan.progress_reminder');
    Route::post('/bimbingan/progress-reminder/frequency', [\App\Http\Controllers\Mahasiswa\ProgressReminderController::class, 'updateFrequency'])->name('bimbingan.progress_reminder.update');

    // Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik.index'); // Contoh untuk PBI-ZZZ
});
