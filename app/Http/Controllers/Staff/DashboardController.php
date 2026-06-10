<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Mahasiswa;
use App\Models\StudyPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'total_courses' => Course::count(),
            'avg_ipk' => Mahasiswa::avg('ipk') ?? 0,
        ];

        return Inertia::render('Staff/Dashboard', [
            'stats' => $stats,
            'role' => $user->getRoleNames()->first(),
        ]);
    }
}
