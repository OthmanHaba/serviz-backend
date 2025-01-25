<?php

namespace App\Filament\Resources\ServiceProviderResource\Pages;

use App\Filament\Resources\ServiceProviderResource;
use Filament\Resources\Pages\Page;
use App\Models\ServiceProvider;

class ServiceProviderMap extends Page
{
    protected static string $resource = ServiceProviderResource::class;

    protected static string $view = 'filament.resources.service-provider-resource.pages.service-provider-map';

    public ?ServiceProvider $record = null;

    public function mount(ServiceProvider $record): void
    {
        $this->record = $record;
    }
} 