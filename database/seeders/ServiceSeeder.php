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
                'responsable' => 'M. Thierry BOMO',
            ],
            [
                'nom' => 'Service Administratif et Financier',
                'code' => 'SAF',
                'responsable' => 'M. Alain PEPLA',
            ],
            [
                'nom' => 'Formation',
                'code' => 'FORM',
                'responsable' => 'Dr Regina KOUASSI',
            ],
            [
                'nom' => 'Information et Gestion Logistique',
                'code' => 'IGL',
                'responsable' => 'Dr Venance AMAN',
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
