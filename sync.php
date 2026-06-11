<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jadwals = App\Models\JadwalBimbingan::with('ketersediaanJadwal')->where('status', 'approved')->get();
foreach ($jadwals as $b) {
    if (!$b->ketersediaanJadwal) continue;
    $start = $b->ketersediaanJadwal->tanggal . ' ' . $b->ketersediaanJadwal->waktu_mulai;
    $end = $b->ketersediaanJadwal->tanggal . ' ' . $b->ketersediaanJadwal->waktu_selesai;
    
    $bim = App\Models\Bimbingan::updateOrCreate(
        [
            'mahasiswa_id' => $b->mahasiswa_id,
            'dosen_id' => $b->dosen_id,
            'waktu_mulai' => $start
        ],
        [
            'waktu_selesai' => $end,
            'topik' => $b->topik_bimbingan,
            'lokasi' => $b->lokasi ?? '-',
            'tipe_pertemuan' => $b->tipe ?? 'offline',
            'status' => 'disetujui'
        ]
    );
    // explicitly trigger the sync
    app(App\Services\BimbinganReminderService::class)->syncForBimbingan($bim);
}

echo "Done. Reminders count: " . App\Models\BimbinganReminder::count() . "\n";
