<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, Service::class);
    }

    // Only admins can create categories
    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can create categories.');
            }
        });

        static::updating(function ($category) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can update categories.');
            }
        });

        static::deleting(function ($category) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can delete categories.');
            }
        });
    }

    // Check if category has any services
    public function hasServices()
    {
        return $this->services()->exists();
    }

    // Check if category has any employees
    public function hasEmployees()
    {
        return $this->employees()->exists();
    }

    // Get all services with their employees
    public function getServicesWithEmployees()
    {
        return $this->services()->with('employees')->get();
    }
}
