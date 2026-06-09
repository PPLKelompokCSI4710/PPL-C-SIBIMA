<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetersediaanJadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'dosen_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'kuota',
        'tipe',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function jadwalRequests()
    {
        return $this->hasMany(JadwalRequest::class, 'ketersediaan_jadwal_id');
    }

    public function jadwalBimbingans()
    {
        return $this->hasMany(JadwalBimbingan::class, 'ketersediaan_jadwal_id');
    }
}
