<?php

namespace App\Filament\Resources\Materiels\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MaterialForm
{
    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification du Matériel')
                    ->description('Informations d\'identification et type de matériel')
                    ->icon(Heroicon::Identification)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Select::make('materiel_type_id')
                            ->label('Type de Matériel')
                            ->placeholder('Sélectionnez un type')
                            ->helperText('Catégorie du matériel (ordinateur, imprimante, etc.)')
                            ->prefixIcon(Heroicon::Tag)
                            ->relationship('materielType', 'nom')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('nom')
                                    ->label('Nom du Type')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->maxLength(500),
                            ])
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),

                        TextInput::make('marque')
                            ->label('Marque')
                            ->placeholder('Ex: HP, Dell, Canon')
                            ->helperText('Marque du fabricant')
                            ->prefixIcon(Heroicon::BuildingOffice)
                            ->maxLength(100)
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('modele')
                            ->label('Modèle')
                            ->placeholder('Ex: EliteBook 840 G8')
                            ->helperText('Référence ou modèle du matériel')
                            ->prefixIcon(Heroicon::Cube)
                            ->maxLength(100)
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('numero_serie')
                            ->label('Numéro de Série')
                            ->placeholder('Ex: SN123456789')
                            ->helperText('Numéro de série unique (obligatoire)')
                            ->prefixIcon(Heroicon::QrCode)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->autocomplete(false)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),
                    ]),

                Section::make('Spécifications Techniques')
                    ->description('Caractéristiques matérielles du matériel')
                    ->icon(Heroicon::CpuChip)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextInput::make('processor')
                            ->label('Processeur')
                            ->placeholder('Ex: Intel Core i7-1165G7')
                            ->helperText('Type et modèle du processeur')
                            ->prefixIcon(Heroicon::CpuChip)
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),

                        TextInput::make('ram_size_gb')
                            ->label('Mémoire RAM (GB)')
                            ->placeholder('Ex: 16')
                            ->helperText('Taille de la RAM en gigaoctets')
                            ->prefixIcon(Heroicon::CircleStack)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1024)
                            ->suffix('GB')
                            ->columnSpan(1),

                        TextInput::make('storage_size_gb')
                            ->label('Stockage (GB)')
                            ->placeholder('Ex: 512')
                            ->helperText('Capacité de stockage en gigaoctets')
                            ->prefixIcon(Heroicon::ServerStack)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10000)
                            ->suffix('GB')
                            ->columnSpan(1),

                        TextInput::make('screen_size')
                            ->label('Taille de l\'Écran')
                            ->placeholder('Ex: 15.6')
                            ->helperText('Diagonale de l\'écran en pouces')
                            ->prefixIcon(Heroicon::ComputerDesktop)
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99.99)
                            ->step(0.1)
                            ->suffix('pouces')
                            ->columnSpan(1),
                    ]),

                Section::make('Acquisition et État')
                    ->description('Informations d\'achat et état actuel du matériel')
                    ->icon(Heroicon::ShoppingCart)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        DatePicker::make('purchase_date')
                            ->label('Date d\'Achat')
                            ->helperText('Date d\'acquisition du matériel (obligatoire)')
                            ->prefixIcon(Heroicon::Calendar)
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->columnSpan(1),

                        TextInput::make('acquision')
                            ->label('Mode d\'Acquisition')
                            ->placeholder('Ex: Achat direct, Don, Leasing')
                            ->helperText('Comment le matériel a été acquis')
                            ->prefixIcon(Heroicon::ShoppingBag)
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpan(1),

                        Select::make('statut')
                            ->label('Statut')
                            ->placeholder('Sélectionnez un statut')
                            ->helperText('État d\'attribution ou de fonctionnement')
                            ->prefixIcon(Heroicon::Signal)
                            ->options([
                                'disponible' => 'Disponible',
                                'attribué' => 'Attribué',
                                'en_panne' => 'En panne',
                                'en_maintenance' => 'En maintenance',
                                'rebuté' => 'Rebuté',
                            ])
                            ->default('disponible')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('etat_physique')
                            ->label('État Physique')
                            ->placeholder('Sélectionnez un état')
                            ->helperText('Condition physique du matériel')
                            ->prefixIcon(Heroicon::Wrench)
                            ->options([
                                'excellent' => 'Excellent',
                                'bon' => 'Bon',
                                'moyen' => 'Moyen',
                                'mauvais' => 'Mauvais',
                            ])
                            ->default('bon')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                    ]),

                Section::make('Notes Complémentaires')
                    ->description('Informations additionnelles et observations')
                    ->icon(Heroicon::DocumentText)
                    ->columns(1)
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Informations complémentaires, historique, observations...')
                            ->helperText('Notes libres sur le matériel')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
