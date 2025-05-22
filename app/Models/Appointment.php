<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_at_home' => 'boolean',
        'travel_fee' => 'decimal:2',
        'address_verified' => 'boolean'
    ];

    // Validation rules
    public static function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'employee_id' => 'required|exists:employees,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'is_at_home' => 'required|boolean',
            'address' => 'required_if:is_at_home,true|nullable|string',
            'city' => 'required_if:is_at_home,true|nullable|string',
            'postal_code' => 'required_if:is_at_home,true|nullable|string',
            'notes' => 'nullable|string'
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Scopes
    public function scopeAtHome($query)
    {
        return $query->where('is_at_home', true);
    }

    public function scopeInShop($query)
    {
        return $query->where('is_at_home', false);
    }

    // Helper methods
    public function calculateTotal()
    {
        $total = $this->service->price;
        
        if ($this->is_at_home) {
            $total += $this->travel_fee;
        }
        
        return $total;
    }

    public function validateAddress()
    {
        if (!$this->is_at_home) {
            return true;
        }

        // Check if all required address fields are present
        if (empty($this->address) || empty($this->city) || empty($this->postal_code)) {
            return false;
        }

        // Here you could add additional validation like:
        // - Validating postal code format
        // - Checking if address exists using a geocoding service
        // - Verifying if address is within service radius

        return true;
    }

    public function calculateTravelBuffer()
    {
        if (!$this->is_at_home) {
            return 0;
        }

        return $this->service->getTravelBufferForEmployee($this->employee);
    }

    public function getAdjustedStartTime()
    {
        if (!$this->is_at_home) {
            return $this->booking_time;
        }

        $buffer = $this->calculateTravelBuffer();
        $times = explode(' - ', $this->booking_time);
        $startTime = \Carbon\Carbon::createFromFormat('g:i A', trim($times[0]));
        return $startTime->subMinutes($buffer)->format('g:i A');
    }

    public function getAdjustedEndTime()
    {
        if (!$this->is_at_home) {
            return $this->booking_time;
        }

        $buffer = $this->calculateTravelBuffer();
        $times = explode(' - ', $this->booking_time);
        $endTime = \Carbon\Carbon::createFromFormat('g:i A', trim($times[1]));
        return $endTime->addMinutes($buffer)->format('g:i A');
    }

    public function isWithinServiceRadius()
    {
        if (!$this->is_at_home) {
            return true;
        }

        $serviceRadius = $this->service->getServiceRadiusForEmployee($this->employee);
        
        if (!$serviceRadius) {
            return true; // No radius set means no restriction
        }

        // Here you would implement the distance calculation
        // between the employee's location and the client's address
        // using a geocoding service
        
        return true; // Placeholder
    }
}
