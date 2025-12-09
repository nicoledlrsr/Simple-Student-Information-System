<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'subject_code',
        'subject_name',
        'course',
        'year_level',
        'semester',
        'units',
        'hours_per_week',
        'schedule',
        'time',
        'description',
        'instructor_id',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
