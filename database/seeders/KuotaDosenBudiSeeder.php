<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dosen;
use App\Models\KetersediaanJadwal;

class KuotaDosenBudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosenBudi = Dosen::where('nama_lengkap', 'like', '%Budi Santoso%')->first();

        if ($dosenBudi) {
            KetersediaanJadwal::create([
                'dosen_id' => $dosenBudi->id,
                'tanggal' => '2026-06-07',
                'waktu_mulai' => '09:00:00', // Sesuaikan waktu mulai
                'waktu_selesai' => '12:00:00', // Sesuaikan waktu selesai
                'kuota' => 3,
            ]);
            KetersediaanJadwal::create([
                'dosen_id' => $dosenBudi->id,
                'tanggal' => '2026-06-18',
                'waktu_mulai' => '09:00:00', // Sesuaikan waktu mulai
                'waktu_selesai' => '12:00:00', // Sesuaikan waktu selesai
                'kuota' => 3,
            ]);
            $this->command->info('Berhasil menambahkan 3 kuota untuk Pak Budi pada 7 Juni & 18 Juni 2026.');
        } else {
            $this->command->error('Data Dosen Pak Budi tidak ditemukan. Pastikan JadwalBimbinganSeeder atau dosen terkait sudah ada.');
        }
    }
}
