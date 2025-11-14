<?php

namespace App\Filament\Resources\Materiels\Pages;

use App\Filament\Actions\AttribuerRapidementAction;
use App\Filament\Actions\VoirHistoriqueAttributionsAction;
use App\Filament\Resources\Materiels\MaterialResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class ViewMaterial extends ViewRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AttribuerRapidementAction::make(),
            VoirHistoriqueAttributionsAction::makeForMateriel(),
            EditAction::make()
                ->icon(Heroicon::PencilSquare),
        ];
    }

    /**
     * @throws \Exception
     */
    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->description('Informations d\'identification du matériel')
                    ->icon(Heroicon::Identification)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                        ->columnSpan(2)
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Désignation')
                            ->icon(Heroicon::ComputerDesktop)
                            ->iconColor('primary')
                            ->size('lg')
                          //  ->weight(FontWeight::Bold)
                            ->getStateUsing(fn ($record) => $record->nom)
                            ->columnSpanFull(),

                        TextEntry::make('numero_serie')
                            ->label('Numéro de Série')
                            ->icon(Heroicon::QrCode)
//                            ->iconColor('primary')
//                            ->badge()
//                            ->color('primary')
                           // ->weight(FontWeight::Bold)
                            ->placeholder('Non défini')
                            ->copyable()
                            ->copyMessage('Numéro de série copié!')
                            ->copyMessageDuration(1500),
                            //->columnSpan(1),


                        TextEntry::make('materielType.nom')
                            ->label('Type de Matériel')
                            ->icon(Heroicon::Tag)
                           // ->iconColor('info')
                          //  ->weight(FontWeight::Bold)
                            //->badge()
                           // ->color('info')
                            ->placeholder('Non défini')
                            ->columnSpan(1),


                        TextEntry::make('marque')
                            ->label('Marque')
                            ->icon(Heroicon::BuildingOffice)
                            ->iconColor('gray')
                            ->placeholder('Non définie')
                           // ->weight(FontWeight::Bold)
                            ->columnSpan(1),

                        TextEntry::make('modele')
                            ->label('Modèle')
                            ->icon(Heroicon::Cube)
                            ->iconColor('gray')
                          //  ->weight(FontWeight::Bold)
                           // ->placeholder('Non défini')
                            ->columnSpan(1),
                    ]),

                Section::make('Spécifications Techniques')
                    ->description('Caractéristiques matérielles')
                    ->icon(Heroicon::CpuChip)
                    ->columnSpan(2)
                    ->visible(fn ($record): bool => $record->materielType->isComputer())
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('processor')
                            ->label('Processeur')
                            ->icon(Heroicon::CpuChip)
                            ->iconColor('info')
                            ->placeholder('Non renseigné'),
//                            ->columnSpan(1),

                        TextEntry::make('ram_size_gb')
                            ->label('Mémoire RAM')
                            ->icon(Heroicon::CircleStack)
                            ->iconColor('success')
                            ->placeholder('Non renseignée')
                            ->suffix(' GB'),
