<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AcademicProgressNotification extends Notification
{
    private $daysSinceLastBimbingan;

    private $isDosenCc;

    private $mahasiswaName;

    private $progressSummary;

    /**
     * Create a new notification instance.
     */
    public function __construct($daysSinceLastBimbingan, $isDosenCc = false, $mahasiswaName = '', $progressSummary = [])
    {
        $this->daysSinceLastBimbingan = $daysSinceLastBimbingan;
        $this->isDosenCc = $isDosenCc;
        $this->mahasiswaName = $mahasiswaName;
        $this->progressSummary = $progressSummary;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $summary = [
            'sks_lulus' => $this->progressSummary['sks_lulus'] ?? null,
            'sks_total' => $this->progressSummary['sks_total'] ?? null,
            'ipk' => $this->progressSummary['ipk'] ?? null,
            'semester' => $this->progressSummary['semester'] ?? null,
        ];

        if ($this->isDosenCc) {
            return [
                'type' => 'academic_progress_reminder_cc',
                'title' => 'Reminder Progres Mahasiswa Bimbingan',
                'message' => "Mahasiswa {$this->mahasiswaName} belum melakukan bimbingan selama {$this->daysSinceLastBimbingan} hari.",
                'days_since_last_bimbingan' => $this->daysSinceLastBimbingan,
                'progress_summary' => $summary,
            ];
        }

        return [
            'type' => 'academic_progress_reminder',
            'title' => 'Reminder Progres Akademik',
            'message' => "Anda belum melakukan bimbingan selama {$this->daysSinceLastBimbingan} hari. Segera jadwalkan bimbingan untuk melaporkan progres Anda.",
            'days_since_last_bimbingan' => $this->daysSinceLastBimbingan,
            'progress_summary' => $summary,
        ];
    }
}
