<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BimbinganScheduleReminderNotification extends Notification
{
    public function __construct(
        private readonly array $payload,
        private readonly string $stage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
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
