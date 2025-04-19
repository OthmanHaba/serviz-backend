<?php

namespace App\Filament\Resources\SupportMessageResource\Pages;

use App\Filament\Resources\SupportMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupportMessage extends EditRecord
{
    protected static string $resource = SupportMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
