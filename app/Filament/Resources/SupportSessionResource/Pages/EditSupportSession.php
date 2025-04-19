<?php

namespace App\Filament\Resources\SupportSessionResource\Pages;

use App\Filament\Resources\SupportSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupportSession extends EditRecord
{
    protected static string $resource = SupportSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
