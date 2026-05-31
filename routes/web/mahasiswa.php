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
    Route::get('/bimbingan/reminder', [BimbinganReminderController::class, 'index'])->name('bimbingan.reminder');
    Route::get('/bimbingan/progress-reminder', [ProgressReminderController::class, 'index'])->name('bimbingan.progress_reminder');
    Route::post('/bimbingan/progress-reminder/frequency', [ProgressReminderController::class, 'updateFrequency'])->name('bimbingan.progress_reminder.update');

    Route::get('/jadwal-bimbingan/create', [InputJadwalBimbinganController::class, 'create'])->name('jadwal-bimbingan.create');
    Route::get('/jadwal-bimbingan/schedules/{dosenId}', [InputJadwalBimbinganController::class, 'getSchedules'])->name('jadwal-bimbingan.schedules');
    Route::post('/jadwal-bimbingan', [InputJadwalBimbinganController::class, 'store'])->name('jadwal-bimbingan.store');
    Route::get('/jadwal-bimbingan', [MonitoringJadwalBimbinganController::class, 'index'])->name('jadwal.index');
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
    Route::put('/progress', [ProgressController::class, 'update'])->name('progress.update');
});
