<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use App\Filament\Resources\ServiceRequestResource\RelationManagers;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->required(),
                        Select::make('provider_id')
                            ->relationship('provider', 'name')
                            ->searchable()
                            ->nullable(),
                        Select::make('service_type')
                            ->options([
                                'tow_truck' => 'Tow Truck',
                                'mechanic' => 'Mechanic',
                                'gas_delivery' => 'Gas Delivery',
                            ])
                            ->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'accepted' => 'Accepted',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        TextInput::make('total_price')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('provider.name')
                    ->sortable()
                    ->searchable()
                    ->label('Provider'),
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
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('service_type')
                    ->options([
                        'tow_truck' => 'Tow Truck',
                        'mechanic' => 'Mechanic',
                        'gas_delivery' => 'Gas Delivery',
                    ]),
                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('requested_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('requested_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ServiceRequest $record) => $record->status !== 'cancelled' && $record->status !== 'completed')
                    ->action(fn (ServiceRequest $record) => $record->update(['status' => 'cancelled'])),
                Action::make('assignProvider')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Select::make('provider_id')
                            ->label('Provider')
                            ->options(fn () => \App\Models\ServiceProvider::where('is_available', true)->pluck('name', 'provider_id'))
                            ->required(),
                    ])
                    ->visible(fn (ServiceRequest $record) => $record->status === 'pending')
                    ->action(function (ServiceRequest $record, array $data): void {
                        $record->update([
                            'provider_id' => $data['provider_id'],
                            'status' => 'accepted',
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'accepted', 'in_progress'])->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['request_id', 'user.email', 'provider.name', 'service_type'];
    }
} 