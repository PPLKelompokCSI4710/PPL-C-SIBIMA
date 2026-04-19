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

        $dosen = User::firstOrCreate(
            ['email' => 'dosen@sibima.test'],
            [
                'name' => 'Dosen Pembimbing',
                'password' => Hash::make('password'),
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'Fakultas Teknik',
                'kuota_pembimbingan' => 10,
            ]
        );
        $dosen->assignRole('dosen');

        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@sibima.test'],
            [
                'name' => 'Mahasiswa Test',
                'password' => Hash::make('password'),
            ]
        );
        $mahasiswa->assignRole('mahasiswa');
    }
}
