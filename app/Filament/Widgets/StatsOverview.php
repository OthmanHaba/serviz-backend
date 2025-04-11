<?php

namespace App\Filament\Widgets;

use App\Enums\ServiceStatus;
use App\Models\ActiveRequest;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('primary'),

            Stat::make('Total completed Requests', ActiveRequest::whereStatus(ServiceStatus::Completed)->count())
                ->description('Total completed requests')
                ->descriptionIcon('heroicon-m-check')
                ->chart([3, 5, 4, 3, 6, 3, 5, 4])
                ->color(Color::Green),

            Stat::make('Total revenue', function () {
                return ActiveRequest::whereStatus(ServiceStatus::Completed)->sum('price') * 0.3;
            })->description('Total revenue from completed requests')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([4, 5, 4, 5, 6, 5, 4, 5])
                ->color('success'),

            Stat::make('Active Providers', User::whereRole('provider')
                ->whereIsActive(true)
                ->count())
                ->description('Currently available service providers')
                ->descriptionIcon('heroicon-m-truck')
                ->chart([3, 5, 4, 3, 6, 3, 5, 4])
                ->color('success'),
        ];
    }
}
