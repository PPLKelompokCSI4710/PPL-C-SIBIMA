<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BimbinganScheduleReminderNotification extends Notification
{
    public function __construct(
        private readonly array $payload,
        private readonly string $stage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $stageLabel = match ($this->stage) {
            'h3' => 'H-3',
            'h1' => 'H-1',
            'h2' => 'H-2 Jam',
            default => strtoupper($this->stage),
        };

        $waktu = Carbon::parse($this->payload['waktu_mulai'] ?? now())->translatedFormat('l, d F Y (H:i)');
        $topik = $this->payload['topik'] ?? '-';
        $tipe = ucfirst($this->payload['tipe_pertemuan'] ?? '-');
        $lokasi = $this->payload['lokasi'] ?? '-';

        $isMahasiswa = false;
        if (method_exists($notifiable, 'hasRole') && $notifiable->hasRole('mahasiswa')) {
            $isMahasiswa = true;
        }

        $subject = "⏰ Reminder Bimbingan {$stageLabel}: {$topik}";

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($isMahasiswa ? 'Halo, rekan mahasiswa!' : 'Yth. Bapak/Ibu Dosen Pembimbing,')
            ->line($isMahasiswa
                ? 'Kami ingin mengingatkan Anda bahwa jadwal bimbingan akademik Anda akan segera berlangsung.'
                : 'Berikut adalah pengingat bahwa Anda memiliki jadwal bimbingan akademik yang akan datang.')
            ->line('Berikut rincian jadwal bimbingan:')
            ->line("• **Topik:** {$topik}")
            ->line("• **Waktu:** {$waktu} WIB")
            ->line("• **Metode:** {$tipe}");

        if (strtolower($this->payload['tipe_pertemuan'] ?? '') === 'online') {
            $mailMessage->line("• **Link Pertemuan:** [Hubungkan ke Sesi]({$lokasi})");
        } else {
            $mailMessage->line("• **Lokasi:** {$lokasi}");
        }

        if ($isMahasiswa) {
            $mailMessage->line('Silakan persiapkan materi, draf dokumen, serta catatan pertanyaan Anda agar sesi bimbingan berjalan efektif.');
        } else {
            $mailMessage->line('Mohon luangkan waktu Anda untuk membimbing mahasiswa bersangkutan pada waktu yang telah disepakati.');
        }

        return $mailMessage
            ->action('Buka Dashboard SIBIMA', url('/'))
            ->line('Terima kasih atas dedikasi dan kerja sama Anda.')
            ->salutation("Salam hangat,\nTim SIBIMA");
    }

    public function toArray(object $notifiable): array
    {
        $stageLabel = match ($this->stage) {
            'h3' => 'H-3',
            'h1' => 'H-1',
            'h2' => 'H-2 Jam',
            default => strtoupper($this->stage),
        };

        return [
            'type' => 'bimbingan_schedule_reminder',
            'stage' => $this->stage,
            'title' => "Reminder Jadwal Bimbingan ({$stageLabel})",
            'message' => 'Jangan lupa jadwal bimbingan Anda.',
            'detail' => [
                'mahasiswa' => $this->payload['mahasiswa'] ?? null,
                'dosen' => $this->payload['dosen'] ?? null,
                'waktu_mulai' => $this->payload['waktu_mulai'] ?? null,
                'lokasi' => $this->payload['lokasi'] ?? null,
                'tipe_pertemuan' => $this->payload['tipe_pertemuan'] ?? null,
                'topik' => $this->payload['topik'] ?? null,
                'bimbingan_id' => $this->payload['bimbingan_id'] ?? null,
            ],
        ];
    }
}
