<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::where('email', 'mahasiswa@sibima.test')->first();
        if ($u && ! $u->mahasiswa) {
            Mahasiswa::create([
                'user_id' => $u->id,
                'nim' => '123456789',
                'nama_lengkap' => $u->name,
                'program_studi' => 'Teknik Informatika',
                'angkatan' => '2023',
                'status_akademik' => 'aktif',
            ]);
        }
    }
}
