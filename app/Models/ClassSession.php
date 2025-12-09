<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'description',
        'is_active',
        'teacher_id',
        'course',
        'course_id',
        'subject',
        'schedule',
        'time',
        'room',
        'instructor',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_class_session')
            ->withPivot('enrolled_date')
            ->withTimestamps();
    }

    public function attendanceCodes(): HasMany
    {
        return $this->hasMany(AttendanceCode::class);
    }

    public function sessionEnrollments(): HasMany
    {
        return $this->hasMany(SessionEnrollment::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function getTimeRangeAttribute(): string
    {
        // First try to use start_time and end_time if available
        if ($this->start_time && $this->end_time) {
            try {
                $start = \Carbon\Carbon::parse($this->start_time)->format('g:i A');
                $end = \Carbon\Carbon::parse($this->end_time)->format('g:i A');

                return $start.' - '.$end;
            } catch (\Exception $e) {
                // If parsing fails, fall through to time field
            }
        }

        // Fall back to the time field (which comes from subject)
        if ($this->time) {
            return $this->time;
        }

        // If nothing is available, return N/A
        return 'N/A';
    }
}
