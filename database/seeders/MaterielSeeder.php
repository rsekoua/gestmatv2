<?php

namespace Database\Seeders;

use App\Models\Materiel;
use App\Models\MaterielType;
use Illuminate\Database\Seeder;

class MaterielSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laptopType = MaterielType::where('nom', 'Ordinateur Portable')->first();
        $desktopType = MaterielType::where('nom', 'Ordinateur Bureau')->first();

        $materiels = [
            [
                'materiel_type_id' => $laptopType?->id,
                'marque' => 'Dell',
                'modele' => 'Latitude 5520',
                'numero_serie' => 'DELL-LAT-001',
                'processor' => 'Intel Core i7-11850H',
                'ram_size_gb' => 16,
                'storage_size_gb' => 512,
                'screen_size' => 15.6,
                'purchase_date' => now()->subMonths(6),
                'acquision' => 'Achat',
                'statut' => 'disponible',
                'etat_physique' => 'excellent',
                'notes' => 'Ordinateur portable haute performance pour développeurs',
            ],
            [
                'materiel_type_id' => $laptopType?->id,
                'marque' => 'HP',
                'modele' => 'EliteBook 840 G8',
                'numero_serie' => 'HP-ELI-002',
                'processor' => 'Intel Core i5-1135G7',
                'ram_size_gb' => 16,
                'storage_size_gb' => 256,
                'screen_size' => 14.0,
                'purchase_date' => now()->subMonths(12),
                'acquision' => 'Achat',
                'statut' => 'disponible',
                'etat_physique' => 'bon',
                'notes' => 'Ordinateur portable léger pour déplacements',
            ],
            [
                'materiel_type_id' => $desktopType?->id,
                'marque' => 'Lenovo',
                'modele' => 'ThinkCentre M90t',
                'numero_serie' => 'LEN-TC-003',
                'processor' => 'Intel Core i7-10700',
                'ram_size_gb' => 32,
                'storage_size_gb' => 1000,
                'screen_size' => 17.0,
                'purchase_date' => now()->subMonths(18),
                'acquision' => 'Achat',
                'statut' => 'disponible',
                'etat_physique' => 'bon',
                'notes' => 'Station de travail pour tâches intensives',
            ],
            [
                'materiel_type_id' => $desktopType?->id,
                'marque' => 'Dell',
                'modele' => 'OptiPlex 7090',
                'numero_serie' => 'DELL-OPT-004',
                'processor' => 'Intel Core i5-10500',
                'ram_size_gb' => 16,
                'storage_size_gb' => 512,
                'screen_size' => 14.0,
                'purchase_date' => now()->subMonths(9),
                'acquision' => 'Achat',
                'statut' => 'disponible',
                'etat_physique' => 'excellent',
                'notes' => 'Ordinateur de bureau standard',
            ],
            [
                'materiel_type_id' => $laptopType?->id,
                'marque' => 'Asus',
                'modele' => 'ZenBook 14',
                'numero_serie' => 'ASUS-ZEN-005',
                'processor' => 'AMD Ryzen 7 5800H',
                'ram_size_gb' => 16,
                'storage_size_gb' => 512,
                'screen_size' => 14.0,
                'purchase_date' => now()->subMonths(4),
                'acquision' => 'Achat',
                'statut' => 'disponible',
                'etat_physique' => 'excellent',
                'notes' => 'Ordinateur portable ultraléger',
            ],
        ];

        foreach ($materiels as $materiel) {
            Materiel::firstOrCreate(
                ['numero_serie' => $materiel['numero_serie']],
                $materiel
            );
        }
    }
}
