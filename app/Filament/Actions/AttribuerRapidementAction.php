<?php

namespace App\Filament\Actions;

use App\Filament\Concerns\ManagesAccessories;
use App\Filament\Resources\Attributions\Schemas\AttributionForm;
use App\Models\Attribution;
use App\Models\Materiel;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class AttribuerRapidementAction
{
    use ManagesAccessories;

    public static function make(): Action
    {
        return Action::make('attribuer_rapidement')
            ->label('Attribuer rapidement')
            ->icon(Heroicon::Bolt)
            ->color('success')
            ->visible(fn (Materiel $record): bool => $record->statut === 'disponible')
            ->requiresConfirmation()
            ->modalHeading('Attribuer le matériel')
            ->modalDescription(fn (Materiel $record): string => "Matériel : {$record->numero_serie} ({$record->marque} {$record->modele})"
            )
            ->modalSubmitActionLabel('Créer l\'attribution')
            ->modalWidth('2xl')
            ->Schema(fn (Materiel $record) => AttributionForm::getQuickAttributionSchema($record))
            ->action(function (Materiel $record, array $data): void {
                // Extract accessories from data
                $accessories = $data['accessories'] ?? [];
                unset($data['accessories']);

                // Create the attribution
                $attribution = Attribution::create([
                    'materiel_id' => $record->id,
                    'employee_id' => $data['employee_id'] ?? null,
                    'service_id' => $data['service_id'] ?? null,
                    'responsable_service' => $data['responsable_service'] ?? null,
                    'date_attribution' => $data['date_attribution'],
                    'observations_att' => $data['observations_att'] ?? null,
                ]);

                // Utiliser le trait pour attacher les accessoires avec les données pivot
                (new self)->attachAccessories($attribution, $accessories);

                // Préparer le message de notification
                $recipient = $attribution->isForEmployee()
                    ? "à {$attribution->employee->full_name}"
                    : "au service {$attribution->service->nom}";

                Notification::make()
                    ->title('Attribution créée')
                    ->success()
                    ->body("Le matériel {$record->numero_serie} a été attribué {$recipient}.")
                    ->send();
            });
    }
}
