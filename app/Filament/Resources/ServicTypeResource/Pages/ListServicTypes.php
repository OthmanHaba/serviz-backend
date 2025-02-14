<?php

namespace App\Filament\Resources\ServicTypeResource\Pages;

use App\Filament\Resources\ServicTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicTypes extends ListRecords
{
    protected static string $resource = ServicTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
