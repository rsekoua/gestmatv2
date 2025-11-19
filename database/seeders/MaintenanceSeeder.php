<?php

namespace Database\Seeders;

use App\Models\MaintenanceDefinition;
use App\Models\MaterielType;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ordinateurs (Portables et Bureau)
        $computerTypes = MaterielType::whereIn('nom', ['Ordinateur Portable', 'Ordinateur Bureau'])->get();

        foreach ($computerTypes as $type) {
            MaintenanceDefinition::firstOrCreate(
                [
                    'materiel_type_id' => $type->id,
                    'label' => 'Nettoyage Physique & Dépoussiérage',
                ],
                [
                    'description' => 'Nettoyage complet : écran, clavier, et dépoussiérage des ventilateurs.',
                    'frequency_days' => 180, // 6 mois
                    'is_active' => true,
                ]
            );

            MaintenanceDefinition::firstOrCreate(
                [
                    'materiel_type_id' => $type->id,
                    'label' => 'Vérification Système & Mises à jour',
                ],
                [
                    'description' => 'Vérification des mises à jour OS, drivers, et scan antivirus complet.',
                    'frequency_days' => 90, // 3 mois
                    'is_active' => true,
                ]
            );
        }

        // 2. Imprimantes
        $printerType = MaterielType::where('nom', 'Imprimante')->first();
        if ($printerType) {
            MaintenanceDefinition::firstOrCreate(
                [
                    'materiel_type_id' => $printerType->id,
                    'label' => 'Nettoyage Têtes & Calibrage',
                ],
                [
                    'description' => 'Nettoyage des têtes d\'impression, vérification des niveaux, et calibrage.',
                    'frequency_days' => 90, // 3 mois
                    'is_active' => true,
                ]
            );
        }

        // 3. Serveurs
        $serverType = MaterielType::where('nom', 'Serveur')->first();
        if ($serverType) {
            MaintenanceDefinition::firstOrCreate(
                [
                    'materiel_type_id' => $serverType->id,
                    'label' => 'Maintenance Préventive Serveur',
                ],
                [
                    'description' => 'Vérification RAID, logs système, dépoussiérage salle serveur, check onduleur.',
                    'frequency_days' => 30, // 1 mois
                    'is_active' => true,
                ]
            );
        }
    }
}
