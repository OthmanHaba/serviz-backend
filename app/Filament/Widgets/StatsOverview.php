<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\Payment;

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

            Stat::make('Active Providers', ServiceProvider::where('is_available', true)->count())
                ->description('Currently available service providers')
                ->descriptionIcon('heroicon-m-truck')
                ->chart([3, 5, 4, 3, 6, 3, 5, 4])
                ->color('success'),

            Stat::make('Pending Requests', ServiceRequest::where('status', 'pending')->count())
                ->description('Requests waiting for providers')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([2, 3, 4, 3, 4, 3, 4, 3])
                ->color('warning'),

            Stat::make('Today\'s Revenue', function () {
                $amount = Payment::whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->sum('amount');
                return '$' . number_format($amount, 2);
            })
                ->description('Revenue from completed payments today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([4, 5, 4, 5, 6, 5, 4, 5])
                ->color('success'),
        ];
    }
} 