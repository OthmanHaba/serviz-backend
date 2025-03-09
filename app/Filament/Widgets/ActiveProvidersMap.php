<?php

namespace App\Filament\Widgets;

use App\Models\Location;
use App\Models\User;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ActiveProvidersMap extends Widget
{
    protected static string $view = 'filament.widgets.active-providers-map';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public Collection $providers;

    public function mount(): void
    {
        $this->refreshProviders();
    }

    public function getProviders(): Collection
    {
        return User::whereRole('provider')
            ->get()
            ->filter(fn($provider) => $provider->currentLocation !== null);
    }

    protected function getViewData(): array
    {
        $this->providers = $this->getProviders();

        $avgLat = 0;
        $avgLong = 0;

        foreach ($this->providers as $provider) {
            $avgLat += $provider->currentLocation->latitude;

            $avgLong += $provider->currentLocation->longitude;
        }

        $avgLat /= $this->providers->count();
        $avgLong /= $this->providers->count();

        return [
            'providers' => $this->providers,
            'center' => [
                'lat' => $avgLat,
                'lng' => $avgLong,
            ],
        ];
    }

    public function refreshProviders(): void
    {
        $this->providers = $this->getProviders();
        $this->dispatch('$refresh'); // Ensures Livewire updates the frontend
    }

    public static function canView(): bool
    {
        return true;
    }
}
