<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dosen Routes
|--------------------------------------------------------------------------
|
| Rute-rute khusus untuk PBI yang berkaitan dengan Dosen Pembimbing.
|
*/

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    // Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index'); // Contoh untuk PBI-YYY
});
