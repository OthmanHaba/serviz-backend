<?php

namespace App\Filament\Resources\SupportMessageResource\Pages;

use App\Filament\Resources\SupportMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupportMessages extends ListRecords
{
    protected static string $resource = SupportMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
