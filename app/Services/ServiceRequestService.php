<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\RequestDetail;
use App\Models\PricingModel;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\DB;

class ServiceRequestService
{
    protected $providerService;
    protected $pricingService;

    public function __construct(
        ServiceProviderService $providerService,
        PricingService $pricingService
    ) {
        $this->providerService = $providerService;
        $this->pricingService = $pricingService;
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Calculate price
            $price = $this->pricingService->calculatePrice(
                $data['service_type'],
                $data['distance_km'] ?? 0,
                $data['details'] ?? []
            );

            // Create service request
            $request = ServiceRequest::create([
                'user_id' => $data['user_id'],
                'service_type' => $data['service_type'],
                'status' => 'pending',
                'pickup_location' => new Point($data['latitude'], $data['longitude']),
                'total_price' => $price,
                'requested_at' => now(),
            ]);

            // Create request details
            RequestDetail::create([
                'request_id' => $request->request_id,
                'details' => $data['details'] ?? [],
            ]);

            // Find nearby providers
            $providers = $this->providerService->findNearbyProviders(
                $data['latitude'],
                $data['longitude'],
                10, // Default radius in km
                $data['service_type']
            );

            DB::commit();

            return [
                'request' => $request->load('details'),
                'available_providers' => $providers,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function acceptRequest(ServiceRequest $request, int $providerId)
    {
        if ($request->status !== 'pending') {
            throw new \Exception('Request is no longer pending');
        }

        $request->update([
            'provider_id' => $providerId,
            'status' => 'accepted'
        ]);

        return $request->load(['provider', 'details']);
    }

    public function updateStatus(ServiceRequest $request, string $status)
    {
        $validTransitions = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (!in_array($status, $validTransitions[$request->status] ?? [])) {
            throw new \Exception('Invalid status transition');
        }

        $request->update(['status' => $status]);
        return $request;
    }

    public function getActiveRequests(int $userId)
    {
        return ServiceRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->with(['provider', 'details'])
            ->get();
    }

    public function getProviderRequests(int $providerId)
    {
        return ServiceRequest::where('provider_id', $providerId)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with(['user', 'details'])
            ->get();
    }
} 