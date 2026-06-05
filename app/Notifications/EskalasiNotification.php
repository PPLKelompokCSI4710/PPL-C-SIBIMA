<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
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
        $terakhir = $this->terakhirBimbinganPada
            ? Carbon::parse($this->terakhirBimbinganPada)->translatedFormat('l, d F Y (H:i)').' WIB'
            : 'Belum pernah melakukan bimbingan';

        $subject = "⚠️ Eskalasi Bimbingan: Mahasiswa {$this->mahasiswaName} Membutuhkan Tindakan";

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Yth. Koordinator / Administrator SIBIMA,')
            ->line("Sistem mendeteksi bahwa mahasiswa **{$this->mahasiswaName}** telah berulang kali menerima reminder progres akademik tetapi belum menjadwalkan bimbingan baru.")
            ->line('Berikut adalah rangkuman riwayat bimbingan dan progres akademik mahasiswa:')
            ->line("• **Nama Mahasiswa:** {$this->mahasiswaName}")
            ->line("• **Jumlah Sesi Selesai:** {$this->jumlahSesiBimbinganSelesai} Sesi")
            ->line("• **Terakhir Bimbingan:** {$terakhir}")
            ->line("• **Semester:** {$semester}")
            ->line("• **IPK Saat Ini:** {$ipk}")
            ->line("• **SKS Lulus / Total:** {$sksLulus} / {$sksTotal} SKS")
            ->line('Silakan tinjau status akademik mahasiswa yang bersangkutan melalui dashboard monitoring eskalasi di SIBIMA untuk menentukan langkah pembinaan selanjutnya.')
            ->action('Buka Halaman Monitoring Eskalasi', url('/admin/eskalasi'))
            ->line('Terima kasih atas perhatian dan kerja sama Anda.')
            ->salutation("Salam profesional,\nTim SIBIMA");
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
