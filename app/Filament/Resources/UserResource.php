<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('phone')
                        ->tel()
                        ->required(),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create'),
                    Toggle::make('is_active')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('wallet.balance')
                    ->label('Balance')
                    ->default(0)
                    ->money()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'provider' => 'provider',
                        'user' => 'user',
                        'admin' => 'admin',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Action::make('ban')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->update(['is_active' => false])),
                    Action::make('resetPassword')
                        ->icon('heroicon-o-key')
                        ->form([
                            TextInput::make('new_password')
                                ->password()
                                ->required()
                                ->minLength(8),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'password' => Hash::make($data['new_password']),
                            ]);
                        }),

                    Action::make('deposit')
                        ->label('Deposit')
                        ->icon('heroicon-o-currency-dollar')
                        ->color(Color::Green)
                        ->form([
                            TextInput::make('amount')
                                ->required()
                                ->numeric()
                                ->minValue(1),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'password' => Hash::make($data['new_password']),
                            ]);
                        }),
                ])
                ->icon('heroicon-o-rectangle-group')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('activate')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ServiceRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
