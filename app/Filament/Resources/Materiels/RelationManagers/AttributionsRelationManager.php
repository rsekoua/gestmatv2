<?php

namespace App\Filament\Resources\Materiels\RelationManagers;

use App\Filament\Actions\RestituerAttributionAction;
use App\Models\Attribution;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'attributions';

    protected static ?string $title = 'Historique des Attributions';

    protected static ?string $recordTitleAttribute = 'numero_decharge_att';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_decharge_att')
            ->columns([
                TextColumn::make('numero_decharge_att')
                    ->label('N° Décharge')
                    ->icon(Heroicon::QrCode)
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Numéro copié!')
                    ->copyMessageDuration(1500),

                TextColumn::make('recipient_name')
                    ->label('Attribué à')
                    ->icon(fn (Attribution $record): Heroicon => $record->isForEmployee() ? Heroicon::User : Heroicon::BuildingOffice2)
                    ->iconColor(fn (Attribution $record): string => $record->isForEmployee() ? 'success' : 'info')
                    ->searchable(['employee.nom', 'employee.prenom', 'service.nom'])
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->leftJoin('employees', 'attributions.employee_id', '=', 'employees.id')
                            ->leftJoin('services', 'attributions.service_id', '=', 'services.id')
                            ->orderBy(\DB::raw('COALESCE(employees.nom, services.nom)'), $direction);
                    })
                    ->description(fn (Attribution $record): string => $record->isForEmployee()
                        ? ($record->employee?->service?->nom ?? 'Aucun service')
                        : ($record->service?->responsable ? "Chef: {$record->service->responsable}" : 'Chef non défini')
                    )
                    ->wrap()
                    ->url(function ($record): string {
                        if ($record->isForEmployee() && $record->employee) {
                            return route('filament.admin.resources.employees.view', ['record' => $record->employee]);
                        }
                        if ($record->isForService() && $record->service) {
                            return route('filament.admin.resources.services.view', ['record' => $record->service]);
                        }

                        return '#';
                    }),

                TextColumn::make('date_attribution')
                    ->label('Date Attribution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon(Heroicon::Calendar)
                    ->tooltip(fn ($record): string => $record->date_attribution->diffForHumans()),

                TextColumn::make('date_restitution')
                    ->label('Date Restitution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon(Heroicon::Calendar)
                    ->placeholder('En cours')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state): string => $state ? $state->format('d/m/Y') : 'En cours')
                    ->tooltip(fn ($record): ?string => $record->date_restitution?->diffForHumans()),

                TextColumn::make('duration_in_days')
                    ->label('Durée')
                    ->suffix(' jours')
                    ->icon(Heroicon::Clock)
                    ->iconColor('gray')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state < 30 => 'success',
                        $state < 180 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('etat_fonctionnel_res')
                    ->label('État Retour')
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
                        'defauts_mineurs' => 'Défauts mineurs',
                        'dysfonctionnements' => 'Dysfonctionnements',
                        'hors_service' => 'Hors service',
                        default => '—',
                    })
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('decision_res')
                    ->label('Décision')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'remis_en_stock' => 'success',
                        'a_reparer' => 'warning',
                        'rebut' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'remis_en_stock' => 'Remis en stock',
                        'a_reparer' => 'À réparer',
                        'rebut' => 'Rebut',
                        default => '—',
                    })
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Statut')
                    ->placeholder('Toutes')
                    ->trueLabel('Actives')
                    ->falseLabel('Clôturées')
                    ->queries(
                        true: fn (Builder $query) => $query->active(),
                        false: fn (Builder $query) => $query->closed(),
                    ),
            ])
            ->headerActions([
                // On peut ajouter une action pour créer une attribution depuis le matériel
            ])
            ->recordActions([

                RestituerAttributionAction::make(),
                ViewAction::make()
                    ->iconButton()
                    ->url(fn ($record): string => route('filament.admin.resources.attributions.view', ['record' => $record])),
            ])

            ->emptyStateHeading('Aucune attribution')
            ->emptyStateDescription('Ce matériel n\'a jamais été attribué.')
            ->emptyStateIcon(Heroicon::ArrowsRightLeft)
            ->defaultSort('date_attribution', 'desc')
            ->paginated([10, 25, 50]);
    }
}
