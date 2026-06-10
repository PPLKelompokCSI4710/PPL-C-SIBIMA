<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\KetersediaanJadwal;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JadwalBimbinganSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil atau buat user mahasiswa
        $mahasiswaUser = User::firstOrCreate(
            ['email' => 'mahasiswa@sibima.test'],
            [
                'name' => 'Mahasiswa Test',
                'password' => Hash::make('password'),
            ]
        );
        $mahasiswaUser->assignRole('mahasiswa');

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

        $mahasiswaProfiles = [$mahasiswaProfile];

        $newMahasiswas = [
            ['name' => 'Jack', 'email' => 'jack@sibima.test', 'nim' => '2021001002'],
            ['name' => 'Mulyono', 'email' => 'mulyono@sibima.test', 'nim' => '2021001003'],
            ['name' => 'Wowo', 'email' => 'wowo@sibima.test', 'nim' => '2021001004'],
        ];

        foreach ($newMahasiswas as $m) {
            $u = User::firstOrCreate(
                ['email' => $m['email']],
                ['name' => $m['name'], 'password' => Hash::make('password')]
            );
            $u->assignRole('mahasiswa');

            $p = Mahasiswa::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'nim' => $m['nim'],
                    'nama_lengkap' => $m['name'],
                    'program_studi' => 'Teknik Informatika',
                    'fakultas' => 'FMIPA',
                    'angkatan' => '2021',
                    'semester' => '7',
                    'status_akademik' => 'aktif',
                ]
            );
            $mahasiswaProfiles[] = $p;
        }

        // 2. Data beberapa Dosen
        $dosenList = [
            [
                'email' => 'dosen@sibima.test',
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'nidn' => '0012345678',
                'jabatan_fungsional' => 'Lektor Kepala',
                'gelar' => 'Dr., M.Kom.',
            ],
            [
                'email' => 'dosen2@sibima.test',
                'name' => 'Dr. Siti Aminah, M.T.',
                'nidn' => '0012345679',
                'jabatan_fungsional' => 'Lektor',
                'gelar' => 'Dr., M.T.',
            ],
            [
                'email' => 'dosen3@sibima.test',
                'name' => 'Prof. Dr. Antonius, M.Sc.',
                'nidn' => '0012345680',
                'jabatan_fungsional' => 'Guru Besar',
                'gelar' => 'Prof. Dr., M.Sc.',
            ],
        ];

        $dosenProfiles = [];

        foreach ($dosenList as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $user->assignRole('dosen');

            $profile = Dosen::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nidn' => $d['nidn'],
                    'nama_lengkap' => $d['name'],
                    'program_studi' => 'Teknik Informatika',
                    'fakultas' => 'FMIPA',
                    'jabatan_fungsional' => $d['jabatan_fungsional'],
                    'gelar' => $d['gelar'],
                    'no_telepon' => '08'.rand(1000000000, 9999999999),
                    'is_active' => true,
                    'kuota_mahasiswa' => 10,
                ]
            );
            $dosenProfiles[] = $profile;

            // Buat beberapa jadwal (schedules) untuk tiap dosen
            for ($i = 1; $i <= 5; $i++) {
                KetersediaanJadwal::create([
                    'dosen_id' => $profile->id,
                    'tanggal' => now()->addDays($i)->toDateString(),
                    'waktu_mulai' => '09:00:00',
                    'waktu_selesai' => '10:00:00',
                    'kuota' => 2, // Kuota awal
                ]);
                KetersediaanJadwal::create([
                    'dosen_id' => $profile->id,
                    'tanggal' => now()->addDays($i)->toDateString(),
                    'waktu_mulai' => '13:00:00',
                    'waktu_selesai' => '14:30:00',
                    'kuota' => 1,
                ]);
            }
        }

        // 3. Data dummy jadwal bimbingan (menggunakan dosen pertama)
        $dosenUtama = $dosenProfiles[0];
        // Ambil beberapa jadwal dosen utama
        $schedulesUtama = KetersediaanJadwal::where('dosen_id', $dosenUtama->id)->get();

        if ($schedulesUtama->count() >= 3) {
            $jadwalData = [
                [
                    'dosen_id' => $dosenUtama->id,
                    'mahasiswa_id' => $mahasiswaProfiles[1]->id, // Jack
                    'ketersediaan_jadwal_id' => $schedulesUtama[0]->id,
                    'judul_ta' => 'Implementasi AI pada Sistem Rekomendasi',
                    'topik_bimbingan' => 'Pengajuan Bab 1',
                    'tipe' => 'online',
                    'status' => 'pending',
                ],
                [
                    'dosen_id' => $dosenUtama->id,
                    'mahasiswa_id' => $mahasiswaProfiles[2]->id, // Mulyono
                    'ketersediaan_jadwal_id' => $schedulesUtama[1]->id,
                    'judul_ta' => 'Sistem Informasi Manajemen Skripsi',
                    'topik_bimbingan' => 'Diskusi Alur Sistem',
                    'tipe' => 'offline',
                    'status' => 'pending',
                ],
                [
                    'dosen_id' => $dosenUtama->id,
                    'mahasiswa_id' => $mahasiswaProfiles[3]->id, // Wowo
                    'ketersediaan_jadwal_id' => $schedulesUtama[2]->id,
                    'judul_ta' => 'Keamanan Jaringan Komputer',
                    'topik_bimbingan' => 'Review Latar Belakang Masalah',
                    'tipe' => 'online',
                    'status' => 'pending',
                ],
            ];

            foreach ($jadwalData as $jadwal) {
                JadwalBimbingan::create($jadwal);

                // Jika status bukan pending, kurangi kuota (karena approved/completed)
                if ($jadwal['status'] !== 'pending') {
                    $sched = KetersediaanJadwal::find($jadwal['ketersediaan_jadwal_id']);
                    $sched->decrement('kuota');
                }
            }
        }
    }
}
