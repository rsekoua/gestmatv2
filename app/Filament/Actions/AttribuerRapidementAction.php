<?php

namespace App\Filament\Actions;

use App\Filament\Concerns\ManagesAccessories;
use App\Filament\Resources\Attributions\Schemas\AttributionForm;
use App\Models\Attribution;
use App\Models\Materiel;
use Filament\Actions\Action;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class AttribuerRapidementAction
{
    use ManagesAccessories;

    public static function make(): Action
    {
        return Action::make('attribuer_rapidement')
            ->label('Attribuer')
            ->icon(Heroicon::UserPlus)
            ->color('primary')
            ->visible(fn (Materiel $record): bool => $record->statut === 'disponible')
            ->modalHeading(fn (Materiel $record): string => 'Attribuer le matériel')
            ->modalDescription(fn (Materiel $record): string => static::getModalDescription($record))
            ->modalSubmitActionLabel('Créer l\'attribution')
            ->modalWidth('3xl')
            ->modalIcon(Heroicon::DocumentPlus)
            ->modalIconColor('primary')
            ->closeModalByClickingAway(false)
            ->schema(fn (Materiel $record) => AttributionForm::getQuickAttributionSchema($record))
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
                    ? $attribution->employee->full_name
                    : $attribution->service->nom;

                $recipientType = $attribution->isForEmployee() ? 'à l\'employé' : 'au service';

                // Générer l'URL de la décharge PDF
                $pdfUrl = route('attributions.discharge.attribution', $attribution->id);

                // Notification enrichie avec actions
                Notification::make()
                    ->title('Attribution créée avec succès')
                    ->success()
                    ->body("Le matériel **{$record->numero_serie}** a été attribué {$recipientType} **{$recipient}**.")
                    ->actions([
                        NotificationAction::make('view_discharge')
                            ->label('Voir la décharge')
                            ->icon(Heroicon::DocumentText)
                            ->url($pdfUrl)
                            ->openUrlInNewTab(),
                        NotificationAction::make('view_attribution')
                            ->label('Voir l\'attribution')
                            ->icon(Heroicon::Eye)
                            ->url(route('filament.admin.resources.attributions.view', $attribution)),
                    ])
                    ->duration(8000)
                    ->send();
            })
            ->successNotificationTitle('Attribution créée')
            ->after(function () {
                // Rafraîchir la page pour voir le statut mis à jour
                redirect()->refresh();
            });
    }

    /**
     * Get modal description with material details.
     */
    protected static function getModalDescription(Materiel $materiel): string
    {
        $details = [
            "**Type:** {$materiel->materielType->nom}",
            "**N° Série:** {$materiel->numero_serie}",
            "**Marque:** {$materiel->marque} {$materiel->modele}",
        ];

        if ($materiel->materielType->isComputer()) {
            $details[] = '📌 *Les ordinateurs sont attribués aux employés*';
        } else {
            $details[] = '📌 *Les équipements sont attribués aux services*';
        }

        return implode(' • ', $details);
    }
}
