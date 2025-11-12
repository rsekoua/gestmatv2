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
                'nom' => 'Chargeur/Câble alimentation',
                'description' => 'Chargeur ou câble d\'alimentation secteur',
            ],
            [
                'nom' => 'Souris',
                'description' => 'Souris filaire ou sans fil',
            ],
            [
                'nom' => 'Câble réseau',
                'description' => 'Câble Ethernet RJ45',
            ],
            [
                'nom' => 'Sacoche/Housse',
                'description' => 'Sacoche de transport ou housse de protection',
            ],
            [
                'nom' => 'Documentation',
                'description' => 'Manuel utilisateur et documentation',
            ],
            [
                'nom' => 'Clé USB',
                'description' => 'Clé USB de stockage',
            ],
            [
                'nom' => 'Casque audio',
                'description' => 'Casque ou écouteurs',
            ],
            [
                'nom' => 'Webcam',
                'description' => 'Caméra web externe',
            ],
            [
                'nom' => 'Clavier externe',
                'description' => 'Clavier filaire ou sans fil',
            ],
            [
                'nom' => 'Adaptateur',
                'description' => 'Adaptateurs divers (HDMI, VGA, USB-C, etc.)',
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
