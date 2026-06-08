<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicAssistantMessage extends Model
{
    protected $fillable = ['session_id', 'role', 'content'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicAssistantSession::class, 'session_id');
    }
}
