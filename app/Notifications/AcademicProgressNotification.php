<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcademicProgressNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $daysSinceLastBimbingan;
    private $isDosenCc;
    private $mahasiswaName;

    /**
     * Create a new notification instance.
     */
    public function __construct($daysSinceLastBimbingan, $isDosenCc = false, $mahasiswaName = '')
    {
        $this->daysSinceLastBimbingan = $daysSinceLastBimbingan;
        $this->isDosenCc = $isDosenCc;
        $this->mahasiswaName = $mahasiswaName;
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
        if ($this->isDosenCc) {
            return [
                'type' => 'academic_progress_reminder_cc',
                'title' => 'Reminder Progres Mahasiswa Bimbingan',
                'message' => "Mahasiswa {$this->mahasiswaName} belum melakukan bimbingan selama {$this->daysSinceLastBimbingan} hari.",
                'days_since_last_bimbingan' => $this->daysSinceLastBimbingan,
            ];
        }

        return [
            'type' => 'academic_progress_reminder',
            'title' => 'Reminder Progres Akademik',
            'message' => "Anda belum melakukan bimbingan selama {$this->daysSinceLastBimbingan} hari. Segera jadwalkan bimbingan untuk melaporkan progres Anda.",
            'days_since_last_bimbingan' => $this->daysSinceLastBimbingan,
        ];
    }
}
