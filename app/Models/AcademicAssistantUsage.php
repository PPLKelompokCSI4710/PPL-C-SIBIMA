<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicAssistantUsage extends Model
{
    protected $table = 'academic_assistant_usages';

    protected $fillable = [
        'user_id',
        'date',
        'requests_count',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment or create usage record for the given user on a given date.
     */
    public static function incrementForUser(int $userId, string $date): self
    {
        $record = static::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        if (! $record) {
            $record = static::create([
                'user_id' => $userId,
                'date' => $date,
                'requests_count' => 0,
            ]);
        }

        $record->increment('requests_count');

        return $record->fresh();
    }

    /**
     * Get today's usage count for a given user.
     */
    public static function todayCountForUser(int $userId): int
    {
        return static::where('user_id', $userId)
            ->whereDate('date', today())
            ->value('requests_count') ?? 0;
    }
}
