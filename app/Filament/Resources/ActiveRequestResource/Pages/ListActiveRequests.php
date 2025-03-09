<?php

namespace App\Filament\Resources\ActiveRequestResource\Pages;

use App\Filament\Resources\ActiveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActiveRequests extends ListRecords
{
    protected static string $resource = ActiveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
