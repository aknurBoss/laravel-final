<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = [
        'name',
        'started_at',
        'closed_at',
        'closed_by_user_id',
        'students_count',
        'records_count',
        'total_merits',
        'total_demerits',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at'  => 'datetime',
    ];

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function pointHistories(): HasMany
    {
        return $this->hasMany(PointHistory::class);
    }

    public function studentSnapshots(): HasMany
    {
        return $this->hasMany(SemesterStudentSnapshot::class);
    }

    public function isActive(): bool
    {
        return $this->closed_at === null;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('closed_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('closed_at')->orderByDesc('closed_at');
    }
}
