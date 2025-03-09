<?php

namespace App\Filament\Widgets;

use App\Models\ActiveRequest;
use App\Models\ServiceRequest;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestServiceRequests extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Service Requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActiveRequest::query()
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('provider.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('price')
                    ->badge()
                    ->label('Price'),
            ])
            // ->actions([
            //     Tables\Actions\Action::make('view')
            //         ->url(fn (ServiceRequest $record): string => route('filament.admin.resources.service-requests.edit', $record))
            //         ->icon('heroicon-m-eye'),
            // ])
            ->paginated(false);
    }
}
