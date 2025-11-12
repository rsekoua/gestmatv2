<?php

namespace Database\Seeders;

use App\Models\MaterielType;
use Illuminate\Database\Seeder;

class MaterielTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'nom' => 'Ordinateur Portable',
                'description' => 'Ordinateur portable pour utilisation mobile',
            ],
            [
                'nom' => 'Ordinateur Bureau',
                'description' => 'Ordinateur de bureau fixe',
            ],
            [
                'nom' => 'Imprimante',
                'description' => 'Imprimante laser ou jet d\'encre',
            ],
            [
                'nom' => 'Écran',
                'description' => 'Moniteur externe',
            ],
            [
                'nom' => 'Smartphone',
                'description' => 'Téléphone intelligent',
            ],
            [
                'nom' => 'Tablette',
                'description' => 'Tablette tactile',
            ],
            [
                'nom' => 'Vidéoprojecteur',
                'description' => 'Projecteur pour présentations',
            ],
            [
                'nom' => 'Serveur',
                'description' => 'Serveur informatique',
            ],
            [
                'nom' => 'Switch',
                'description' => 'Commutateur réseau',
            ],
            [
                'nom' => 'Routeur',
                'description' => 'Routeur réseau',
            ],
            [
                'nom' => 'Autre',
                'description' => 'Autre type de matériel',
            ],
        ];

        foreach ($types as $type) {
            MaterielType::firstOrCreate(
                ['nom' => $type['nom']],
                ['description' => $type['description']]
            );
        }
    }
}
