<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActiveProvidersMap;
use App\Filament\Widgets\LatestServiceRequests;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\ServiceRequestsChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    //    protected static string $view = 'filament.pages.dashboard';

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
            RevenueChart::class,
            LatestServiceRequests::class,
        ];
    }
}
