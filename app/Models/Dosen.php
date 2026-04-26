<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dosen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dosen';

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

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
