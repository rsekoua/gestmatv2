<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EmployeeForm
{
    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Personnelles')
                    ->description('Renseignez les informations d\'identité de l\'employé')
                    ->icon(Heroicon::UserCircle)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom')
                            ->placeholder('Ex: Dupont')
                            ->helperText('Nom de famille de l\'employé')
                            ->prefixIcon(Heroicon::User)
                            ->required()
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('prenom')
                            ->label('Prénom')
                            ->placeholder('Ex: Marie')
                            ->helperText('Prénom de l\'employé')
                            ->prefixIcon(Heroicon::User)
                            ->required()
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Ex: marie.dupont@entreprise.com')
                            ->helperText('Adresse email professionnelle (doit être unique)')
                            ->prefixIcon(Heroicon::Envelope)
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),

                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->placeholder('Ex: 0102030405')
                            ->helperText('Numéro de téléphone professionnel')
                            ->prefix('+225')
                            ->prefixIcon(Heroicon::Phone)
                            ->tel()
                            ->maxLength(10)
                            ->autocomplete(false)
                            ->columnSpan(1),
                    ]),

                Section::make('Informations Professionnelles')
                    ->description('Détails sur le poste et l\'affectation de l\'employé')
                    ->icon(Heroicon::Briefcase)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Select::make('service_id')
                            ->label('Service')
                            ->placeholder('Sélectionnez un service')
                            ->helperText('Service auquel l\'employé est rattaché')
                            ->prefixIcon(Heroicon::BuildingOffice2)
                            ->relationship('service', 'nom')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nom')
                                    ->label('Nom du Service')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->label('Code du Service')
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])->maxLength(10),
                                //                                    ->uppercase(),
                                TextInput::make('responsable')
                                    ->label('Responsable du Service')
                                    ->maxLength(255),
                            ])
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),

                        TextInput::make('emploi')
                            ->label('Emploi')
                            ->placeholder('Ex: Ingénieur')
                            ->helperText('Intitulé de l\'emploi ou du poste')
                            ->prefixIcon(Heroicon::Briefcase)
                            ->maxLength(255)
                            ->required()
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('fonction')
                            ->label('Fonction')
                            ->placeholder('Ex: Responsable technique')
                            ->helperText('Fonction ou rôle dans l\'organisation')
                            ->prefixIcon(Heroicon::Identification)
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
