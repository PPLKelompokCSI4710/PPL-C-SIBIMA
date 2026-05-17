<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderSettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('mahasiswa')) {
        return redirect()->route('mahasiswa.dashboard');
    }

    if ($user->hasRole('admin') || $user->hasRole('dosen') || $user->hasRole('staff')) {
        return redirect()->route('staff.dashboard');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/reminders', [ReminderSettingsController::class, 'edit'])->name('reminders.edit');
    Route::post('/settings/reminders', [ReminderSettingsController::class, 'update'])->name('reminders.update');
});

require __DIR__.'/auth.php';

// Include actor-based routes for isolated PBI development
require __DIR__.'/web/admin.php';
require __DIR__.'/web/dosen.php';
require __DIR__.'/web/mahasiswa.php';
require __DIR__.'/web/staff.php';
