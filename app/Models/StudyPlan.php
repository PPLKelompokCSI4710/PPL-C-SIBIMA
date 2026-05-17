<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'course_id',
        'semester',
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
