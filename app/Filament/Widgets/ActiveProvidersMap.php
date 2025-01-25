<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\ServiceProvider;
use App\Models\Location;

class ActiveProvidersMap extends Widget
{
    protected static string $view = 'filament.widgets.active-providers-map';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $providers = ServiceProvider::with(['currentLocation'])
            ->where('is_available', true)
            ->get()
            ->map(function ($provider) {
                if ($provider->currentLocation) {
                    $coordinates = json_decode($provider->currentLocation->coordinates);
                    return [
                        'id' => $provider->provider_id,
                        'name' => $provider->name,
                        'type' => $provider->provider_type,
                        'lat' => $coordinates->lat,
                        'lng' => $coordinates->lng,
                    ];
                }
                return null;
            })
            ->filter();

        return [
            'providers' => $providers,
            'center' => [
                'lat' => $providers->avg('lat') ?? 0,
                'lng' => $providers->avg('lng') ?? 0,
            ],
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
} 