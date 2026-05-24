<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'program_studi', 'fakultas', 'kuota_pembimbingan'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function progress()
    {
        return $this->hasOne(StudentProgress::class);
    }

    // Catatan: relasi dosen() dihapus karena tabel dosen telah di-drop
    // oleh migration simplify_dosen_management. Data dosen kini disimpan
    // langsung di tabel users (kolom program_studi, fakultas, kuota_pembimbingan).
    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Mendapatkan profil mahasiswa yang dimiliki oleh user ini.
     * (One-to-One)
     */
    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }
}
