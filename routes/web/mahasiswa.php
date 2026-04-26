<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
        return Inertia::render('Mahasiswa/Bimbingan/Reminder');
    })->name('bimbingan.reminder');

    // Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik.index'); // Contoh untuk PBI-ZZZ
});
