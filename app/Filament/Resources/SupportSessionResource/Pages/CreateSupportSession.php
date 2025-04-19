<?php

namespace App\Filament\Resources\SupportSessionResource\Pages;

use App\Filament\Resources\SupportSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupportSession extends CreateRecord
{
    protected static string $resource = SupportSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
