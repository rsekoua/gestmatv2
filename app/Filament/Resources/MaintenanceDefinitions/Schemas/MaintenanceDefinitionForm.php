<?php

namespace App\Filament\Resources\MaintenanceDefinitions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MaintenanceDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('materiel_type_id')
                    ->relationship('materielType', 'nom')
                    ->label('Type de Matériel')
                    ->required(),
                TextInput::make('label')
                    ->label('Libellé')
                    ->required()
                    ->maxLength(255),
                TextInput::make('frequency_days')
                    ->label('Fréquence (jours)')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true)
                    ->required(),
                Textarea::make('description')
                    ->label('Description / Procédure')
                    ->columnSpanFull(),
            ]);
    }
}
