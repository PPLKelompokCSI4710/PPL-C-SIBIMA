<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AkademikStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    use SoftDeletes;

    /**
     * The table associated with the model.
     * Eksplisit karena nama tabel tidak mengikuti konvensi plural Laravel.
     */
    protected $table = 'mahasiswa';

    /**
     * Menggunakan $guarded = [] agar semua kolom dapat di-mass assign.
     * Form Filament sendiri sudah bertindak sebagai whitelist implisit.
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status_akademik' => AkademikStatus::class,
            'tanggal_lahir' => 'date',
            'ipk' => 'decimal:2',
            'status_kelulusan_bimbingan' => 'boolean',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Mendapatkan akun User yang terhubung dengan profil mahasiswa ini.
     * (Inverse of One-to-One)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
