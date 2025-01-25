<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ServiceRequestsChart;
use App\Filament\Widgets\LatestServiceRequests;
use App\Filament\Widgets\ActiveProvidersMap;
use App\Filament\Widgets\RevenueChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ServiceRequestsChart::class,
            LatestServiceRequests::class,
            ActiveProvidersMap::class,
            RevenueChart::class,
        ];
    }
} 