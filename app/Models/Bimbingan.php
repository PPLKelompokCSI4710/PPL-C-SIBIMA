<?php

namespace App\Models;

use App\Services\BimbinganReminderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    use HasFactory;

    protected $table = 'bimbingans';

    protected $fillable = [
        'mahasiswa_id', 'dosen_id', 'waktu_mulai', 'waktu_selesai',
        'topik', 'lokasi', 'tipe_pertemuan', 'catatan_persiapan', 'status',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'catatan_persiapan' => 'array',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Bimbingan $bimbingan) {
            // Auto create/update reminders when schedule changes.
            app(BimbinganReminderService::class)->syncForBimbingan($bimbingan);
        });

        static::deleted(function (Bimbingan $bimbingan) {
            app(BimbinganReminderService::class)->cancelForBimbingan($bimbingan);
        });
    }
}
