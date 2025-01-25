<?php

namespace App\Filament\Resources\PricingModelResource\Pages;

use App\Filament\Resources\PricingModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPricingModels extends ListRecords
{
    protected static string $resource = PricingModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 