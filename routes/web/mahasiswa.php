<?php

use App\Http\Controllers\InputJadwalBimbinganController;
use App\Http\Controllers\MonitoringJadwalBimbinganController;
use App\Http\Controllers\Mahasiswa\CourseController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\ProgressController;
use App\Http\Controllers\Mahasiswa\StudyPlanController;
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
    Route::get('/jadwal-bimbingan/create', [InputJadwalBimbinganController::class, 'create'])->name('jadwal-bimbingan.create');
    Route::get('/jadwal-bimbingan/schedules/{dosenId}', [InputJadwalBimbinganController::class, 'getSchedules'])->name('jadwal-bimbingan.schedules');
    Route::post('/jadwal-bimbingan', [InputJadwalBimbinganController::class, 'store'])->name('jadwal-bimbingan.store');
    Route::get('/jadwal-bimbingan', [MonitoringJadwalBimbinganController::class, 'index'])->name('jadwal.index');
    Route::patch('/jadwal-bimbingan/{id}/cancel', [MonitoringJadwalBimbinganController::class, 'cancel'])->name('jadwal.cancel');
    Route::delete('/jadwal-bimbingan/{id}', [MonitoringJadwalBimbinganController::class, 'destroy'])->name('jadwal.destroy');
    Route::get('/bimbingan/reminder', function () {
        return Inertia::render('Mahasiswa/Bimbingan/Reminder');
    })->name('bimbingan.reminder');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');

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
