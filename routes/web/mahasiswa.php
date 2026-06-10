<?php

use App\Http\Controllers\InputJadwalBimbinganController;
use App\Http\Controllers\KalenderAkademikController;
use App\Http\Controllers\Mahasiswa\BimbinganReminderController;
use App\Http\Controllers\Mahasiswa\CourseController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\ProgressController;
use App\Http\Controllers\Mahasiswa\ProgressReminderController;
use App\Http\Controllers\Mahasiswa\StudyPlanController;
use App\Http\Controllers\MonitoringJadwalBimbinganController;
use App\Http\Controllers\DraftSkripsiController;
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
    Route::get('/bimbingan/reminder', [BimbinganReminderController::class, 'index'])->name('bimbingan.reminder');
    Route::get('/bimbingan/progress-reminder', [ProgressReminderController::class, 'index'])->name('bimbingan.progress_reminder');
    Route::post('/bimbingan/progress-reminder/frequency', [ProgressReminderController::class, 'updateFrequency'])->name('bimbingan.progress_reminder.update');

    Route::get('/jadwal-bimbingan/create', [InputJadwalBimbinganController::class, 'create'])->name('jadwal-bimbingan.create');
    Route::get('/jadwal-bimbingan/schedules/{dosenId}', [InputJadwalBimbinganController::class, 'getSchedules'])->name('jadwal-bimbingan.schedules');
    Route::post('/jadwal-bimbingan', [InputJadwalBimbinganController::class, 'store'])->name('jadwal-bimbingan.store');
    Route::get('/jadwal-bimbingan', [MonitoringJadwalBimbinganController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal-bimbingan/export-pdf', [ExportBimbinganController::class, 'exportPdf'])->name('jadwal.exportPdf');
    Route::patch('/jadwal-bimbingan/{id}/cancel', [MonitoringJadwalBimbinganController::class, 'cancel'])->name('jadwal.cancel');
    Route::delete('/jadwal-bimbingan/{id}', [MonitoringJadwalBimbinganController::class, 'destroy'])->name('jadwal.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [KalenderAkademikController::class, 'mahasiswaIndex'])->name('calendar');

    // Study Plan routes
    Route::get('/study-plans', [StudyPlanController::class, 'index'])->name('study-plans.index');
    Route::post('/study-plans', [StudyPlanController::class, 'store'])->name('study-plans.store');
    Route::put('/study-plans/{studyPlan}', [StudyPlanController::class, 'update'])->name('study-plans.update');
    Route::delete('/study-plans/{studyPlan}', [StudyPlanController::class, 'destroy'])->name('study-plans.destroy');

    // Course routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

    // Progress routes
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    // Draft Skripsi routes
    Route::get('/draft-skripsi', [DraftSkripsiController::class, 'index'])->name('draft-skripsi.index');
    Route::post('/draft-skripsi', [DraftSkripsiController::class, 'store'])->name('draft-skripsi.store');
    Route::post('/draft-skripsi/{draft}', [DraftSkripsiController::class, 'update'])->name('draft-skripsi.update');
    Route::put('/draft-skripsi/{draft}/catatan', [DraftSkripsiController::class, 'updateCatatan'])->name('draft-skripsi.updateCatatan');
    Route::delete('/draft-skripsi/{draft}', [DraftSkripsiController::class, 'destroy'])->name('draft-skripsi.destroy');
});
