<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::PencilSquare)->label('')
                ->defaultSize('2xl'),
        ];
    }

    /**
     * @throws \Exception
     */
    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du Service')
                    ->description('Détails du service')
                    ->icon(Heroicon::BuildingOffice2)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Nom du Service')
                            ->icon(Heroicon::Tag)
                            ->iconColor('primary')
                            ->size('lg')
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                        TextEntry::make('code')
                            ->label('Code du Service')
                            ->icon(Heroicon::Tag)
                            ->badge()
                            ->color('info')
                            ->placeholder('Non défini')
                            ->columnSpan(1),

                        TextEntry::make('responsable')
                            ->label('Responsable')
                            ->icon(Heroicon::User)
                            ->iconColor('success')
                            ->placeholder('Non défini')
                            ->columnSpan(1),

                        TextEntry::make('employees_count')
                            ->label('Nombre d\'employés')
                            ->icon(Heroicon::Users)
                            ->iconColor('gray')
                            ->getStateUsing(fn ($record) => $record->employees()->count())
                            ->suffix(fn ($state) => $state > 1 ? ' employés' : ' employé')
                            ->columnSpan(1),

                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->icon(Heroicon::Clock)
                            ->dateTime('d/m/Y à H:i')
                            ->since()
                            ->columnSpan(1),

                        TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->icon(Heroicon::PencilSquare)
                            ->dateTime('d/m/Y à H:i')
                            ->since()
                            ->columnSpan(1),
                    ]),
            ])->columns(2);
    }
}
