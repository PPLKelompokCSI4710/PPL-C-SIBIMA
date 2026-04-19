<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dosen extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database.
     * Secara bawaan Laravel akan mencari tabel 'dosens',
     * jadi kita setel ke 'dosen' karena migrasi menggunakan nama 'dosen'.
     */
    protected $table = 'dosen';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nidn',
        'nama_lengkap',
        'program_studi',
        'fakultas',
        'jabatan_fungsional',
        'gelar',
        'no_telepon',
        'foto',
        'is_active',
        'kuota_mahasiswa',
        'bio',
    ];

    /**
     * Mendapatkan user yang terkait dengan dosen ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
