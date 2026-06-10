<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\BimbinganScheduleReminderNotification;

$mahasiswaUser = User::role('mahasiswa')->first();
$dosenUser = User::role('dosen')->first();

$payload = [
    'mahasiswa' => $mahasiswaUser->name ?? 'Mahasiswa Test',
    'dosen' => $dosenUser->name ?? 'Dosen Test',
    'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
    'lokasi' => 'Ruang Dosen Gedung A / Google Meet',
    'tipe_pertemuan' => 'offline',
    'topik' => 'Pembahasan Bab 3 Metodologi Penelitian',
    'bimbingan_id' => 1,
];

if ($dosenUser) {
    echo "Mengirim email Reminder Jadwal ke Dosen ({$dosenUser->email})...\n";
    $dosenUser->notify(new BimbinganScheduleReminderNotification($payload, 'h1'));
    echo "✅ Email berhasil dikirim ke Dosen!\n\n";
} else {
    echo "⚠️ Tidak ada user Dosen ditemukan.\n";
}
