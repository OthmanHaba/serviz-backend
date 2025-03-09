<?php

namespace App\Filament\Resources\ActiveRequestResource\Pages;

use App\Filament\Resources\ActiveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActiveRequest extends EditRecord
{
    protected static string $resource = ActiveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
