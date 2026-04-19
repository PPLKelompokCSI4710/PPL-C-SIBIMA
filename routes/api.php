<?php

use App\Http\Controllers\KalenderAkademikController;
use Illuminate\Support\Facades\Route;

Route::apiResource('kalender-akademik', KalenderAkademikController::class);
