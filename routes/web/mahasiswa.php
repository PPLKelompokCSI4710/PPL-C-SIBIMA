<?php

use App\Http\Controllers\Mahasiswa\BimbinganReminderController;
use App\Http\Controllers\Mahasiswa\ProgressReminderController;
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
    Route::get('/bimbingan/reminder', [BimbinganReminderController::class, 'index'])
        ->name('bimbingan.reminder');

    Route::get('/bimbingan/progress-reminder', [ProgressReminderController::class, 'index'])->name('bimbingan.progress_reminder');
    Route::post('/bimbingan/progress-reminder/frequency', [ProgressReminderController::class, 'updateFrequency'])->name('bimbingan.progress_reminder.update');

    // Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik.index'); // Contoh untuk PBI-ZZZ
});
