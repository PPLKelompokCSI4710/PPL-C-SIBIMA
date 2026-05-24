<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DosenController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('dosen', DosenController::class);
});
