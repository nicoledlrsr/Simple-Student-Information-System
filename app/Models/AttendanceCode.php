<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceCode extends Model
{
    protected $fillable = [
        'class_session_id',
        'subject_id',
        'code',
        'date',
        'expires_at',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'attendance_code_id');
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isValid(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    protected static function boot(): void
    {
        parent::boot();

        // Automatically deactivate expired codes when accessed
        static::retrieved(function (AttendanceCode $code) {
            if ($code->is_active && $code->isExpired()) {
                $code->update(['is_active' => false]);
            }
        });
    }
}
