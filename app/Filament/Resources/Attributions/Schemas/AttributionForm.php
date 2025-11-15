<?php

namespace App\Filament\Resources\Attributions\Schemas;

use App\Models\Accessory;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
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
                    ->description('Sélectionnez le matériel et le destinataire')
                    ->icon(Heroicon::DocumentText)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('numero_decharge_att')
                            ->label('Numéro d\'Attribution')
                            ->disabled()
                            ->visible(false)
                            ->dehydrated(false)
                            ->placeholder('Généré automatiquement')
                            ->helperText('Généré automatiquement à la création')
                            ->columnSpan(1),

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
                            ->live()
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText(fn ($record) => $record
                                ? '⚠️ Le matériel ne peut pas être modifié après la création de l\'attribution'
                                : 'Seuls les matériels disponibles sont affichés'
                            )
                            ->getOptionLabelFromRecordUsing(fn (Materiel $record) => "{$record->nom} ({$record->numero_serie})")
                            ->columnSpan(1),

                        Select::make('employee_id')
                            ->label('Employé')
                            ->relationship('employee', 'nom')
                            ->searchable()
                            ->preload()
                            ->required(function (Get $get): bool {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return false;
                                }

                                $materiel = Materiel::with('materielType')->find($materielId);

                                return $materiel && $materiel->materielType->isComputer();
                            })
                            ->visible(function (Get $get): bool {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return true;
                                }

                                $materiel = Materiel::with('materielType')->find($materielId);

                                return $materiel && $materiel->materielType->isComputer();
                            })
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText(fn ($record) => $record
                                ? '⚠️ L\'employé ne peut pas être modifié après la création'
                                : 'Les ordinateurs sont attribués aux employés'
                            )
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->full_name} - {$record->service?->code}")
                            ->columnSpan(1),

                        Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'nom')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $service = Service::find($state);
                                    $set('responsable_service', $service?->responsable);
                                } else {
                                    $set('responsable_service', null);
                                }
                            })
                            ->required(function (Get $get): bool {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return false;
                                }

                                $materiel = Materiel::with('materielType')->find($materielId);

                                return $materiel && ! $materiel->materielType->isComputer();
                            })
                            ->visible(function (Get $get): bool {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return false;
                                }

                                $materiel = Materiel::with('materielType')->find($materielId);

                                return $materiel && ! $materiel->materielType->isComputer();
                            })
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText(fn ($record) => $record
                                ? '⚠️ Le service ne peut pas être modifié après la création'
                                : 'Les autres équipements sont attribués aux services'
                            )
                            ->getOptionLabelFromRecordUsing(fn (Service $record) => $record->full_name)
                            ->columnSpan(1),

                        TextInput::make('responsable_service')
                            ->label('Responsable du Service')
                            ->helperText('Nom du chef de service (rempli automatiquement)')
                            ->disabled()
                            ->dehydrated()
                            ->visible(function (Get $get): bool {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return false;
                                }

                                $materiel = Materiel::with('materielType')->find($materielId);

                                return $materiel && ! $materiel->materielType->isComputer();
                            })
                            ->columnSpan(1),

                        DatePicker::make('date_attribution')
                            ->label('Date d\'Attribution')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->live()
                            ->minDate(function (Get $get, $record) {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return null;
                                }

                                // Chercher la dernière restitution de ce matériel
                                // Si on est en modification, exclure les restitutions après cette attribution
                                $query = Attribution::where('materiel_id', $materielId)
                                    ->whereNotNull('date_restitution');

                                // En modification, exclure cette attribution et les suivantes
                                if ($record) {
                                    $query->where('date_attribution', '<', $record->date_attribution);
                                }

                                $lastRestitution = $query->orderBy('date_restitution', 'desc')->first();

                                return $lastRestitution ? $lastRestitution->date_restitution : null;
                            })
                            ->helperText(function (Get $get, $record) {
                                $materielId = $get('materiel_id');

                                if (! $materielId) {
                                    return 'Sélectionnez d\'abord un matériel';
                                }

                                // Chercher la dernière restitution de ce matériel
                                $query = Attribution::where('materiel_id', $materielId)
                                    ->whereNotNull('date_restitution');

                                // En modification, exclure cette attribution et les suivantes
                                if ($record) {
                                    $query->where('date_attribution', '<', $record->date_attribution);
                                }

                                $lastRestitution = $query->orderBy('date_restitution', 'desc')->first();

                                if ($lastRestitution) {
                                    return "⚠️ Ce matériel a été restitué le {$lastRestitution->date_restitution->format('d/m/Y')}. La date d'attribution doit être égale ou postérieure à cette date.";
                                }

                                return 'Première attribution de ce matériel';
                            })
                            ->validationMessages([
                                'after_or_equal' => 'La date d\'attribution doit être égale ou postérieure à la dernière restitution de ce matériel.',
                            ])
                            ->columnSpan(1),

                        Textarea::make('observations_att')
                            ->label('Observations d\'Attribution')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notes ou observations concernant l\'attribution'),

                        FileUpload::make('decharge_scannee')
                            ->label('Décharge Scannée')
                            ->helperText('Uploadez la décharge d\'attribution signée (PDF, JPG, PNG - Max 5MB)')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('decharges')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Accessoires')
                    ->description('Sélectionnez les accessoires associés')
                    ->icon(Heroicon::CpuChip)
                    ->collapsible()
                    // ->collapsed()
                    ->schema([
                        ToggleButtons::make('accessories')
                            ->label('Accessoires associés')
                            ->multiple()
                           // ->relationship('accessories', 'nom')
                            ->options(Accessory::pluck('nom', 'id'))
                           // ->searchable()
                           // ->bulkToggleable()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ]),
                    ]),

                Section::make('Restitution')
                    ->description('Informations de restitution (à compléter lors du retour)')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->collapsible()
                    ->columnSpan(2)
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
                            ->maxDate(now())
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
            ])->columns(3);
    }
}
