<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeolocationService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.yandex_geolocation.key', '');
    }

    public function geocode(string $address): ?array
    {
        if (empty($this->apiKey)) {
            return $this->mockGeocode($address);
        }

        $response = Http::get('https://geocode-maps.yandex.ru/1.x/', [
            'geocode' => $address,
            'apikey' => $this->apiKey,
            'format' => 'json',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $pos = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'] ?? null;
            
            if ($pos) {
                [$lon, $lat] = explode(' ', $pos);
                return [
                    'latitude' => (float) $lat,
                    'longitude' => (float) $lon,
                ];
            }
        }

        return null;
    }

    public function reverseGeocode(float $lat, float $lon): ?string
    {
        if (empty($this->apiKey)) {
            return $this->mockReverseGeocode($lat, $lon);
        }

        $response = Http::get('https://geocode-maps.yandex.ru/1.x/', [
            'geocode' => "{$lon},{$lat}",
            'apikey' => $this->apiKey,
            'format' => 'json',
            'kind' => 'house',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderResponseMetaData']['request'] ?? null;
        }

        return null;
    }

    private function mockGeocode(string $address): array
    {
        return [
            'latitude' => 55.755814 + (rand(-100, 100) / 1000),
            'longitude' => 37.617644 + (rand(-100, 100) / 1000),
        ];
    }

    private function mockReverseGeocode(float $lat, float $lon): string
    {
        return "Москва, ул. Примерная, д. 1";
    }
}