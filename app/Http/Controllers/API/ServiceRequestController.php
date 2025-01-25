<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceRequestController extends Controller
{
    protected $requestService;

    public function __construct(ServiceRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|in:tow_truck,mechanic,gas_delivery',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'distance_km' => 'nullable|numeric|min:0',
            'details' => 'nullable|array',
            'details.vehicle_type' => 'required_if:service_type,tow_truck',
            'details.fuel_type' => 'required_if:service_type,gas_delivery',
            'details.liters' => 'required_if:service_type,gas_delivery|numeric|min:0',
            'details.service_complexity' => 'required_if:service_type,mechanic|in:low,medium,high'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->requestService->create([
                'user_id' => auth()->id(),
                'service_type' => $request->service_type,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'distance_km' => $request->distance_km,
                'details' => $request->details
            ]);

            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function acceptRequest(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'pending') {
            return response()->json(['message' => 'Request is no longer pending'], 400);
        }

        try {
            $result = $this->requestService->acceptRequest($serviceRequest, auth()->id());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,in_progress,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->requestService->updateStatus($serviceRequest, $request->status);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function userRequests()
    {
        $requests = $this->requestService->getActiveRequests(auth()->id());
        return response()->json($requests);
    }

    public function providerRequests()
    {
        $requests = $this->requestService->getProviderRequests(auth()->id());
        return response()->json($requests);
    }

    public function show(ServiceRequest $serviceRequest)
    {
        // Check if user has access to this request
        if (auth()->id() !== $serviceRequest->user_id && 
            auth()->id() !== $serviceRequest->provider_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($serviceRequest->load(['user', 'provider', 'details', 'payment']));
    }
} 