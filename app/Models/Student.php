<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'current_points',
    ];

    public static function startingPoints(): int
    {
        return (int) config('discipline.starting_points', 100);
    }

    protected static function booted(): void
    {
        static::creating(function (Student $student): void {
            if (! array_key_exists('current_points', $student->getAttributes())
                || $student->current_points === null) {
                $student->current_points = static::startingPoints();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
