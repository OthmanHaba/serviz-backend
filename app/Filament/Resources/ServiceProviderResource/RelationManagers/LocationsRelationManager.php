<?php

namespace App\Filament\Resources\ServiceProviderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class LocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'locations';

    protected static ?string $recordTitleAttribute = 'location_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only view, no form needed
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('location_id')
            ->columns([
                TextColumn::make('location_id')
                    ->sortable(),
                TextColumn::make('coordinates')
                    ->formatStateUsing(function ($state) {
                        $coordinates = json_decode($state);
                        return "Lat: {$coordinates->lat}, Lng: {$coordinates->lng}";
                    }),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Update'),
            ])
            ->filters([
                // No filters needed for locations
            ])
            ->headerActions([
                // No create action needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('viewOnMap')
                    ->icon('heroicon-o-map')
                    ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query=" . json_decode($record->coordinates)->lat . "," . json_decode($record->coordinates)->lng)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // No bulk actions needed
            ])
            ->defaultSort('updated_at', 'desc')
            ->poll('5s'); // Auto-refresh every 5 seconds
    }
} 