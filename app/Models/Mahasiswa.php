<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_lengkap',
        'program_studi',
        'fakultas',
        'angkatan',
        'semester',
        'ipk',
        'sks_lulus',
        'sks_total',
        'status_akademik',
        'no_telepon',
        'foto',
        'tanggal_lahir',
        'alamat',
        'status_kelulusan_bimbingan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_kelulusan_bimbingan' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
