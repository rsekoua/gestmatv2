<?php

namespace App\Filament\Resources\Attributions\Schemas;

use App\Models\Accessory;
use App\Models\Employee;
use App\Models\Materiel;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AttributionForm
{
    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de l\'Attribution')
                    ->description('Sélectionnez le matériel et l\'employé')
                    ->icon(Heroicon::DocumentText)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Select::make('materiel_id')
                            ->label('Matériel')
                            ->relationship(
                                name: 'materiel',
                                titleAttribute: 'numero_serie',
                                modifyQueryUsing: fn ($query, $record) => $record
                                    ? $query
                                    : $query->where('statut', 'disponible')
                            )
                            ->searchable(['numero_serie', 'marque', 'modele'])
                            ->preload()
                            ->required()
                            ->helperText(fn ($record) => $record
                                ? 'Matériel actuellement attribué'
                                : 'Seuls les matériels disponibles sont affichés'
                            )
                            ->getOptionLabelFromRecordUsing(fn (Materiel $record) => "{$record->nom} ({$record->numero_serie})")
                            ->columnSpan(1),

                        Select::make('employee_id')
                            ->label('Employé')
                            ->relationship('employee', 'nom')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Sélectionnez l\'employé destinataire')
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->full_name} - {$record->service?->code}")
                            ->columnSpan(1),

                        DatePicker::make('date_attribution')
                            ->label('Date d\'Attribution')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        TextInput::make('numero_decharge_att')
                            ->label('Numéro de Décharge d\'Attribution')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Généré automatiquement')
                            ->helperText('Le numéro sera généré automatiquement lors de la création')
                            ->columnSpan(1),

                        Textarea::make('observations_att')
                            ->label('Observations d\'Attribution')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notes ou observations concernant l\'attribution'),
                    ]),

                Section::make('Accessoires')
                    ->description('Sélectionnez les accessoires associés')
                    ->icon(Heroicon::CpuChip)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        CheckboxList::make('accessories')
                            ->label('Accessoires associés')
                            ->relationship('accessories', 'nom')
                            ->options(Accessory::pluck('nom', 'id'))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns([
                                'sm' => 1,
                                'md' => 3,
                            ]),
                    ]),

                Section::make('Restitution')
                    ->description('Informations de restitution (à compléter lors du retour)')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record !== null)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        DatePicker::make('date_restitution')
                            ->label('Date de Restitution')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('Laissez vide si le matériel n\'est pas encore restitué')
                            ->afterOrEqual('date_attribution')
                            ->validationMessages([
                                'after_or_equal' => 'La date de restitution doit être postérieure ou égale à la date d\'attribution.',
                            ])
                            ->live()
                            ->columnSpan(1),

                        TextInput::make('numero_decharge_res')
                            ->label('Numéro de Décharge de Restitution')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Généré automatiquement')
                            ->helperText('Le numéro sera généré automatiquement lors de la restitution')
                            ->columnSpan(1),

                        Select::make('etat_general_res')
                            ->label('État Général')
                            ->options([
                                'excellent' => 'Excellent',
                                'bon' => 'Bon',
                                'moyen' => 'Moyen',
                                'mauvais' => 'Mauvais',
                            ])
                            ->native(false)
                            ->required(fn (Get $get) => filled($get('date_restitution')))
                            ->validationMessages([
                                'required' => 'L\'état général est obligatoire lors de la restitution.',
                            ])
                            ->columnSpan(1),

                        Select::make('etat_fonctionnel_res')
                            ->label('État Fonctionnel')
                            ->options([
                                'parfait' => 'Parfait',
                                'defauts_mineurs' => 'Défauts Mineurs',
                                'dysfonctionnements' => 'Dysfonctionnements',
                                'hors_service' => 'Hors Service',
                            ])
                            ->native(false)
                            ->required(fn (Get $get) => filled($get('date_restitution')))
                            ->validationMessages([
                                'required' => 'L\'état fonctionnel est obligatoire lors de la restitution.',
                            ])
                            ->columnSpan(1),

                        Select::make('decision_res')
                            ->label('Décision')
                            ->options([
                                'remis_en_stock' => 'Remis en Stock',
                                'a_reparer' => 'À Réparer',
                                'rebut' => 'Rebut',
                            ])
                            ->native(false)
                            ->columnSpan(1),

                        Textarea::make('observations_res')
                            ->label('Observations de Restitution')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notes ou observations concernant la restitution')
                            ->required(fn (Get $get) => filled($get('date_restitution')))
                            ->validationMessages([
                                'required' => 'Les observations de restitution sont obligatoires.',
                            ]),

                        Textarea::make('dommages_res')
                            ->label('Dommages Constatés')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Description des dommages éventuels'),
                    ]),
            ]);
    }
}
