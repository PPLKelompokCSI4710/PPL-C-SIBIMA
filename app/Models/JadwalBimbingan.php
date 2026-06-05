<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalBimbingan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_bimbingans';

    protected $fillable = [
        'dosen_id',
        'mahasiswa_id',
        'ketersediaan_jadwal_id',
        'judul_ta',
        'topik_bimbingan',
        'tipe',
        'lokasi',
        'status',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function ketersediaanJadwal()
    {
        return $this->belongsTo(KetersediaanJadwal::class, 'ketersediaan_jadwal_id');
    }
}
