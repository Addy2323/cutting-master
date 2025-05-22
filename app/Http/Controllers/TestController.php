<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function testGeocoding(Request $request)
    {
        try {
            // Test address
            $address = "1600 Amphitheatre Parkway";
            $city = "Mountain View";
            $postalCode = "94043";

            // Get coordinates
            $coordinates = $this->geocodingService->getCoordinates($address, $city, $postalCode);

            if (!$coordinates) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get coordinates'
                ], 400);
            }

            // Test employee location (Google HQ)
            $employeeLat = 37.4220;
            $employeeLng = -122.0841;
            $radius = 10; // 10 km radius

            // Check if within radius
            $isWithinRadius = $this->geocodingService->isWithinRadius(
                $address,
                $city,
                $postalCode,
                $employeeLat,
                $employeeLng,
                $radius
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'coordinates' => $coordinates,
                    'is_within_radius' => $isWithinRadius,
                    'formatted_address' => $this->geocodingService->formatAddress($address, $city, $postalCode)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 