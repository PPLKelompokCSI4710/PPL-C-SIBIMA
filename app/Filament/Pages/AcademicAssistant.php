<?php

namespace App\Filament\Pages;

use App\Models\AcademicAssistantUsage;
use App\Models\AppSetting;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AcademicAssistant extends Page
{
    protected string $view = 'filament.pages.academic-assistant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'AI Monitoring';

    protected static ?string $title = 'AI Academic Assistant — Monitoring';

    protected static ?string $slug = 'academic-assistant';

    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return 'Bantuan Akademik';
    }

    // =========================================================================
    // LIVEWIRE PROPERTIES
    // =========================================================================

    public int $dailyQuota = 20;

    public string $filterDate = '';

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        $this->dailyQuota = (int) AppSetting::get('ai_daily_quota', 20);
        $this->filterDate = today()->toDateString();
    }

    // =========================================================================
    // COMPUTED STATS
    // =========================================================================

    /** Total requests consumed today */
    public function getTodayTotalRequests(): int
    {
        return AcademicAssistantUsage::where('date', today()->toDateString())
            ->sum('requests_count');
    }

    /** Total requests ever consumed (all time) */
    public function getAllTimeRequests(): int
    {
        return AcademicAssistantUsage::sum('requests_count');
    }

    /** Number of unique users who used AI today */
    public function getActiveUsersToday(): int
    {
        return AcademicAssistantUsage::where('date', today()->toDateString())
            ->count();
    }

    /** Usage data for the selected date, grouped by user */
    public function getUsageTableData(): \Illuminate\Support\Collection
    {
        $date = $this->filterDate ?: today()->toDateString();

        return AcademicAssistantUsage::with('user')
            ->where('date', $date)
            ->orderByDesc('requests_count')
            ->get()
            ->map(function ($usage) {
                return [
                    'user_id' => $usage->user_id,
                    'name' => $usage->user?->name ?? '—',
                    'email' => $usage->user?->email ?? '—',
                    'requests_count' => $usage->requests_count,
                    'date' => $usage->date->format('d M Y'),
                    'quota_used_pct' => $this->dailyQuota > 0
                        ? round(($usage->requests_count / $this->dailyQuota) * 100)
                        : 0,
                ];
            });
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /** Save the daily quota to AppSetting */
    public function saveQuota(): void
    {
        $this->validate([
            'dailyQuota' => 'required|integer|min:1|max:500',
        ]);

        AppSetting::set('ai_daily_quota', $this->dailyQuota);

        Notification::make()
            ->title('Kuota harian berhasil disimpan!')
            ->success()
            ->send();
    }
}
