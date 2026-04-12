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
    // Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik.index'); // Contoh untuk PBI-ZZZ
});
