<?php

namespace App\Services;

use App\Models\ServiceProvider;
use App\Models\Location;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\DB;

class ServiceProviderService
{
    public function create(array $data)
    {
        return ServiceProvider::create([
            'name' => $data['name'],
            'provider_type' => $data['provider_type'],
            'service_radius_km' => $data['service_radius_km'],
            'is_available' => $data['is_available'] ?? true,
        ]);
    }

    public function updateAvailability(ServiceProvider $provider, bool $isAvailable)
    {
        $provider->update(['is_available' => $isAvailable]);
        return $provider;
    }

    public function updateLocation(ServiceProvider $provider, float $latitude, float $longitude)
    {
        return Location::create([
            'provider_id' => $provider->provider_id,
            'coordinates' => new Point($latitude, $longitude),
        ]);
    }

    public function findNearbyProviders(float $latitude, float $longitude, float $radiusKm, string $serviceType)
    {
        $point = new Point($latitude, $longitude);
        
        return ServiceProvider::query()
            ->whereHas('locations', function ($query) use ($point, $radiusKm) {
                $query->whereRaw('ST_Distance_Sphere(coordinates, ?) <= ?', [
                    $point->toWKT(),
                    $radiusKm * 1000 // Convert km to meters
                ]);
            })
            ->where('provider_type', $serviceType)
            ->where('is_available', true)
            ->get();
    }

    public function updateRating(ServiceProvider $provider)
    {
        $averageRating = DB::table('service_requests')
            ->where('provider_id', $provider->provider_id)
            ->whereNotNull('rating')
            ->avg('rating');

        $provider->update(['rating' => $averageRating ?? 0]);
        return $provider;
    }
} 