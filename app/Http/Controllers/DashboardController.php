<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the dashboard request and redirect based on role.
     */
    public function __invoke()
    {
        $user = Auth::user();
        if ($user->hasRole('mahasiswa')) {
            return redirect()->route('mahasiswa.dashboard');
        }
        if ($user->hasAnyRole(['admin', 'dosen', 'staff'])) {
            return redirect()->route('staff.dashboard');
        }

        return Inertia::render('Dashboard');
    }
}
