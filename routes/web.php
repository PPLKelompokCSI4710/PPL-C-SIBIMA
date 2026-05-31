<?php

use App\Http\Controllers\GoogleCalendarAuthController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\JadwalRequestController;
use App\Http\Controllers\KalenderAkademikController;
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

Route::post('/api/ai-chat', [AiChatController::class, 'generate'])->name('api.ai-chat');

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

    // Google Calendar Connection Routes
    Route::get('/google/connect', [GoogleCalendarAuthController::class, 'redirectToGoogle'])->name('google.connect');
    Route::get('/google/callback', [GoogleCalendarAuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

require __DIR__.'/auth.php';

// Preview routes for Kalender Akademik
// Jika user sudah login, data auth sungguhan digunakan.
// Jika belum login, fallback ke mock data sehingga route tetap bisa diakses untuk dev.
Route::prefix('preview')->group(function () {
    Route::get('/kalender-admin', function () {
        if (! auth()->check()) {
            Inertia::share('auth', ['user' => ['name' => 'Admin Preview', 'email' => 'admin@preview.com']]);
        }

        return app(KalenderAkademikController::class)->adminIndex();
    })->name('preview.kalender-admin');

    Route::post('/kalender-admin', [KalenderAkademikController::class, 'adminStore'])->name('admin.kalender-akademik.store');
    Route::put('/kalender-admin/{kalenderAkademik}', [KalenderAkademikController::class, 'adminUpdate'])->name('admin.kalender-akademik.update');
    Route::delete('/kalender-admin/{kalenderAkademik}', [KalenderAkademikController::class, 'adminDestroy'])->name('admin.kalender-akademik.destroy');

    Route::get('/kalender-dosen', function () {
        if (! auth()->check()) {
            Inertia::share('auth', ['user' => ['name' => 'Dosen Preview', 'email' => 'dosen@preview.com']]);
        }

        return app(KalenderAkademikController::class)->dosenIndex();
    })->name('preview.kalender-dosen');

    Route::get('/kalender-mahasiswa', function () {
        if (! auth()->check()) {
            Inertia::share('auth', ['user' => ['name' => 'Mahasiswa Preview', 'email' => 'mahasiswa@preview.com']]);
        }

        return app(KalenderAkademikController::class)->mahasiswaIndex();
    })->name('preview.kalender-mahasiswa');

    Route::post('/jadwal-request', [JadwalRequestController::class, 'store'])->name('jadwal-request.store');
    Route::put('/jadwal-request/{id}/status', [JadwalRequestController::class, 'updateStatus'])->name('jadwal-request.updateStatus');

    // Dosen can add their own non-bimbingan schedules directly
    Route::post('/kalender-dosen', [KalenderAkademikController::class, 'dosenStore'])->name('dosen.kalender.store');
});

// Include actor-based routes for isolated PBI development
require __DIR__.'/web/admin.php';
require __DIR__.'/web/dosen.php';
require __DIR__.'/web/mahasiswa.php';
require __DIR__.'/web/staff.php';
