<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'icon',
        'is_active',
        'credentials'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'array'
    ];

    public function getIconUrlAttribute()
    {
        // If the icon is a URL, return it directly
        if (filter_var($this->icon, FILTER_VALIDATE_URL)) {
            return $this->icon;
        }
        
        // Otherwise, assume it's a local file
        return asset('storage/payment-methods/' . $this->icon);
    }
} 