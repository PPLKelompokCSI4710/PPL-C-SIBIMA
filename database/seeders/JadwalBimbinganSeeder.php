<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class JadwalBimbinganSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data jadwal lama agar tidak duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JadwalBimbingan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil atau buat user dosen
        $dosenUser = User::firstOrCreate(
            ['email' => 'dosen@sibima.test'],
            [
                'name' => 'Dosen Pembimbing',
                'password' => Hash::make('password'),
            ]
        );
        $dosenUser->assignRole('dosen');

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

        // Data mahasiswa dengan NIM unik yang tidak konflik
        $mahasiswaData = [
            [
                'email' => 'andi@sibima.test',
                'name' => 'Andi Firmansyah',
                'nim' => '2021010001',
                'nama_lengkap' => 'Andi Firmansyah',
                'angkatan' => '2021',
                'semester' => '7',
                'ipk' => 3.75,
            ],
            [
                'email' => 'siti@sibima.test',
                'name' => 'Siti Rahayu',
                'nim' => '2021010002',
                'nama_lengkap' => 'Siti Rahayu',
                'angkatan' => '2021',
                'semester' => '7',
                'ipk' => 3.60,
            ],
            [
                'email' => 'rizky@sibima.test',
                'name' => 'Muhammad Rizky',
                'nim' => '2022010003',
                'nama_lengkap' => 'Muhammad Rizky',
                'angkatan' => '2022',
                'semester' => '5',
                'ipk' => 3.45,
            ],
        ];

        $mahasiswaProfiles = [];

        foreach ($mahasiswaData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password')]
            );
            $user->assignRole('mahasiswa');

            // Lookup by NIM agar tidak duplikat
            $profile = Mahasiswa::firstOrCreate(
                ['nim' => $data['nim']],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $data['nama_lengkap'],
                    'program_studi' => 'Teknik Informatika',
                    'fakultas' => 'FMIPA',
                    'angkatan' => $data['angkatan'],
                    'semester' => $data['semester'],
                    'ipk' => $data['ipk'],
                    'sks_lulus' => 100,
                    'sks_total' => 144,
                    'status_akademik' => 'aktif',
                ]
            );

            DB::table('dosen_mahasiswa')->updateOrInsert(
                ['dosen_id' => $dosenProfile->id, 'mahasiswa_id' => $profile->id],
                [
                    'tanggal_penugasan' => now()->toDateString(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $mahasiswaProfiles[] = $profile;
        }

        [$andi, $siti, $rizky] = $mahasiswaProfiles;

        // Data dummy jadwal bimbingan yang beragam
        $jadwalData = [
            [
                'mahasiswa_id' => $andi->id,
                'tanggal' => now()->addDays(2)->toDateString(),
                'waktu' => '09:00:00',
                'topik_bimbingan' => 'Konsultasi Bab 1 - Pendahuluan & Latar Belakang',
                'tipe' => 'offline',
                'status' => 'pending',
            ],
            [
                'mahasiswa_id' => $siti->id,
                'tanggal' => now()->addDays(4)->toDateString(),
                'waktu' => '13:00:00',
                'topik_bimbingan' => 'Review Tinjauan Pustaka & Landasan Teori',
                'tipe' => 'online',
                'status' => 'pending',
            ],
            [
                'mahasiswa_id' => $rizky->id,
                'tanggal' => now()->subDays(3)->toDateString(),
                'waktu' => '10:00:00',
                'topik_bimbingan' => 'Diskusi Metodologi Penelitian - Bab 3',
                'tipe' => 'offline',
                'status' => 'approved',
            ],
            [
                'mahasiswa_id' => $andi->id,
                'tanggal' => now()->subDays(7)->toDateString(),
                'waktu' => '14:30:00',
                'topik_bimbingan' => 'Bimbingan Proposal Skripsi Awal',
                'tipe' => 'online',
                'status' => 'rejected',
            ],
            [
                'mahasiswa_id' => $siti->id,
                'tanggal' => now()->subDays(10)->toDateString(),
                'waktu' => '11:00:00',
                'topik_bimbingan' => 'Evaluasi Progress Analisis Data - Bab 4',
                'tipe' => 'offline',
                'status' => 'approved',
            ],
        ];

        foreach ($jadwalData as $jadwal) {
            JadwalBimbingan::create(array_merge($jadwal, [
                'dosen_id' => $dosenProfile->id,
            ]));
        }
    }
}
