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
        'tanggal',
        'waktu',
        'topik_bimbingan',
        'tipe',
        'status',
        'catatan',
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

    public function catatanKonsultasi()
    {
        return $this->hasOne(CatatanKonsultasi::class, 'jadwal_bimbingan_id');
    }
}
