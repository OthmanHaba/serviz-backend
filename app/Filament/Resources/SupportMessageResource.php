<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportMessageResource\Pages;
use App\Models\SupportMessage;
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

class SupportMessageResource extends Resource
{
    protected static ?string $model = SupportMessage::class;

    protected static ?string $slug = 'support-messages';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('message')
                    ->required(),

                Select::make('sender_id')
                    ->relationship('sender', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('support_session_id')
                    ->required()
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?SupportMessage $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?SupportMessage $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('message'),

                TextColumn::make('sender.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('support_session_id'),
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
            'index' => Pages\ListSupportMessages::route('/'),
            'create' => Pages\CreateSupportMessage::route('/create'),
            'edit' => Pages\EditSupportMessage::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['sender']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['sender.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->sender) {
            $details['Sender'] = $record->sender->name;
        }

        return $details;
    }
}
