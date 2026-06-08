<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sksLulus = $this->progressSummary['sks_lulus'] ?? '-';
        $sksTotal = $this->progressSummary['sks_total'] ?? '-';
        $ipk = $this->progressSummary['ipk'] ?? '-';
        $semester = $this->progressSummary['semester'] ?? '-';

        if ($this->isDosenCc) {
            $subject = "📢 CC Reminder: Progres Akademik Mahasiswa {$this->mahasiswaName}";

            return (new MailMessage)
                ->subject($subject)
                ->greeting('Yth. Bapak/Ibu Dosen Pembimbing,')
                ->line("Pemberitahuan ini dikirimkan sebagai salinan (CC) karena mahasiswa bimbingan Anda, **{$this->mahasiswaName}**, tercatat belum melakukan bimbingan akademik selama **{$this->daysSinceLastBimbingan} hari**.")
                ->line('Berikut adalah ringkasan progres akademik mahasiswa yang bersangkutan:')
                ->line("• **Nama Mahasiswa:** {$this->mahasiswaName}")
                ->line("• **Semester:** {$semester}")
                ->line("• **IPK Saat Ini:** {$ipk}")
                ->line("• **SKS Lulus / Total:** {$sksLulus} / {$sksTotal} SKS")
                ->line('Kami menyarankan Bapak/Ibu untuk menghubungi mahasiswa bersangkutan guna memantau kendala atau hambatan yang mungkin sedang dihadapi dalam pengerjaan tugas akhir.')
                ->action('Buka Dashboard Dosen SIBIMA', url('/'))
                ->line('Terima kasih atas dedikasi Bapak/Ibu dalam membimbing mahasiswa.')
                ->salutation("Salam hormat,\nTim SIBIMA");
        }

        $subject = '📈 Pantau Progres Akademikmu: Waktunya Bimbingan!';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Halo, rekan mahasiswa!')
            ->line("Sudah **{$this->daysSinceLastBimbingan} hari** berlalu sejak sesi bimbingan terakhir Anda. Konsistensi bimbingan sangat krusial dalam menyukseskan perjalanan akademik Anda.")
            ->line('Berikut adalah ringkasan progres akademik Anda saat ini:')
            ->line("• **Semester:** {$semester}")
            ->line("• **IPK Saat Ini:** {$ipk}")
            ->line("• **SKS Lulus / Total:** {$sksLulus} / {$sksTotal} SKS")
            ->line('Ayo, selangkah lebih dekat dengan kelulusanmu! Jangan tunda lagi, segera jadwalkan sesi bimbingan baru dengan dosen pembimbing Anda untuk membahas kemajuan studi Anda.')
            ->action('Booking Bimbingan Baru', url('/'))
            ->line('Terima kasih, tetap semangat untuk meraih impian akademikmu!')
            ->salutation("Salam hangat,\nTim SIBIMA");
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
