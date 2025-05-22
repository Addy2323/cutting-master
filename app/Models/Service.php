<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    // Service type constants
    const TYPE_IN_SHOP = 'in_shop';
    const TYPE_AT_HOME = 'at_home';
    const TYPE_BOTH = 'both';

    // Service type options
    public static function getServiceTypes()
    {
        return [
            self::TYPE_IN_SHOP => 'In-Shop Only',
            self::TYPE_AT_HOME => 'At-Home Only',
            self::TYPE_BOTH => 'Both In-Shop and At-Home'
        ];
    }

    // Cast attributes
    protected $casts = [
        'travel_fee' => 'decimal:2',
        'service_radius' => 'integer',
        'travel_buffer_minutes' => 'integer',
        'is_active' => 'boolean'
    ];

    // Validation rules
    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'service_type' => 'required|in:' . implode(',', array_keys(self::getServiceTypes())),
            'travel_fee' => 'nullable|numeric|min:0',
            'service_radius' => 'nullable|integer|min:1',
            'travel_buffer_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ];
    }

    // Only admins can create services
    public static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can create services.');
            }
        });

        static::updating(function ($service) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can update services.');
            }
        });

        static::deleting(function ($service) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can delete services.');
            }
        });
    }

    // Check if service has any employees
    public function hasEmployees()
    {
        return $this->employees()->exists();
    }

    // Get all employees with their specializations
    public function getEmployeesWithSpecializations()
    {
        return $this->employees()->with('user')->get();
    }

    // Check if service is visible to a specific user
    public function isVisibleTo(User $user)
    {
        // Admins can see all services
        if ($user->isAdmin()) {
            return true;
        }

        // Professionals can only see services they are assigned to
        if ($user->isProfessional()) {
            return $this->employees()->where('user_id', $user->id)->exists();
        }

        // Subscribers can see all services
        if ($user->isSubscriber()) {
            return true;
        }

        return false;
    }

    // Get all visible services for a user
    public static function getVisibleServicesFor(User $user)
    {
        if ($user->isAdmin()) {
            return self::all();
        }

        if ($user->isProfessional()) {
            return self::whereHas('employees', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }

        if ($user->isSubscriber()) {
            return self::all();
        }

        return collect();
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot(['travel_fee', 'service_radius', 'travel_buffer_minutes'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInShop($query)
    {
        return $query->whereIn('service_type', [self::TYPE_IN_SHOP, self::TYPE_BOTH]);
    }

    public function scopeAtHome($query)
    {
        return $query->whereIn('service_type', [self::TYPE_AT_HOME, self::TYPE_BOTH]);
    }

    // Helper methods
    public function isAvailableAtHome()
    {
        return in_array($this->service_type, [self::TYPE_AT_HOME, self::TYPE_BOTH]);
    }

    public function isAvailableInShop()
    {
        return in_array($this->service_type, [self::TYPE_IN_SHOP, self::TYPE_BOTH]);
    }

    public function getTravelFeeForEmployee(Employee $employee)
    {
        return $this->employees()
            ->where('employee_id', $employee->id)
            ->value('travel_fee') ?? $this->travel_fee;
    }

    public function getServiceRadiusForEmployee(Employee $employee)
    {
        return $this->employees()
            ->where('employee_id', $employee->id)
            ->value('service_radius') ?? $this->service_radius;
    }

    public function getTravelBufferForEmployee(Employee $employee)
    {
        return $this->employees()
            ->where('employee_id', $employee->id)
            ->value('travel_buffer_minutes') ?? $this->travel_buffer_minutes;
    }
}
