<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?string $navigationLabel = 'Settings';
    
    protected static ?string $navigationGroup = 'System';
    
    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Setting Key')
                            ->helperText('Unique identifier for this setting')
                            ->disabled(fn ($record) => $record !== null),
                            
                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'number' => 'Number',
                                'boolean' => 'Boolean (Yes/No)',
                                'array' => 'Array/List',
                                'json' => 'JSON Object',
                            ])
                            ->default('text')
                            ->required()
                            ->reactive()
                            ->label('Value Type')
                            ->disabled(fn ($record) => $record !== null),
                            
                        Forms\Components\TextInput::make('value')
                            ->label('Value')
                            ->required()
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text', 'number']))
                            ->numeric(fn (Forms\Get $get) => $get('type') === 'number'),
                            
                        Forms\Components\Toggle::make('value')
                            ->label('Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'boolean'),
                            
                        Forms\Components\TagsInput::make('value')
                            ->label('Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'array'),
                            
                        Forms\Components\Textarea::make('value')
                            ->label('Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->rows(5)
                            ->helperText('Enter valid JSON format'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Optional description of what this setting controls'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Key')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) || is_object($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT);
                        }
                        
                        if (is_bool($state)) {
                            return $state ? 'Yes' : 'No';
                        }
                        
                        return (string) $state;
                    })
                    ->limit(50)
                    ->tooltip(function ($state) {
                        if (is_array($state) || is_object($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT);
                        }
                        
                        return (string) $state;
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Setting Value'),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
