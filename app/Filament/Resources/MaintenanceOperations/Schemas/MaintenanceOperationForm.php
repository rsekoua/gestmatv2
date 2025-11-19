<?php

namespace App\Filament\Resources\MaintenanceOperations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MaintenanceOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('materiel_id')
                    ->relationship('materiel', 'nom')
                    ->label('Matériel')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('maintenance_definition_id')
                    ->relationship('definition', 'label')
                    ->label('Type de Maintenance')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'À faire',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                    ])
                    ->label('Statut')
                    ->required()
                    ->default('pending'),
                DatePicker::make('scheduled_at')
                    ->label('Date Prévue')
                    ->required(),
                DatePicker::make('completed_at')
                    ->label('Date Réalisation'),
                Select::make('performed_by')
                    ->relationship('performer', 'name')
                    ->label('Réalisé par')
                    ->searchable(),
                Textarea::make('notes')
                    ->label('Notes / Observations')
                    ->columnSpanFull(),
            ]);
    }
}
