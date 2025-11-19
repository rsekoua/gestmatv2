<?php

namespace App\Console\Commands;

use App\Models\MaintenanceDefinition;
use App\Models\MaintenanceOperation;
use App\Models\Materiel;
use Illuminate\Console\Command;

class ScheduleMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule preventive maintenance tasks based on definitions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting maintenance scheduling...');

        // 1. Récupérer toutes les définitions actives
        $definitions = MaintenanceDefinition::where('is_active', true)->get();

        foreach ($definitions as $definition) {
            $this->info("Processing definition: {$definition->label} (Every {$definition->frequency_days} days)");

            // 2. Récupérer les matériels concernés par ce type
            $materiels = Materiel::where('materiel_type_id', $definition->materiel_type_id)
                ->where('statut', '!=', 'rebuté') // On ne maintient pas le matériel rebuté
                ->get();

            foreach ($materiels as $materiel) {
                // 3. Vérifier s'il y a déjà une opération en attente pour cette définition
                $pendingOperation = MaintenanceOperation::where('materiel_id', $materiel->id)
                    ->where('maintenance_definition_id', $definition->id)
                    ->pending()
                    ->exists();

                if ($pendingOperation) {
                    // $this->line("  - Materiel {$materiel->nom}: Pending operation exists. Skipping.");
                    continue;
                }

                // 4. Récupérer la dernière opération terminée
                $lastOperation = MaintenanceOperation::where('materiel_id', $materiel->id)
                    ->where('maintenance_definition_id', $definition->id)
                    ->completed()
                    ->latest('completed_at')
                    ->first();

                $shouldSchedule = false;
                $scheduledDate = now();

                if (! $lastOperation) {
                    // Jamais maintenu : on planifie pour aujourd'hui (ou selon une règle de mise en service)
                    // Pour l'instant : on planifie immédiatement si le matériel a plus de X jours ?
                    // Simplification : On planifie pour aujourd'hui
                    $shouldSchedule = true;
                    $this->line("  - Materiel {$materiel->nom}: No history. Scheduling now.");
                } else {
                    // Calculer la prochaine date
                    $nextDate = $lastOperation->completed_at->addDays($definition->frequency_days);
                    if ($nextDate->isPast() || $nextDate->isToday()) {
                        $shouldSchedule = true;
                        $scheduledDate = $nextDate->isPast() ? now() : $nextDate; // Si en retard, on met à aujourd'hui pour l'action
                        $this->line("  - Materiel {$materiel->nom}: Due since {$nextDate->format('Y-m-d')}. Scheduling.");
                    }
                }

                if ($shouldSchedule) {
                    MaintenanceOperation::create([
                        'materiel_id' => $materiel->id,
                        'maintenance_definition_id' => $definition->id,
                        'status' => 'pending',
                        'scheduled_at' => $scheduledDate,
                    ]);
                    $this->info("    -> Created task for {$materiel->nom}");
                }
            }
        }

        $this->info('Maintenance scheduling completed.');
    }
}
