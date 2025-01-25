<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ServiceRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class LatestServiceRequests extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Service Requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceRequest::query()
                    ->latest('requested_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('request_id')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable(),
                BadgeColumn::make('service_type')
                    ->colors([
                        'primary' => 'tow_truck',
                        'success' => 'mechanic',
                        'warning' => 'gas_delivery',
                    ]),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'accepted',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('total_price')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (ServiceRequest $record): string => route('filament.admin.resources.service-requests.edit', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
} 