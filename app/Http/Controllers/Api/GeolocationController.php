<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeolocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeolocationController extends Controller
{
    public function __construct(
        private GeolocationService $geolocationService
    ) {}

    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string|max:500',
        ]);

        $coordinates = $this->geolocationService->geocode($request->address);

        if (!$coordinates) {
            return response()->json([
                'message' => 'Address not found',
            ], 404);
        }

        return response()->json([
            'address' => $request->address,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);
    }

    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $address = $this->geolocationService->reverseGeocode(
            $request->latitude,
            $request->longitude
        );

        if (!$address) {
            return response()->json([
                'message' => 'Address not found for coordinates',
            ], 404);
        }

        return response()->json([
            'address' => $address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }
}