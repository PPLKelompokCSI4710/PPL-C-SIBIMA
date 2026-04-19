<?php

use App\Http\Controllers\ProfileController;
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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute untuk fitur input jadwal bimbingan
Route::middleware(['auth'])->group(function () {
    Route::get('/jadwal-bimbingan/create', [\App\Http\Controllers\JadwalBimbinganController::class, 'create'])->name('jadwal-bimbingan.create');
    Route::post('/jadwal-bimbingan', [\App\Http\Controllers\JadwalBimbinganController::class, 'store'])->name('jadwal-bimbingan.store');
});

require __DIR__.'/auth.php';

// Include actor-based routes for isolated PBI development
require __DIR__.'/web/admin.php';
require __DIR__.'/web/dosen.php';
require __DIR__.'/web/mahasiswa.php';
