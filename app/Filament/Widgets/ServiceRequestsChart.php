<?php

namespace App\Filament\Widgets;

use App\Models\ActiveRequest;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;

class ServiceRequestsChart extends ChartWidget
{
    protected static ?string $heading = 'Service Requests';

    protected static ?int $sort = 2;

    protected function getData(): array
    {

        $sampleData = Trend::model(ActiveRequest::class)
            ->between(
                start: Carbon::now()->startOfMonth(),
                end: Carbon::now()->endOfMonth()
            )
            ->perday()
            ->count();

        $dates = $sampleData->map(fn ($data) => Carbon::parse($data->date)->day);

        $counts = $sampleData->map(fn ($data) => $data->aggregate);

        return [
            'datasets' => [
                [
                    'label' => 'Active Requests',
                    'data' => $counts,
                ],
            ],
            'labels' => $dates,
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
}
