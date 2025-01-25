<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceProviderResource\Pages;
use App\Models\ServiceProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use App\Filament\Resources\ServiceProviderResource\RelationManagers;

class ServiceProviderResource extends Resource
{
    protected static ?string $model = ServiceProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->required(),
                        Select::make('provider_type')
                            ->options([
                                'tow_truck' => 'Tow Truck',
                                'mechanic' => 'Mechanic',
                                'gas_delivery' => 'Gas Delivery',
                            ])
                            ->required(),
                        TextInput::make('service_radius_km')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100),
                        TextInput::make('rating')
                            ->numeric()
                            ->disabled()
                            ->minValue(0)
                            ->maxValue(5),
                        Toggle::make('is_available')
                            ->default(true)
                            ->helperText('Provider availability status'),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider_id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('provider_type')
                    ->colors([
                        'primary' => 'tow_truck',
                        'success' => 'mechanic',
                        'warning' => 'gas_delivery',
                    ]),
                TextColumn::make('rating')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => number_format($state, 1) . ' ★'),
                TextColumn::make('service_radius_km')
                    ->label('Radius (km)')
                    ->sortable(),
                IconColumn::make('is_available')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('provider_type')
                    ->options([
                        'tow_truck' => 'Tow Truck',
                        'mechanic' => 'Mechanic',
                        'gas_delivery' => 'Gas Delivery',
                    ]),
                SelectFilter::make('is_available')
                    ->options([
                        '1' => 'Available',
                        '0' => 'Unavailable',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('toggleAvailability')
                    ->icon('heroicon-o-power')
                    ->requiresConfirmation()
                    ->action(function (ServiceProvider $record) {
                        $record->update(['is_available' => !$record->is_available]);
                    }),
                // Action::make('viewLocation')
                //     ->icon('heroicon-o-map-pin')
                //     ->url(fn (ServiceProvider $record) => route('filament.admin.resources.service-providers.map', $record))
                //     ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggleAvailability')
                        ->icon('heroicon-o-power')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['is_available' => !$record->is_available]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ServiceRequestsRelationManager::class,
            RelationManagers\LocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceProviders::route('/'),
            'create' => Pages\CreateServiceProvider::route('/create'),
            'edit' => Pages\EditServiceProvider::route('/{record}/edit'),
            'map' => Pages\ServiceProviderMap::route('/{record}/map'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_available', true)->count();
    }
} 