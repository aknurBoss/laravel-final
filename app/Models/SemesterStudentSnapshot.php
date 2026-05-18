<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterStudentSnapshot extends Model
{
    protected $fillable = [
        'semester_id',
        'student_id',
        'class_id',
        'student_name',
        'class_name',
        'final_points',
        'global_rank',
        'class_rank',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
