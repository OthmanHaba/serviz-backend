<?php

namespace App\Filament\Resources\ServiceProviderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class ServiceRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceRequests';

    protected static ?string $recordTitleAttribute = 'request_id';

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
            ->recordTitleAttribute('request_id')
            ->columns([
                TextColumn::make('request_id')
                    ->sortable(),
                TextColumn::make('service_type')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('service_type')
                    ->options([
                        'tow_truck' => 'Tow Truck',
                        'mechanic' => 'Mechanic',
                        'gas_delivery' => 'Gas Delivery',
                    ]),
            ])
            ->headerActions([
                // No create action needed in this relation
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk actions needed
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
    }
} 