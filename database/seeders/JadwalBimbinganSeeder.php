<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JadwalBimbinganSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil atau buat user dosen
        $dosenUser = User::firstOrCreate(
            ['email' => 'dosen@sibima.test'],
            [
                'name' => 'Dosen Pembimbing',
                'password' => Hash::make('password'),
            ]
        );
        $dosenUser->assignRole('dosen');

        // Buat profil dosen
        $dosenProfile = Dosen::firstOrCreate(
            ['user_id' => $dosenUser->id],
            [
                'nidn' => '0012345678',
                'nama_lengkap' => 'Dr. Budi Santoso, M.Kom.',
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'FMIPA',
                'jabatan_fungsional' => 'Lektor Kepala',
                'gelar' => 'Dr., M.Kom.',
                'no_telepon' => '081234567890',
                'is_active' => true,
                'kuota_mahasiswa' => 10,
            ]
        );

        // Ambil atau buat user mahasiswa
        $mahasiswaUser = User::firstOrCreate(
            ['email' => 'mahasiswa@sibima.test'],
            [
                'name' => 'Mahasiswa Test',
                'password' => Hash::make('password'),
            ]
        );
        $mahasiswaUser->assignRole('mahasiswa');

        // Buat profil mahasiswa
        $mahasiswaProfile = Mahasiswa::firstOrCreate(
            ['user_id' => $mahasiswaUser->id],
            [
                'nim' => '2021001001',
                'nama_lengkap' => 'Ahmad Rizky Pratama',
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'FMIPA',
                'angkatan' => '2021',
                'semester' => '7',
                'ipk' => 3.75,
                'sks_lulus' => 120,
                'sks_total' => 144,
                'status_akademik' => 'aktif',
            ]
        );

        // Data dummy jadwal bimbingan
        $jadwalData = [
            [
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->addDays(3)->toDateString(),
                'waktu' => '09:00:00',
                'topik_bimbingan' => 'Konsultasi Bab 1 - Pendahuluan & Latar Belakang',
                'tipe' => 'offline',
                'status' => 'pending',
            ],
            [
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->addDays(5)->toDateString(),
                'waktu' => '13:00:00',
                'topik_bimbingan' => 'Review Bab 2 - Tinjauan Pustaka & Landasan Teori',
                'tipe' => 'online',
                'status' => 'pending',
            ],
            [
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->subDays(2)->toDateString(),
                'waktu' => '10:00:00',
                'topik_bimbingan' => 'Diskusi Metodologi Penelitian - Bab 3',
                'tipe' => 'offline',
                'status' => 'completed',
            ],
            [
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->subDays(4)->toDateString(),
                'waktu' => '14:30:00',
                'topik_bimbingan' => 'Bimbingan Proposal Skripsi Awal',
                'tipe' => 'online',
                'status' => 'rejected',
            ],
            [
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->addDays(7)->toDateString(),
                'waktu' => '11:00:00',
                'topik_bimbingan' => 'Evaluasi Progress Analisis Data - Bab 4',
                'tipe' => 'offline',
                'status' => 'approved',
            ],
            [
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->addDays(14)->toDateString(),
                'waktu' => '15:00:00',
                'topik_bimbingan' => 'Konsultasi Persiapan Sidang Akhir',
                'tipe' => 'online',
                'status' => 'canceled',
            ],
        ];

        foreach ($jadwalData as $jadwal) {
            JadwalBimbingan::create($jadwal);
        }
    }
}
