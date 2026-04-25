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
        'user_id', 'nim', 'nama_lengkap', 'program_studi', 'fakultas',
        'angkatan', 'semester', 'ipk', 'sks_lulus', 'sks_total',
        'status_akademik', 'no_telepon', 'foto', 'tanggal_lahir',
        'alamat', 'status_kelulusan_bimbingan', 'progress_reminder_frequency_days',
        'progress_reminder_enabled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingan::class);
    }

    public function dosens()
    {
        return $this->belongsToMany(Dosen::class, 'dosen_mahasiswa')->withPivot(['tanggal_penugasan', 'tanggal_berakhir', 'is_active', 'catatan']);
    }
}
