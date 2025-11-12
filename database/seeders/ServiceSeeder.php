<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nom' => 'Direction des Systèmes d\'Information',
                'code' => 'DSI',
                'responsable' => 'Jean Dupont',
            ],
            [
                'nom' => 'Direction des Ressources Humaines',
                'code' => 'DRH',
                'responsable' => 'Marie Martin',
            ],
            [
                'nom' => 'Direction Financière',
                'code' => 'FIN',
                'responsable' => 'Pierre Dubois',
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['code' => $service['code']],
                [
                    'nom' => $service['nom'],
                    'responsable' => $service['responsable'],
                ]
            );
        }
    }
}
