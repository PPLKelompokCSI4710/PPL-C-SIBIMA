<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\KalenderAkademikController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|dosen'])->prefix('staff')->name('staff.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/ai-config', [DashboardController::class, 'updateAiConfig'])->name('dashboard.ai-config');

    // KRS Approval (Dosen & Admin)
    Route::get('/study-plans', [KrsController::class, 'index'])->name('study-plans.index');
    Route::post('/study-plans/{studyPlan}/approve', [KrsController::class, 'approve'])->name('study-plans.approve');
    Route::post('/study-plans/{studyPlan}/reject', [KrsController::class, 'reject'])->name('study-plans.reject');

    // Progress Monitoring (Dosen & Admin)
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::put('/progress/{mahasiswa}', [ProgressController::class, 'update'])->name('progress.update');

    // Kalender & Jadwal Bimbingan Approval
    Route::get('/calendar', [KalenderAkademikController::class, 'dosenIndex'])->name('calendar');
    Route::put('/jadwal-bimbingan/{id}/approve', [KalenderAkademikController::class, 'approveBimbingan'])->name('bimbingan.approve');
    Route::put('/jadwal-bimbingan/{id}/reject', [KalenderAkademikController::class, 'rejectBimbingan'])->name('bimbingan.reject');

    // Courses Management (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

});
