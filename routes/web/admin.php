<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Di sinilah rute-rute khusus untuk PBI yang berkaitan dengan Admin (Actor: Admin).
| Jangan lupa gunakan prefix dan middleware jika diperlukan.
|
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // Contoh untuk PBI-XXX
});
