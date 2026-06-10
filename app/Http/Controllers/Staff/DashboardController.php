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

        $aiStats = null;
        if ($user->hasRole('admin')) {
            $dailyQuota = (int) \App\Models\AppSetting::get('ai_daily_quota', 20);
            $aiStats = [
                'today_total_requests' => (int) \App\Models\AcademicAssistantUsage::whereDate('date', today())->sum('requests_count'),
                'active_users_today' => (int) \App\Models\AcademicAssistantUsage::whereDate('date', today())->count(),
                'usage_data' => \App\Models\AcademicAssistantUsage::with('user')
                    ->whereDate('date', today())
                    ->orderByDesc('requests_count')
                    ->get()
                    ->map(function ($usage) use ($dailyQuota) {
                        return [
                            'name' => $usage->user?->name ?? '—',
                            'email' => $usage->user?->email ?? '—',
                            'requests_count' => $usage->requests_count,
                            'quota_used_pct' => $dailyQuota > 0
                                ? round(($usage->requests_count / $dailyQuota) * 100)
                                : 0,
                        ];
                    }),
            ];

            $aiConfig = [
                'ai_daily_quota' => $dailyQuota,
                'ai_system_prompt' => \App\Models\AppSetting::get('ai_system_prompt', "Anda adalah SIBIMA Academic Assistant..."),
            ];
        }

        return Inertia::render('Staff/Dashboard', [
            'stats' => $stats,
            'role' => $user->getRoleNames()->first(),
            'aiStats' => $aiStats,
            'aiConfig' => $aiConfig ?? null,
        ]);
    }

    public function updateAiConfig(Request $request)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'ai_daily_quota' => 'required|integer|min:1',
            'ai_system_prompt' => 'required|string',
        ]);

        \App\Models\AppSetting::set('ai_daily_quota', $request->ai_daily_quota);
        \App\Models\AppSetting::set('ai_system_prompt', $request->ai_system_prompt);

        return back()->with('success', 'Konfigurasi AI berhasil diperbarui.');
    }
}
