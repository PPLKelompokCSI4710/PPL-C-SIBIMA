<?php

namespace App\Filament\Widgets;

use App\Models\AcademicAssistantUsage;
use App\Models\AppSetting;
use Filament\Widgets\Widget;

class AiAssistantUsageWidget extends Widget
{
    protected string $view = 'filament.widgets.ai-assistant-usage-widget';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Only visible to admin/staff users (with direct fallback for seeded admin)
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->email === 'admin@sibima.test' ||
               $user->hasRole('admin') ||
               $user->hasRole('staff') ||
               (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'staff']));
    }

    public function getTodayTotalRequests(): int
    {
        return AcademicAssistantUsage::whereDate('date', today())
            ->sum('requests_count');
    }

    public function getActiveUsersToday(): int
    {
        return AcademicAssistantUsage::whereDate('date', today())
            ->count();
    }

    public function getUsageData()
    {
        $dailyQuota = (int) AppSetting::get('ai_daily_quota', 20);
        return AcademicAssistantUsage::with('user')
            ->whereDate('date', today())
            ->orderByDesc('requests_count')
            ->take(5) // show top 5 active students today on dashboard
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
            });
    }
}
