<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ServiceForm
{
    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du Service')
                    ->description('Renseignez les informations principales du service')
                    ->icon(Heroicon::BuildingOffice2)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom du Service')
                            ->placeholder('Ex: Ressources Humaines')
                            ->helperText('Le nom complet du service (doit être unique)')
                            ->prefixIcon(Heroicon::Tag)
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autocomplete(false)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),

                        TextInput::make('code')
                            ->label('Code du Service')
                            ->placeholder('Ex: RH')
                            ->helperText('Code court et unique pour identifier le service')
                            ->prefixIcon(Heroicon::Tag)
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('responsable')
                            ->label('Responsable du Service')
                            ->placeholder('Ex: Marie Dupont')
                            ->helperText('Nom complet du responsable du service')
                            ->prefixIcon(Heroicon::User)
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
