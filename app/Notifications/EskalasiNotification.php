<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class EskalasiNotification extends Notification
{
    public function __construct(
        public readonly array $progressSummary,
        public readonly string $mahasiswaName,
        public readonly int $jumlahSesiBimbinganSelesai = 0,
        public readonly ?string $terakhirBimbinganPada = null,
    ) {}

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
        return [
            'type' => 'eskalasi_progress',
            'title' => 'Eskalasi Bimbingan: '.$this->mahasiswaName,
            'message' => 'Mahasiswa tidak merespons reminder progres bimbingan secara berturut-turut.',
            'detail' => [
                'mahasiswa' => $this->mahasiswaName,
                'jumlah_sesi_bimbingan_selesai' => $this->jumlahSesiBimbinganSelesai,
                'terakhir_bimbingan_pada' => $this->terakhirBimbinganPada,
                ...$this->progressSummary,
            ],
        ];
    }
}
