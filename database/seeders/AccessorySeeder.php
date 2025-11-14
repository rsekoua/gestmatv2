<?php

namespace Database\Seeders;

use App\Models\Accessory;
use Illuminate\Database\Seeder;

class AccessorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accessories = [
            [
                'nom' => 'Chargeur',
                'description' => 'Chargeur ou câble d\'alimentation secteur',
            ],
            [
                'nom' => 'Souris',
                'description' => 'Souris filaire ou sans fil',
            ],
            [
                'nom' => 'Sacoche/Housse',
                'description' => 'Sacoche de transport ou housse de protection',
            ],
            [
                'nom' => 'Clé USB',
                'description' => 'Clé USB de stockage',
            ],
            [
                'nom' => 'Disque dur externe',
                'description' => 'Disque dur externe pour le stockage',
            ],

        ];

        foreach ($accessories as $accessory) {
            Accessory::firstOrCreate(
                ['nom' => $accessory['nom']],
                ['description' => $accessory['description']]
            );
        }
    }
}
