<?php

use App\Http\Controllers\ProfileController;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
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
    $stats = null;

    if ($user->hasRole('dosen')) {
        $dosen = Dosen::where('user_id', $user->id)->first();
        if ($dosen) {
            $jadwals = JadwalBimbingan::where('dosen_id', $dosen->id)->get();
            $stats = [
                'total' => $jadwals->count(),
                'pending' => $jadwals->where('status', 'pending')->count(),
                'approved' => $jadwals->where('status', 'approved')->count(),
                'completed' => $jadwals->where('status', 'completed')->count(),
                'rejected' => $jadwals->where('status', 'rejected')->count(),
                'canceled' => $jadwals->where('status', 'canceled')->count(),
            ];
        }
    }

    return Inertia::render('Dashboard', [
        'stats' => $stats,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Include actor-based routes for isolated PBI development
require __DIR__.'/web/admin.php';
require __DIR__.'/web/dosen.php';
require __DIR__.'/web/mahasiswa.php';
