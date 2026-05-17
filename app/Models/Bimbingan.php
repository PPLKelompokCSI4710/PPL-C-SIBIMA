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
            if ($bimbingan->wasRecentlyCreated || $bimbingan->wasChanged([
                'waktu_mulai', 'waktu_selesai', 'status', 'lokasi', 'topik',
                'tipe_pertemuan', 'mahasiswa_id', 'dosen_id', 'catatan_persiapan',
            ])) {
                app(BimbinganReminderService::class)->syncForBimbingan($bimbingan);
            }

            // AC 34.4: Auto close escalation when new booking is created/approved.
            if ($bimbingan->wasRecentlyCreated && in_array($bimbingan->status, ['menunggu', 'disetujui'], true)) {
                if ($bimbingan->mahasiswa) {
                    $bimbingan->mahasiswa->forceFill([
                        'consecutive_progress_reminders' => 0,
                    ])->save();
                }

                Eskalasi::where('mahasiswa_id', $bimbingan->mahasiswa_id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                    ]);
            }
        });

        static::deleted(function (Bimbingan $bimbingan) {
            app(BimbinganReminderService::class)->cancelForBimbingan($bimbingan);
        });
    }
}
