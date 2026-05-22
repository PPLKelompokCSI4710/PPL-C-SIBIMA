<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $guarded = [];
=======
use App\Enums\AkademikStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id', 'nim', 'nama_lengkap', 'program_studi', 'fakultas',
        'angkatan', 'semester', 'ipk', 'sks_lulus', 'sks_total',
        'status_akademik', 'no_telepon', 'foto', 'tanggal_lahir',
        'alamat', 'status_kelulusan_bimbingan', 'progress_reminder_frequency_days',
        'progress_reminder_frequency', 'progress_reminder_enabled', 'last_progress_reminder_sent_at',
        'consecutive_progress_reminders',
    ];

    protected function casts(): array
    {
        return [
            'status_akademik' => AkademikStatus::class,
            'tanggal_lahir' => 'date',
            'ipk' => 'decimal:2',
            'status_kelulusan_bimbingan' => 'boolean',
            'last_progress_reminder_sent_at' => 'datetime',
            'progress_reminder_enabled' => 'boolean',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function studentProgress(): HasOne
    {
        return $this->hasOne(StudentProgress::class, 'user_id', 'user_id');
    }

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    protected static function booted(): void
    {
        // Ketika mahasiswa dihapus (soft-delete), hapus juga akun User-nya
        // agar email tidak menghalangi import ulang di masa mendatang.
        static::deleting(function (Mahasiswa $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->delete();
            }
        });
    }
>>>>>>> main
}
