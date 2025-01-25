<?php

namespace App\Services;

use App\Models\PricingModel;

class PricingService
{
    public function calculatePrice(string $serviceType, float $distanceKm, array $details = [])
    {
        $pricingModel = PricingModel::where('service_type', $serviceType)->first();
        
        if (!$pricingModel) {
            throw new \Exception('Pricing model not found for this service type');
        }

        $price = $pricingModel->base_fee;
        
        // Add distance-based fee
        if ($distanceKm > 0) {
            $price += $distanceKm * $pricingModel->fee_per_km;
        }

        // Apply additional fees based on service type and details
        if ($pricingModel->parameters) {
            $price += $this->calculateAdditionalFees($serviceType, $details, $pricingModel->parameters);
        }

        return round($price, 2);
    }

    protected function calculateAdditionalFees(string $serviceType, array $details, array $parameters)
    {
        $additionalFees = 0;

        switch ($serviceType) {
            case 'tow_truck':
                // Additional fee for vehicle type
                if (isset($details['vehicle_type']) && isset($parameters['vehicle_type_fees'][$details['vehicle_type']])) {
                    $additionalFees += $parameters['vehicle_type_fees'][$details['vehicle_type']];
                }
                break;

            case 'gas_delivery':
                // Additional fee based on fuel type and quantity
                if (isset($details['fuel_type']) && isset($parameters['fuel_type_fees'][$details['fuel_type']])) {
                    $fuelTypeFee = $parameters['fuel_type_fees'][$details['fuel_type']];
                    $liters = $details['liters'] ?? 0;
                    $additionalFees += $fuelTypeFee * $liters;
                }
                break;

            case 'mechanic':
                // Additional fee for service complexity
                if (isset($details['service_complexity']) && isset($parameters['complexity_fees'][$details['service_complexity']])) {
                    $additionalFees += $parameters['complexity_fees'][$details['service_complexity']];
                }
                break;
        }

        return $additionalFees;
    }

    public function createPricingModel(array $data)
    {
        return PricingModel::create([
            'service_type' => $data['service_type'],
            'base_fee' => $data['base_fee'],
            'fee_per_km' => $data['fee_per_km'],
            'parameters' => $data['parameters'] ?? null,
        ]);
    }

    public function updatePricingModel(PricingModel $model, array $data)
    {
        $model->update([
            'base_fee' => $data['base_fee'],
            'fee_per_km' => $data['fee_per_km'],
            'parameters' => $data['parameters'] ?? $model->parameters,
        ]);

        return $model;
    }
} 