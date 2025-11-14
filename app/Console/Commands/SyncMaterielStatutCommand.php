<?php

namespace App\Console\Commands;

use App\Models\Attribution;
use App\Models\Materiel;
use Illuminate\Console\Command;

class SyncMaterielStatutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materiel:sync-statut {--dry-run : Afficher les incohérences sans les corriger}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les statuts des matériels avec leurs attributions actives';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Vérification des incohérences de statut...');

        $dryRun = $this->option('dry-run');

        // 1. Vérifier les matériels avec attribution active mais statut != 'attribué'
        $this->checkMaterialsWithActiveAttribution($dryRun);

        // 2. Vérifier les matériels avec statut 'attribué' mais sans attribution active
        $this->checkMaterialsWithoutActiveAttribution($dryRun);

        $this->newLine();
        $this->info('✓ Synchronisation terminée avec succès.');

        return self::SUCCESS;
    }

    /**
     * Vérifier et corriger les matériels avec attribution active mais statut incorrect.
     */
    protected function checkMaterialsWithActiveAttribution(bool $dryRun): void
    {
        $this->info('');
        $this->info('1. Matériels avec attribution active mais statut incorrect...');

        $materielsWithIssues = Materiel::whereHas('activeAttribution')
            ->where('statut', '!=', 'attribué')
            ->get();

        if ($materielsWithIssues->isEmpty()) {
            $this->comment('   Aucune incohérence trouvée.');

            return;
        }

        $this->warn("   {$materielsWithIssues->count()} incohérence(s) trouvée(s).");

        foreach ($materielsWithIssues as $materiel) {
            $attribution = $materiel->activeAttribution;
            $recipient = $attribution->isForEmployee()
                ? "employé {$attribution->employee->full_name}"
                : "service {$attribution->service->nom}";

            $this->line("   - Matériel {$materiel->numero_serie} ({$materiel->nom})");
            $this->line("     Statut actuel: {$materiel->statut}");
            $this->line("     Attribué à: {$recipient} depuis le {$attribution->date_attribution->format('d/m/Y')}");

            if (! $dryRun) {
                Materiel::withoutEvents(function () use ($materiel) {
                    $materiel->update(['statut' => 'attribué']);
                });
                $this->info("     ✓ Statut corrigé à 'attribué'");
            } else {
                $this->comment("     → Devrait être corrigé à 'attribué' (mode dry-run)");
            }
        }
    }

    /**
     * Vérifier et corriger les matériels avec statut 'attribué' mais sans attribution active.
     */
    protected function checkMaterialsWithoutActiveAttribution(bool $dryRun): void
    {
        $this->info('');
        $this->info('2. Matériels avec statut "attribué" mais sans attribution active...');

        $materielsWithIssues = Materiel::where('statut', 'attribué')
            ->whereDoesntHave('activeAttribution')
            ->get();

        if ($materielsWithIssues->isEmpty()) {
            $this->comment('   Aucune incohérence trouvée.');

            return;
        }

        $this->warn("   {$materielsWithIssues->count()} incohérence(s) trouvée(s).");

        foreach ($materielsWithIssues as $materiel) {
            // Vérifier si le matériel a des attributions passées
            $lastAttribution = Attribution::where('materiel_id', $materiel->id)
                ->whereNotNull('date_restitution')
                ->orderBy('date_restitution', 'desc')
                ->first();

            $this->line("   - Matériel {$materiel->numero_serie} ({$materiel->nom})");
            $this->line("     Statut actuel: {$materiel->statut}");

            if ($lastAttribution) {
                $newStatus = match ($lastAttribution->decision_res) {
                    'remis_en_stock' => 'disponible',
                    'a_reparer' => 'en_maintenance',
                    'rebut' => 'rebuté',
                    default => 'disponible',
                };

                $this->line("     Dernière restitution: {$lastAttribution->date_restitution->format('d/m/Y')}");
                $this->line("     Décision de restitution: {$lastAttribution->decision_res}");

                if (! $dryRun) {
                    Materiel::withoutEvents(function () use ($materiel, $newStatus) {
                        $materiel->update(['statut' => $newStatus]);
                    });
                    $this->info("     ✓ Statut corrigé à '{$newStatus}'");
                } else {
                    $this->comment("     → Devrait être corrigé à '{$newStatus}' (mode dry-run)");
                }
            } else {
                $this->line("     Aucune attribution dans l'historique");

                if (! $dryRun) {
                    Materiel::withoutEvents(function () use ($materiel) {
                        $materiel->update(['statut' => 'disponible']);
                    });
                    $this->info("     ✓ Statut corrigé à 'disponible'");
                } else {
                    $this->comment("     → Devrait être corrigé à 'disponible' (mode dry-run)");
                }
            }
        }
    }
}
