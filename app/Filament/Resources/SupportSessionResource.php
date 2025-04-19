<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportSessionResource\Pages;
use App\Models\SupportSession;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupportSessionResource extends Resource
{
    protected static ?string $model = SupportSession::class;

    protected static ?string $slug = 'support-sessions';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('status')
                    ->required(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?SupportSession $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?SupportSession $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status'),

                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('admin.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportSessions::route('/'),
            'create' => Pages\CreateSupportSession::route('/create'),
            'edit' => Pages\EditSupportSession::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'admin']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'admin.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        if ($record->admin) {
            $details['Admin'] = $record->admin->name;
        }

        return $details;
    }
}
