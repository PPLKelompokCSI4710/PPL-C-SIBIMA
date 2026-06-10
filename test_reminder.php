<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'mahasiswa@sibima.test')->first();
$jadwal = App\Models\JadwalBimbingan::with(['dosen', 'ketersediaanJadwal'])->where('status', 'approved')->first();

if ($user && $jadwal) {
    $payload = [
        'jadwal_id' => $jadwal->id,
        'waktu_mulai' => $jadwal->ketersediaanJadwal->waktu_mulai,
        'tanggal' => $jadwal->ketersediaanJadwal->tanggal,
        'dosen_name' => $jadwal->dosen->nama_lengkap,
        'topik' => $jadwal->topik_bimbingan,
    ];

    $user->notify(new App\Notifications\BimbinganScheduleReminderNotification($payload, 'h1'));
    echo "Notification Sent!\n";
} else {
    echo "User or Jadwal not found.\n";
}
