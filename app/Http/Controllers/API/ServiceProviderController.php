<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Services\ServiceProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceProviderController extends Controller
{
    protected $providerService;

    public function __construct(ServiceProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'provider_type' => 'required|in:tow_truck,mechanic,gas_delivery',
            'service_radius_km' => 'required|numeric|min:0',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $provider = $this->providerService->create($request->all());

        return response()->json($provider, 201);
    }

    public function updateAvailability(Request $request, ServiceProvider $provider)
    {
        $validator = Validator::make($request->all(), [
            'is_available' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $provider = $this->providerService->updateAvailability($provider, $request->is_available);

        return response()->json($provider);
    }

    public function updateLocation(Request $request, ServiceProvider $provider)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $location = $this->providerService->updateLocation(
            $provider,
            $request->latitude,
            $request->longitude
        );

        return response()->json($location);
    }

    public function findNearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'required|numeric|min:0',
            'service_type' => 'required|in:tow_truck,mechanic,gas_delivery'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $providers = $this->providerService->findNearbyProviders(
            $request->latitude,
            $request->longitude,
            $request->radius_km,
            $request->service_type
        );

        return response()->json($providers);
    }

    public function show(ServiceProvider $provider)
    {
        return response()->json($provider->load(['currentLocation']));
    }
} 