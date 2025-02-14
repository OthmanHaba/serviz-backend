<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        // $data = Trend::model(Payment::class)
        //     ->between(
        //         start: now()->startOfMonth(),
        //         end: now()->endOfMonth(),
        //     )
        //     ->perDay()
        //     ->sum('amount');
        $data = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    // 'data' => $data->map(fn (TrendValue $value) => $value->aggregate / 100), // Convert cents to dollars
                    'data' => $data,
                    'borderColor' => '#059669',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                ],
            ],
            // 'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M d')),
            'labels' => $data,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 days',
            'month' => 'This month',
            'year' => 'This year',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value; }',
                    ],
                ],
            ],
        ];
    }
}
