<?php

namespace App\Filament\Actions;

use App\Models\Attribution;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class VoirHistoriqueAttributionsAction
{
    /**
     * Create action for viewing material attribution history
     */
    public static function makeForMateriel(): Action
    {
        return Action::make('voir_historique')
            ->label('Voir l\'historique')
            ->icon(Heroicon::Clock)
            ->color('info')
            ->modalHeading(fn (Model $record): string => "Historique d'attributions - {$record->numero_serie}"
            )
            ->modalWidth('2xl')
            ->schema(function (Model $record): array {
                $attributions = Attribution::where('materiel_id', $record->id)
                    ->with(['employee.service', 'service'])
                    ->orderBy('date_attribution', 'desc')
                    ->get();

                return [
                    Section::make('Statistiques')
                        ->schema([
                            TextEntry::make('total')
                                ->label('Total')
                                ->state(fn (): int => $attributions->count())
                                ->badge()
                                ->color('primary'),

                            TextEntry::make('actives')
                                ->label('Actives')
                                ->state(fn (): int => $attributions->where('date_restitution', null)->count())
                                ->badge()
                                ->color('success'),

                            TextEntry::make('cloturees')
                                ->label('Clôturées')
                                ->state(fn (): int => $attributions->whereNotNull('date_restitution')->count())
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(3),

                    Section::make('Historique complet')
                        ->schema([
                            TextEntry::make('historique')
                                ->label('')
                                ->state(function () use ($attributions): string {
                                    if ($attributions->isEmpty()) {
                                        return 'Aucune attribution trouvée.';
                                    }

                                    return $attributions->map(function (Attribution $attribution): string {
                                        $status = $attribution->isActive() ? '🟢 Active' : '⚫ Clôturée';

                                        // Dynamic recipient display
                                        if ($attribution->isForEmployee()) {
                                            $icon = '👤';
                                            $recipient = $attribution->employee->full_name;
                                            $detail = $attribution->employee->service?->nom ?? 'Sans service';
                                        } else {
                                            $icon = '🏢';
                                            $recipient = $attribution->service->nom;
                                            $detail = $attribution->service->responsable
                                                ? "Chef: {$attribution->service->responsable}"
                                                : 'Chef non défini';
                                        }

                                        $dateAtt = $attribution->date_attribution->format('d/m/Y');
                                        $dateRes = $attribution->date_restitution?->format('d/m/Y') ?? 'En cours';
                                        $duree = $attribution->duration_in_days.' jours';

                                        $decision = match ($attribution->decision_res) {
                                            'remis_en_stock' => '✅ Remis en stock',
                                            'a_reparer' => '🔧 À réparer',
                                            'rebut' => '🗑️ Rebut',
                                            default => '—',
                                        };

                                        return <<<HTML
                                        <div style="border-left: 3px solid #3b82f6; padding-left: 1rem; margin-bottom: 1rem;">
                                            <div style="font-weight: 600; margin-bottom: 0.5rem;">
                                                {$status} | {$attribution->numero_decharge_att}
                                            </div>
                                            <div style="margin-bottom: 0.25rem;">
                                                {$icon} <strong>{$recipient}</strong> ({$detail})
                                            </div>
                                            <div style="margin-bottom: 0.25rem;">
                                                📅 {$dateAtt} → {$dateRes} ({$duree})
                                            </div>
                                            <div>
                                                📋 {$decision}
                                            </div>
                                        </div>
                                        HTML;
                                    })->join('');
                                })
                                ->html(),
                        ]),
                ];
            });
    }

    /**
     * Create action for viewing employee attribution history
     */
    public static function makeForEmployee(): Action
    {
        return Action::make('voir_historique')
            ->label('Voir l\'historique')
            ->icon(Heroicon::Clock)
            ->color('info')
            ->modalHeading(fn (Model $record): string => "Historique d'attributions - {$record->full_name}"
            )
            ->modalWidth('2xl')
            ->schema(function (Model $record): array {
                $attributions = Attribution::where('employee_id', $record->id)
                    ->with(['materiel.materielType'])
                    ->orderBy('date_attribution', 'desc')
                    ->get();

                return [
                    Section::make('Statistiques')
                        ->schema([
                            TextEntry::make('total')
                                ->label('Total')
                                ->state(fn (): int => $attributions->count())
                                ->badge()
                                ->color('primary'),

                            TextEntry::make('actives')
                                ->label('Actives')
                                ->state(fn (): int => $attributions->where('date_restitution', null)->count())
                                ->badge()
                                ->color('success'),

                            TextEntry::make('cloturees')
                                ->label('Clôturées')
                                ->state(fn (): int => $attributions->whereNotNull('date_restitution')->count())
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(3),

                    Section::make('Historique complet')
                        ->schema([
                            TextEntry::make('historique')
                                ->label('')
                                ->state(function () use ($attributions): string {
                                    if ($attributions->isEmpty()) {
                                        return 'Aucune attribution trouvée.';
                                    }

                                    return $attributions->map(function (Attribution $attribution): string {
                                        $status = $attribution->isActive() ? '🟢 Active' : '⚫ Clôturée';
                                        $materiel = $attribution->materiel->numero_serie;
                                        $type = $attribution->materiel->materielType?->nom ?? 'Type inconnu';
                                        $marque = "{$attribution->materiel->marque} {$attribution->materiel->modele}";
                                        $dateAtt = $attribution->date_attribution->format('d/m/Y');
                                        $dateRes = $attribution->date_restitution?->format('d/m/Y') ?? 'En cours';
                                        $duree = $attribution->duration_in_days.' jours';

                                        $decision = match ($attribution->decision_res) {
                                            'remis_en_stock' => '✅ Remis en stock',
                                            'a_reparer' => '🔧 À réparer',
                                            'rebut' => '🗑️ Rebut',
                                            default => '—',
                                        };

                                        return <<<HTML
                                        <div style="border-left: 3px solid #3b82f6; padding-left: 1rem; margin-bottom: 1rem;">
                                            <div style="font-weight: 600; margin-bottom: 0.5rem;">
                                                {$status} | {$attribution->numero_decharge_att}
                                            </div>
                                            <div style="margin-bottom: 0.25rem;">
                                                💻 <strong>{$materiel}</strong> ({$type})
                                            </div>
                                            <div style="margin-bottom: 0.25rem;">
                                                🏷️ {$marque}
                                            </div>
                                            <div style="margin-bottom: 0.25rem;">
                                                📅 {$dateAtt} → {$dateRes} ({$duree})
                                            </div>
                                            <div>
                                                📋 {$decision}
                                            </div>
                                        </div>
                                        HTML;
                                    })->join('');
                                })
                                ->html(),
                        ]),
                ];
            });
    }
}
