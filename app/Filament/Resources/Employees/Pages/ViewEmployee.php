<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                Section::make('Informations Personnelles')
                    ->description('Identité de l\'employé')
                    ->icon(Heroicon::UserCircle)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Nom Complet')
                            ->icon(Heroicon::User)
                            ->iconColor('primary')
                            ->size('lg')
                            ->weight(FontWeight::Bold)
                            ->getStateUsing(fn ($record) => $record->full_name)
                            ->columnSpanFull(),

                        TextEntry::make('nom')
                            ->label('Nom')
                            ->icon(Heroicon::User)
                            ->iconColor('gray')
                            ->placeholder('Non défini')
                            ->columnSpan(1),

                        TextEntry::make('prenom')
                            ->label('Prénom')
                            ->icon(Heroicon::User)
                            ->iconColor('gray')
                            ->placeholder('Non défini')
                            ->columnSpan(1),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon(Heroicon::Envelope)
                            ->iconColor('info')
                            ->placeholder('Non défini')
                            ->copyable()
                            ->copyMessage('Email copié!')
                            ->copyMessageDuration(1500)
                            ->columnSpan(1),

                        TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->icon(Heroicon::Phone)
                            ->iconColor('success')
                            ->placeholder('Non défini')
                            ->copyable()
                            ->copyMessage('Téléphone copié!')
                            ->copyMessageDuration(1500)
                            ->columnSpan(1),
                    ]),

                Section::make('Informations Professionnelles')
                    ->description('Poste et affectation')
                    ->icon(Heroicon::Briefcase)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('service.nom')
                            ->label('Service')
                            ->icon(Heroicon::BuildingOffice2)
                            ->iconColor('success')
                            ->badge()
                            ->color('success')
                            ->placeholder('Non assigné')
                            ->columnSpan(1),

                        TextEntry::make('service.code')
                            ->label('Code Service')
                            ->icon(Heroicon::Tag)
                            ->badge()
                            ->color('info')
                            ->placeholder('—')
                            ->columnSpan(1),

                        TextEntry::make('emploi')
                            ->label('Emploi')
                            ->icon(Heroicon::Briefcase)
                            ->iconColor('warning')
                            ->placeholder('Non défini')
                            ->columnSpan(1),

                        TextEntry::make('fonction')
                            ->label('Fonction')
                            ->icon(Heroicon::Identification)
                            ->iconColor('primary')
                            ->placeholder('Non défini')
                            ->columnSpan(1),
                    ]),

                Section::make('Statistiques')
                    ->description('Informations sur les attributions')
                    ->icon(Heroicon::ChartBar)
                    ->columns([
                        'sm' => 1,
                        'md' => 4,
                    ])
                    ->schema([
                        TextEntry::make('attributions_count')
                            ->label('Total Attributions')
                            ->icon(Heroicon::Cube)
                            ->iconColor('gray')
                            ->getStateUsing(fn ($record) => $record->attributions()->count())
                            ->suffix(' attributions')
                            ->columnSpan(1),

                        TextEntry::make('active_attributions_count')
                            ->label('Attributions Actives')
                            ->icon(Heroicon::CheckCircle)
                            ->iconColor('success')
                            ->getStateUsing(fn ($record) => $record->activeAttributions()->count())
                            ->suffix(' actives')
                            ->badge()
                            ->color('success')
                            ->columnSpan(1),

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
