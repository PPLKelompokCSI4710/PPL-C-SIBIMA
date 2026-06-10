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

    public string $systemPrompt = '';

    public string $filterDate = '';

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        $this->dailyQuota = (int) AppSetting::get('ai_daily_quota', 20);
        
        $defaultPrompt = "Anda adalah SIBIMA Academic Assistant, kecerdasan buatan yang dirancang khusus untuk membantu mahasiswa dalam konteks pendidikan, penyusunan Skripsi/Tugas Akhir, dan bimbingan akademik.\n\n"
            ."ATURAN MUTLAK DAN TIDAK BOLEH DILANGGAR:\n"
            ."1. Anda HANYA diizinkan merespons pertanyaan yang secara spesifik berkaitan dengan konteks PENDIDIKAN, SKRIPSI, atau BIMBINGAN AKADEMIK.\n"
            ."2. Jika pengguna menanyakan APAPUN di luar topik pendidikan, skripsi, atau bimbingan (misalnya: membuat lelucon, resep makanan, menulis kode untuk proyek non-akademik, membuat puisi, berita umum, dll.), Anda WAJIB menolak untuk menjawab.\n"
            ."3. Untuk pertanyaan di luar konteks, berikan respons baku berikut (atau variasi sopan serupa): \"Maaf, saya hanya dapat membantu Anda dalam konteks pendidikan, penyusunan skripsi, dan bimbingan akademik. Silakan ajukan pertanyaan seputar topik tersebut.\"\n"
            ."4. Berikan jawaban dalam Bahasa Indonesia secara terstruktur, ilmiah, solutif, santun, dan memotivasi mahasiswa saat menjawab pertanyaan yang valid.";
            
        $this->systemPrompt = AppSetting::get('ai_system_prompt', $defaultPrompt);
        
        $this->filterDate = today()->toDateString();
    }

    // =========================================================================
    // COMPUTED STATS
    // =========================================================================

    /** Total requests consumed today */
    public function getTodayTotalRequests(): int
    {
        return AcademicAssistantUsage::whereDate('date', today())
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
        return AcademicAssistantUsage::whereDate('date', today())
            ->count();
    }

    /** Usage data for the selected date, grouped by user */
    public function getUsageTableData(): \Illuminate\Support\Collection
    {
        $date = $this->filterDate ?: today()->toDateString();

        return AcademicAssistantUsage::with('user')
            ->whereDate('date', $date)
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

    /** Save settings to AppSetting */
    public function saveSettings(): void
    {
        $this->validate([
            'dailyQuota' => 'required|integer|min:1|max:500',
            'systemPrompt' => 'required|string|min:10',
        ]);

        AppSetting::set('ai_daily_quota', $this->dailyQuota);
        AppSetting::set('ai_system_prompt', $this->systemPrompt);

        Notification::make()
            ->title('Konfigurasi berhasil disimpan!')
            ->success()
            ->send();
    }
}
