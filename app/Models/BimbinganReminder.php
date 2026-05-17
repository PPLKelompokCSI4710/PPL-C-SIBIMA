<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BimbinganReminder extends Model
{
    use HasFactory;

    protected $table = 'bimbingan_reminders';

    protected $fillable = [
        'bimbingan_id',
        'user_id',
        'stage',
        'send_at',
        'status',
        'sent_at',
        'canceled_at',
        'payload',
    ];

    protected $casts = [
        'send_at' => 'datetime',
        'sent_at' => 'datetime',
        'canceled_at' => 'datetime',
        'payload' => 'array',
    ];

    public function bimbingan()
    {
        return $this->belongsTo(Bimbingan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
