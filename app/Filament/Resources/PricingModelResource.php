<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingModelResource\Pages;
use App\Models\PricingModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;

class PricingModelResource extends Resource
{
    protected static ?string $model = PricingModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        Select::make('service_type')
                            ->options([
                                'tow_truck' => 'Tow Truck',
                                'mechanic' => 'Mechanic',
                                'gas_delivery' => 'Gas Delivery',
                            ])
                            ->required(),
                        TextInput::make('base_fee')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0),
                        TextInput::make('fee_per_km')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0),
                    ]),
                    KeyValue::make('parameters')
                        ->keyLabel('Parameter')
                        ->valueLabel('Value')
                        ->reorderable()
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model_id')
                    ->sortable(),
                TextColumn::make('service_type')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                TextColumn::make('base_fee')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('fee_per_km')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
                    ->options([
                        'tow_truck' => 'Tow Truck',
                        'mechanic' => 'Mechanic',
                        'gas_delivery' => 'Gas Delivery',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (PricingModel $record) {
                        $newModel = $record->replicate();
                        $newModel->service_type = $record->service_type . '_copy';
                        $newModel->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingModels::route('/'),
            'create' => Pages\CreatePricingModel::route('/create'),
            'edit' => Pages\EditPricingModel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
} 