//                            ->columnSpan(1),

                        TextEntry::make('storage_size_gb')
                            ->label('Stockage')
                            ->icon(Heroicon::ServerStack)
                            ->iconColor('warning')
                            ->placeholder('Non renseigné')
                            ->suffix(' GB')
                            ->columnSpan(1),

                        TextEntry::make('screen_size')
                            ->label('Taille de l\'Écran')
                            ->icon(Heroicon::ComputerDesktop)
                            ->iconColor('primary')
                            ->placeholder('Non renseignée')
                            ->suffix(' pouces')
                            ->columnSpan(1),

                        TextEntry::make('specifications_summary')
                            ->label('Résumé des Spécifications')
                            ->icon(Heroicon::ListBullet)
                            ->placeholder('Aucune spécification')
                            ->getStateUsing(fn ($record) => $record->specifications_summary)
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                            ]),
                    ]),

                Section::make('Acquisition et État')
                    ->description('Informations d\'achat et état actuel')
                    ->icon(Heroicon::ShoppingCart)
                    ->columnSpan(4)
                    ->columns([
                        'sm' => 1,
                        'md' => 6,
                    ])
                    ->schema([
                        TextEntry::make('purchase_date')
                            ->label('Date d\'Achat')
                            ->icon(Heroicon::Calendar)
                            ->date('d/m/Y')
                            ->since()
                            ->placeholder('Non renseignée')
                            ->columnSpan(1),

                        TextEntry::make('acquision')
                            ->label('Mode d\'Acquisition')
                            ->icon(Heroicon::ShoppingBag)
                            ->iconColor('info')
                            ->placeholder('Non renseigné')
                            ->columnSpan(1),

                        TextEntry::make('statut')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'disponible' => 'success',
                                'attribué' => 'warning',
                                'en_panne' => 'danger',
                                'en_maintenance' => 'info',
                                'rebuté' => 'gray',
                                default => 'gray',
                            })
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'disponible' => Heroicon::CheckCircle,
                                'attribué' => Heroicon::UserCircle,
                                'en_panne' => Heroicon::XCircle,
                                'en_maintenance' => Heroicon::Wrench,
                                'rebuté' => Heroicon::ArchiveBox,
                                default => Heroicon::QuestionMarkCircle,
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'en_panne' => 'En panne',
                                'en_maintenance' => 'En maintenance',
                                'attribué' => 'Attribué',
                                'disponible' => 'Disponible',
                                'rebuté' => 'Rebuté',
                                default => ucfirst($state),
                            })
                            ->columnSpan(1),

                        TextEntry::make('etat_physique')
                            ->label('État Physique')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'excellent' => 'success',
                                'bon' => 'success',
                                'moyen' => 'warning',
                                'mauvais' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->columnSpan(1),
                        TextEntry::make('amortissement_status')
                            ->label('Amortissement')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Amorti' => 'danger',
                                'Actif' => 'success',
                                default => 'gray',
                            })
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'Amorti' => Heroicon::ExclamationTriangle,
                                'Actif' => Heroicon::CheckCircle,
                                default => Heroicon::QuestionMarkCircle,
                            })
                            ->getStateUsing(fn ($record): string => $record->amortissement_status)
                            ->tooltip('Amorti après 3 ans pour les ordinateurs')
                            ->columnSpan(1),
                        TextEntry::make('attributions_count')
                            ->label('Total Attributions')
                            ->icon(Heroicon::UserGroup)
                            ->iconColor('gray')
                            ->getStateUsing(fn ($record) => $record->attributions()->count())
                            ->suffix(' attributions')
                            ->columnSpan(1),
                    ]),

//                Section::make('Statistiques et Métadonnées')
//                    ->description('Amortissement et historique')
//                    ->icon(Heroicon::ChartBar)
//                    ->columns([
//                        'sm' => 1,
//                        'md' => 4,
//                    ])
//                    ->schema([
//                        TextEntry::make('amortissement_status')
//                            ->label('Amortissement')
//                            ->badge()
//                            ->color(fn (string $state): string => match ($state) {
//                                'Amorti' => 'danger',
//                                'Actif' => 'success',
//                                default => 'gray',
//                            })
//                            ->icon(fn (string $state): Heroicon => match ($state) {
//                                'Amorti' => Heroicon::ExclamationTriangle,
//                                'Actif' => Heroicon::CheckCircle,
//                                default => Heroicon::QuestionMarkCircle,
//                            })
//                            ->getStateUsing(fn ($record): string => $record->amortissement_status)
//                            ->tooltip('Amorti après 3 ans pour les ordinateurs')
//                            ->columnSpan(1),
//
//                        TextEntry::make('attributions_count')
//                            ->label('Total Attributions')
//                            ->icon(Heroicon::UserGroup)
//                            ->iconColor('gray')
//                            ->getStateUsing(fn ($record) => $record->attributions()->count())
//                            ->suffix(' attributions')
//                            ->columnSpan(1),
//
//                        TextEntry::make('created_at')
//                            ->label('Créé le')
//                            ->icon(Heroicon::Clock)
//                            ->dateTime('d/m/Y à H:i')
//                            ->since()
//                            ->columnSpan(1),
//
//                        TextEntry::make('updated_at')
//                            ->label('Modifié le')
//                            ->icon(Heroicon::PencilSquare)
//                            ->dateTime('d/m/Y à H:i')
//                            ->since()
//                            ->columnSpan(1),
//                    ]),

                Section::make('Notes et Informations Complémentaires')
                    ->description('Observations et historique')
                    ->icon(Heroicon::DocumentText)
                    ->columns(1)
                    ->collapsed()
                    ->columnSpan(4)
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notes')
                            //->icon(Heroicon::ClipboardDocumentList)
                            ->placeholder('Aucune note')
                            ->markdown()
                            //->columnSpanFull(),
                    ]),

            ])->columns(4);
    }
}
