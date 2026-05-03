<?php

namespace App\Filament\Resources\Mahasiswas\Pages;

use App\Filament\Resources\Mahasiswas\MahasiswaResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateMahasiswa extends CreateRecord
{
    protected static string $resource = MahasiswaResource::class;

    /**
     * Intercept form data sebelum record Mahasiswa dibuat.
     * Di sinilah kita membuat akun User baru secara otomatis
     * berdasarkan email yang diinput admin.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Buat akun User baru dengan password default dari NIM.
        $user = User::create([
            'name' => $data['nama_lengkap'],
            'email' => $data['email'],
            'password' => Hash::make($data['nim']),
        ]);

        // Assign role 'mahasiswa' via Spatie Permission.
        $user->assignRole('mahasiswa');

        // Inject user_id ke dalam data form agar tersimpan ke tabel mahasiswa.
        $data['user_id'] = $user->id;

        // Hapus key 'email' karena kolom ini tidak ada di tabel mahasiswa.
        unset($data['email']);

        return $data;
    }
}
