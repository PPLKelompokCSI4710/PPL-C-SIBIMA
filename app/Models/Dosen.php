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
        'user_id', 'nidn', 'nama_lengkap', 'program_studi', 'fakultas',
        'jabatan_akademik', 'keahlian', 'foto', 'no_telepon'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingan::class);
    }

    public function mahasiswas()
    {
        return $this->belongsToMany(Mahasiswa::class, 'dosen_mahasiswa')->withPivot(['tanggal_penugasan', 'tanggal_berakhir', 'is_active', 'catatan']);
    }
}
