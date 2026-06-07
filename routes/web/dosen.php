<?php

use App\Http\Controllers\Dosen\KetersediaanJadwalController;
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
    // Ketersediaan Jadwal Bimbingan
    Route::get('/ketersediaan-jadwal', [KetersediaanJadwalController::class, 'index'])->name('ketersediaan-jadwal.index');
    Route::post('/ketersediaan-jadwal', [KetersediaanJadwalController::class, 'store'])->name('ketersediaan-jadwal.store');
    Route::delete('/ketersediaan-jadwal/{id}', [KetersediaanJadwalController::class, 'destroy'])->name('ketersediaan-jadwal.destroy');

    // Monitoring Jadwal Bimbingan
    Route::get('/monitoring-jadwal', [\App\Http\Controllers\Dosen\MonitoringJadwalController::class, 'index'])->name('monitoring-jadwal.index');
    Route::put('/monitoring-jadwal/{id}/status', [\App\Http\Controllers\Dosen\MonitoringJadwalController::class, 'updateStatus'])->name('monitoring-jadwal.update-status');
    Route::post('/monitoring-jadwal/{id}/catatan', [\App\Http\Controllers\Dosen\MonitoringJadwalController::class, 'storeCatatan'])->name('monitoring-jadwal.store-catatan');
    Route::put('/monitoring-jadwal/catatan/{id}', [\App\Http\Controllers\Dosen\MonitoringJadwalController::class, 'updateCatatan'])->name('monitoring-jadwal.update-catatan');

    // Riwayat Bimbingan
    Route::get('/riwayat-bimbingan', [\App\Http\Controllers\Dosen\RiwayatBimbinganController::class, 'index'])->name('riwayat-bimbingan.index');
});
