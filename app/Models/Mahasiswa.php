<?php

namespace App\Models;

use App\Enums\AkademikStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mahasiswa extends Model
{
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
