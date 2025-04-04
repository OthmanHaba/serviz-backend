<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Display the value in the appropriate format based on type
        if ($data['type'] === 'boolean') {
            $data['value'] = (bool) $data['value'];
        } elseif ($data['type'] === 'number') {
            $data['value'] = (float) $data['value'];
        }
        
        return $data;
    }
    
    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Edit Setting')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('type')
                            ->disabled()
                            ->dehydrated(false),
                            
                        $this->getValueFormField(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Optional description of what this setting controls'),
                    ]),
            ]);
    }
    
    protected function getValueFormField(): Forms\Components\Field
    {
        $record = $this->getRecord();
        $type = $record->type;
        
        return match($type) {
            'text' => Forms\Components\TextInput::make('value')
                ->label('Value')
                ->required(),
                
            'number' => Forms\Components\TextInput::make('value')
                ->label('Value')
                ->numeric()
                ->required(),
                
            'boolean' => Forms\Components\Toggle::make('value')
                ->label('Value'),
                
            'array' => Forms\Components\TagsInput::make('value')
                ->label('Value'),
                
            'json' => Forms\Components\Textarea::make('value')
                ->label('Value')
                ->rows(5)
                ->helperText('Enter valid JSON format'),
                
            default => Forms\Components\TextInput::make('value')
                ->label('Value')
                ->required(),
        };
    }
}
