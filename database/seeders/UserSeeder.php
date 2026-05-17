<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@sibima.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $dosenBase = User::firstOrCreate(
            ['email' => 'dosen@sibima.test'],
            [
                'name' => 'Dosen Pembimbing',
                'password' => Hash::make('password'),
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'Fakultas Teknik',
                'kuota_pembimbingan' => 10,
            ]
        );
        $dosenBase->assignRole('dosen');

        $daftarDosen = [
            ['name' => 'Dr. Ir. Rahmat Hidayat, M.T.', 'email' => 'rahmat@sibima.test'],
            ['name' => 'Siti Aminah, S.Kom., M.Cs.', 'email' => 'siti@sibima.test'],
            ['name' => 'Prof. Dr. Ahmad Subagyo',   'email' => 'ahmad@sibima.test'],
            ['name' => 'Budi Setiawan, S.T., M.T.', 'email' => 'budi@sibima.test'],
            ['name' => 'Dr. Linda Kusuma, M.Kom.',  'email' => 'linda@sibima.test'],
        ];

        foreach ($daftarDosen as $d) {
            $u = User::firstOrCreate(
                ['email' => $d['email']],
                ['name' => $d['name'], 'password' => Hash::make('password')]
            );
            $u->assignRole('dosen');
        }

        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@sibima.test'],
            [
                'name' => 'Mahasiswa Test',
                'password' => Hash::make('password'),
            ]
        );
        $mahasiswa->assignRole('mahasiswa');

        // Mahasiswa kedua – bimbingan dengan Rahmat
        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'mahasiswa2@sibima.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
            ]
        );
        $mahasiswa2->assignRole('mahasiswa');
    }
}
