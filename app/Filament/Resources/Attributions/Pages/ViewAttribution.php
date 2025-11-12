<?php

namespace App\Filament\Resources\Attributions\Pages;

use App\Filament\Actions\RestituerAttributionAction;
use App\Filament\Resources\Attributions\AttributionResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class ViewAttribution extends ViewRecord
{
    protected static string $resource = AttributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RestituerAttributionAction::make(),
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
                Section::make('Informations Générales')
                    ->description('Détails de l\'attribution')
                    ->icon(Heroicon::DocumentText)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('numero_decharge_att')
                            ->label('Numéro de Décharge d\'Attribution')
                            ->icon(Heroicon::QrCode)
                            ->iconColor('primary')
                            ->size('lg')
                            ->weight(FontWeight::Bold)
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('Numéro copié!')
                            ->copyMessageDuration(1500)
                            ->columnSpanFull(),

                        TextEntry::make('materiel.nom')
                            ->label('Matériel Attribué')
                            ->icon(Heroicon::ComputerDesktop)
                            ->iconColor('info')
                            ->weight(FontWeight::Bold)
                            ->columnSpan(1),

                        TextEntry::make('materiel.numero_serie')
                            ->label('Numéro de Série')
                            ->icon(Heroicon::QrCode)
                            ->iconColor('gray')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->copyMessage('Numéro de série copié!')
                            ->copyMessageDuration(1500)
                            ->columnSpan(1),

                        TextEntry::make('employee.full_name')
                            ->label('Employé Bénéficiaire')
                            ->icon(Heroicon::User)
                            ->iconColor('success')
                            ->weight(FontWeight::Bold)
                            ->columnSpan(1),

                        TextEntry::make('employee.service.nom')
                            ->label('Service')
                            ->icon(Heroicon::BuildingOffice2)
                            ->iconColor('success')
                            ->badge()
                            ->color('success')
                            ->placeholder('Aucun service')
                            ->columnSpan(1),

                        TextEntry::make('date_attribution')
                            ->label('Date d\'Attribution')
                            ->icon(Heroicon::Calendar)
                            ->date('d/m/Y')
                            ->since()
                            ->columnSpan(1),

                        TextEntry::make('duration_in_days')
                            ->label('Durée')
                            ->icon(Heroicon::Clock)
                            ->iconColor('gray')
                            ->suffix(' jours')
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state < 30 => 'success',
                                $state < 180 => 'warning',
                                default => 'danger',
                            })
                            ->columnSpan(1),

                        TextEntry::make('observations_att')
                            ->label('Observations d\'Attribution')
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->placeholder('Aucune observation')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make('Accessoires Associés')
                    ->description('Liste des accessoires attribués')
                    ->icon(Heroicon::CpuChip)
                    ->collapsible()
                    ->collapsed(fn ($record) => $record->accessories->isEmpty())
                    ->visible(fn ($record) => $record->accessories->isNotEmpty())
                    ->schema([
                        TextEntry::make('accessories.nom')
                            ->label('Accessoires')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->icon(Heroicon::CpuChip)
                            ->iconColor('info')
                            ->placeholder('Aucun accessoire')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informations de Restitution')
                    ->description('Détails du retour du matériel')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->collapsible()
                    ->collapsed(fn ($record) => is_null($record->date_restitution))
                    ->visible(fn ($record) => ! is_null($record->date_restitution))
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('numero_decharge_res')
                            ->label('Numéro de Décharge de Restitution')
                            ->icon(Heroicon::QrCode)
                            ->iconColor('success')
                            ->badge()
                            ->color('success')
                            ->copyable()
                            ->copyMessage('Numéro copié!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—')
                            ->columnSpanFull(),

                        TextEntry::make('date_restitution')
                            ->label('Date de Restitution')
                            ->icon(Heroicon::Calendar)
                            ->date('d/m/Y')
                            ->since()
                            ->placeholder('—')
                            ->columnSpan(1),

                        TextEntry::make('etat_general_res')
                            ->label('État Général')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'excellent' => 'success',
                                'bon' => 'success',
                                'moyen' => 'warning',
                                'mauvais' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? '—'))
                            ->placeholder('—')
                            ->columnSpan(1),

                        TextEntry::make('etat_fonctionnel_res')
                            ->label('État Fonctionnel')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'parfait' => 'success',
                                'defauts_mineurs' => 'success',
                                'dysfonctionnements' => 'warning',
                                'hors_service' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'parfait' => 'Parfait',
                                'defauts_mineurs' => 'Défauts Mineurs',
                                'dysfonctionnements' => 'Dysfonctionnements',
                                'hors_service' => 'Hors Service',
                                default => '—',
                            })
                            ->placeholder('—')
                            ->columnSpan(1),

                        TextEntry::make('decision_res')
                            ->label('Décision')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'remis_en_stock' => 'success',
                                'a_reparer' => 'warning',
                                'rebut' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'remis_en_stock' => 'Remis en Stock',
                                'a_reparer' => 'À Réparer',
                                'rebut' => 'Rebut',
                                default => '—',
                            })
                            ->placeholder('—')
                            ->columnSpan(1),

                        TextEntry::make('observations_res')
                            ->label('Observations de Restitution')
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->placeholder('Aucune observation')
                            ->markdown()
                            ->columnSpanFull(),

                        TextEntry::make('dommages_res')
                            ->label('Dommages Constatés')
                            ->icon(Heroicon::ExclamationTriangle)
                            ->iconColor('danger')
                            ->placeholder('Aucun dommage constaté')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make('Métadonnées')
                    ->description('Informations système')
                    ->icon(Heroicon::InformationCircle)
                    ->collapsible()
                    ->collapsed()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->icon(Heroicon::Clock)
                            ->dateTime('d/m/Y à H:i')
                            ->since()
                            ->columnSpan(1),

                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->icon(Heroicon::PencilSquare)
                            ->dateTime('d/m/Y à H:i')
                            ->since()
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
