<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_bimbingan_id',
        'ketersediaan_jadwal_lama_id',
        'ketersediaan_jadwal_baru_id',
        'status',
        'alasan',
    ];

    public function jadwalBimbingan()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_bimbingan_id');
    }

    public function ketersediaanJadwalLama()
    {
        return $this->belongsTo(KetersediaanJadwal::class, 'ketersediaan_jadwal_lama_id');
    }

    public function ketersediaanJadwalBaru()
    {
        return $this->belongsTo(KetersediaanJadwal::class, 'ketersediaan_jadwal_baru_id');
    }
}
