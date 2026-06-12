<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderPreference extends Model
{
    use HasFactory;

    protected $table = 'reminder_preferences';

    protected $fillable = [
        'user_id',
        'schedule_reminder_enabled',
        'stage_h3_enabled',
        'stage_h1_enabled',
        'stage_h3jam_enabled',
        'stage_h2_enabled',
    ];

    protected $casts = [
        'schedule_reminder_enabled' => 'boolean',
        'stage_h3_enabled' => 'boolean',
        'stage_h1_enabled' => 'boolean',
        'stage_h3jam_enabled' => 'boolean',
        'stage_h2_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'schedule_reminder_enabled' => true,
                'stage_h3_enabled' => true,
                'stage_h1_enabled' => true,
                'stage_h3jam_enabled' => true,
                'stage_h2_enabled' => true,
            ],
        );
    }
}
