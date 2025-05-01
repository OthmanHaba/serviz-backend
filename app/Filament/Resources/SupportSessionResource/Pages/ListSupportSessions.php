<?php

namespace App\Filament\Resources\SupportSessionResource\Pages;

use App\Filament\Resources\SupportSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListSupportSessions extends ListRecords
{
    protected static string $resource = SupportSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
