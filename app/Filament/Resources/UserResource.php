<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'wallet',
            'providerServices',
            'providerServices.serviceType',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([

                    TextInput::make('name')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),

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

                    Select::make('role')
                        ->options([
                            'user' => 'User',
                            'provider' => 'Provider',
                            'admin' => 'Admin',
                        ]),

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
                    ->money('LYD')
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
                        ->hidden(fn (User $record) => $record->is_banned)
                        ->hidden(fn (User $record) => $record->role === 'admin')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->label('حظر')
                        ->action(fn (User $record) => $record->update(['is_banned' => true, 'is_active' => false])),

                    Action::make('unban')
                        ->hidden(fn (User $record) => $record->role === 'admin')
                        ->hidden(fn (User $record) => ! $record->is_banned)
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->label('فك الحظر')
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->update(['is_active' => true, 'is_banned' => false])),

                    Action::make('resetPassword')
                        ->icon('heroicon-o-key')
                        ->label(' اعادة تعيين كلمة المرور')
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
                        ->label('إيداع')
                        ->icon('heroicon-o-currency-dollar')
                        ->color(Color::Green)
                        ->form([
                            TextInput::make('amount')
                                ->required()
                                ->numeric()
                                ->label('القيمة ')
                                ->minValue(1),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->deposit($data['amount']);
                        }),
                ])
                    ->icon('heroicon-o-rectangle-group'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Information')
                    ->columns(2)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope-open'),

                                TextEntry::make('phone')
                                    ->label('Phone Number')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('wallet.balance')
                                    ->label('Balance')
                                    ->suffix('LYD')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->badge(),

                                IconEntry::make('is_active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime()
                            ->icon('heroicon-o-pencil'),
                    ]),

                Section::make('Provider Services')
                    ->visible(fn (User $record) => $record->isProvider())
                    ->columns(2)
                    ->schema([
                        RepeatableEntry::make('providerServices')
                            ->schema([
                                TextEntry::make('providerServices.serviceType.name')
                                    ->label('Service Type'),

                                TextEntry::make('providerServices.serviceType.price')
                                    ->label('Service price'),

                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            //            'create' => Pages\CreateUser::route('/create'),
            //            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
