<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_homeroom_teacher',
    ];

    protected $casts = [
        'is_homeroom_teacher' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClass()
    {
        return $this->hasOne(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function managesClass(SchoolClass|int|null $class): bool
    {
        if (! $this->is_homeroom_teacher) {
            return false;
        }

        $classId = $class instanceof SchoolClass ? $class->id : $class;

        return $classId !== null
            && $this->homeroomClass()->where('id', $classId)->exists();
    }

    public function managesStudent(Student $student): bool
    {
        return $this->managesClass($student->class_id);
    }
}
