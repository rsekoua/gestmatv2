<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dsi = Service::where('code', 'DSI')->first();
        $drh = Service::where('code', 'DRH')->first();
        $fin = Service::where('code', 'FIN')->first();

        $employees = [
            [
                'service_id' => $dsi?->id,
                'nom' => 'Bernard',
                'prenom' => 'Thomas',
                'emploi' => 'Administrateur Système',
                'email' => 'thomas.bernard@example.com',
                'telephone' => '0612345678',
                'fonction' => 'Cadre',
            ],
            [
                'service_id' => $dsi?->id,
                'nom' => 'Petit',
                'prenom' => 'Sophie',
                'emploi' => 'Développeur',
                'email' => 'sophie.petit@example.com',
                'telephone' => '0623456789',
                'fonction' => 'Technicien',
            ],
            [
                'service_id' => $drh?->id,
                'nom' => 'Moreau',
                'prenom' => 'Julien',
                'emploi' => 'Chargé de Recrutement',
                'email' => 'julien.moreau@example.com',
                'telephone' => '0634567890',
                'fonction' => 'Agent de maîtrise',
            ],
            [
                'service_id' => $drh?->id,
                'nom' => 'Laurent',
                'prenom' => 'Claire',
                'emploi' => 'Assistant RH',
                'email' => 'claire.laurent@example.com',
                'telephone' => '0645678901',
                'fonction' => 'Employé',
            ],
            [
                'service_id' => $fin?->id,
                'nom' => 'Simon',
                'prenom' => 'Marc',
                'emploi' => 'Comptable',
                'email' => 'marc.simon@example.com',
                'telephone' => '0656789012',
                'fonction' => 'Cadre',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::firstOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}
