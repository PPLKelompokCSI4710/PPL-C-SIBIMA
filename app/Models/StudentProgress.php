<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $table = 'student_progress';

    protected $fillable = [
        'user_id',
        'total_sks',
        'ipk',
        'passed_courses',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
