<?php

namespace App\Filament\Widgets;

use App\Models\ServiceRequest;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ServiceRequestsChart extends ChartWidget
{
    protected static ?string $heading = 'Service Requests';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // $data = Trend::model(ServiceRequest::class)
        //     ->between(
        //         start: now()->startOfMonth(),
        //         end: now()->endOfMonth(),
        //     )
        //     ->perDay()
        //     ->count();

        $data = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        return [
            'datasets' => [
                [
                    'label' => 'Service Requests',
                    // 'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'data' => $data,
                    'borderColor' => '#2563eb',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                ],
            ],
            // 'labels' => $data->map(fn (TrendValue $value) => $value->date),
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
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
