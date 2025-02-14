<?php

namespace App\Filament\Resources\ServicTypeResource\Pages;

use App\Filament\Resources\ServicTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServicType extends EditRecord
{
    protected static string $resource = ServicTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
