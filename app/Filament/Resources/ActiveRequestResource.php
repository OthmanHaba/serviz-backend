<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActiveRequestResource\Pages;
use App\Filament\Resources\ActiveRequestResource\RelationManagers;
use App\Models\ActiveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActiveRequestResource extends Resource
{
    protected static ?string $model = ActiveRequest::class;

    protected static ?string $navigationGroup = 'Service Management';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->required(),

                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('service_fee')
                    ->state(function(ActiveRequest $record){
                        return $record->price * 0.3;
                    })
                    ->money()
                    ->sortable(),


                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),

            ])
            ->filters([
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActiveRequests::route('/'),
            'create' => Pages\CreateActiveRequest::route('/create'),
            'edit' => Pages\EditActiveRequest::route('/{record}/edit'),
        ];
    }
}
