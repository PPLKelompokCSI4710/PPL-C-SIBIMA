<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Mahasiswa;
use App\Notifications\AcademicProgressNotification;
use App\Notifications\EskalasiNotification;

// 1. Uji Pengiriman Email ke Dosen (AcademicProgressNotification sebagai CC)
$dosenUser = User::role('dosen')->first();

if ($dosenUser) {
    echo "Mengirim email CC Reminder Progres ke Dosen ({$dosenUser->email})...\n";
    $progressSummary = [
        'sks_lulus' => 120,
        'sks_total' => 144,
        'ipk' => 3.85,
        'semester' => 7,
    ];
    // Parameter: daysSinceLastBimbingan, isDosenCc, mahasiswaName, progressSummary
    $notification = new AcademicProgressNotification(10, true, "Mahasiswa Dummy Testing", $progressSummary);
    $dosenUser->notify($notification);
    echo "✅ Email berhasil di-dispatch ke queue untuk Dosen!\n\n";
} else {
    echo "⚠️ Tidak ada user Dosen ditemukan di database.\n\n";
}

// Tambahkan jeda 3 detik agar tidak terkena limitasi Mailtrap (Too many emails per second)
echo "Menunggu 3 detik agar tidak terkena limit Mailtrap...\n\n";
sleep(3);

// 2. Uji Pengiriman Email ke Admin (EskalasiNotification)
$adminUser = User::role('admin')->first();

if ($adminUser) {
    echo "Mengirim email Eskalasi ke Admin ({$adminUser->email})...\n";
    $progressSummaryAdmin = [
        'sks_lulus' => 80,
        'sks_total' => 144,
        'ipk' => 2.50,
        'semester' => 8,
    ];
    // Parameter: progressSummary, mahasiswaName, jumlahSesiBimbinganSelesai, terakhirBimbinganPada
    $eskalasiNotification = new EskalasiNotification($progressSummaryAdmin, "Mahasiswa Dummy Bermasalah", 2, "2026-05-10 10:00:00");
    $adminUser->notify($eskalasiNotification);
    echo "✅ Email berhasil di-dispatch ke queue untuk Admin!\n\n";
} else {
    echo "⚠️ Tidak ada user Admin ditemukan di database.\n\n";
}

echo "🎉 Selesai! Silakan cek Mailtrap Anda.\n";
