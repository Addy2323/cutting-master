<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Client\ConnectionException;

class GeocodingService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
    protected $maxRetries = 3;
    protected $retryDelay = 1000; // milliseconds

    public function __construct()
    {
        // Try to get the API key from config first, then fall back to env
        $this->apiKey = config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY');
        
        // Debug information
        Log::info('GeocodingService initialized', [
            'api_key_exists' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey ?? ''),
            'config_value' => config('services.google.maps_api_key'),
            'env_value' => env('GOOGLE_MAPS_API_KEY')
        ]);
        
        if (empty($this->apiKey)) {
            throw new Exception('Google Maps API key is not configured. Please check your .env file and config/services.php');
        }
    }

    /**
     * Get coordinates for an address
     */
    public function getCoordinates($address)
    {
        try {
            // Check if we already have coordinates in cache
            $cacheKey = 'coordinates_' . md5($address);
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Use OpenStreetMap Nominatim API
            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'AppointmentBookingSystem/1.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error('Geocoding API error', [
                    'address' => $address,
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
                return null;
            }

            $data = json_decode($response, true);
            
            if (empty($data)) {
                Log::warning('No coordinates found for address', ['address' => $address]);
                return null;
            }

            $coordinates = [
                'lat' => $data[0]['lat'],
                'lng' => $data[0]['lon']
            ];

            // Cache the coordinates for 24 hours
            Cache::put($cacheKey, $coordinates, 86400);

            return $coordinates;

        } catch (\Exception $e) {
            Log::error('Geocoding service error', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Calculate distance between two points using the Haversine formula
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance; // Distance in km
    }

    /**
     * Validate if an address is within service radius
     */
    public function isWithinRadius(string $address, string $city, string $postalCode, float $employeeLat, float $employeeLng, int $radius): bool
    {
        try {
            $coordinates = $this->getCoordinates($address);

            if (!$coordinates) {
                Log::warning('Could not get coordinates for address', [
                    'address' => $this->formatAddress($address, $city, $postalCode)
                ]);
                return false;
            }

            $distance = $this->calculateDistance(
                $employeeLat,
                $employeeLng,
                $coordinates['lat'],
                $coordinates['lng']
            );

            $isWithin = $distance <= $radius;
            
            Log::info('Radius check result', [
                'address' => $this->formatAddress($address, $city, $postalCode),
                'distance' => $distance,
                'radius' => $radius,
                'is_within' => $isWithin
            ]);

            return $isWithin;
        } catch (Exception $e) {
            Log::error('Error in isWithinRadius', [
                'error' => $e->getMessage(),
                'address' => $this->formatAddress($address, $city, $postalCode)
            ]);
            return false;
        }
    }

    /**
     * Format address for display
     */
    public function formatAddress(string $address, string $city, string $postalCode): string
    {
        return trim("{$address}, {$city}, {$postalCode}");
    }
} 