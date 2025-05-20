<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'appointment_id',
        'user_id',
        'employee_id',
        'rating',
        'comment',
        'is_verified',
        'is_public'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'rating' => 'integer'
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
} 