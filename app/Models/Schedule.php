<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'dosen_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'kuota',
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalBimbingans(): HasMany
    {
        return $this->hasMany(JadwalBimbingan::class);
    }
}
