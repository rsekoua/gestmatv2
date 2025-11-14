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
        $saf = Service::where('code', 'SAF')->first();
        $form = Service::where('code', 'FORM')->first();
        $igl = Service::where('code', 'IGL')->first();

        $employees = [
            [
                'service_id' => $dsi?->id,
                'nom' => 'Ouattara',
                'prenom' => 'Idriss',
                'emploi' => 'Ingenieur Genie Logiciel',
                'email' => 'iouattara@dap.ci',
                'telephone' => '0612345678',
                'fonction' => 'Cadre',
            ],
            [
                'service_id' => $dsi?->id,
                'nom' => 'Awo ',
                'prenom' => 'Mobio Max',
                'emploi' => 'Analyste de données',
                'email' => 'amobio@dap.ci',
                'telephone' => '0623456789',
                'fonction' => 'Analyste de données',
            ],
            [
                'service_id' => $form?->id,
                'nom' => 'Sekoua',
                'prenom' => 'Roger',
                'emploi' => 'Ingenieur Informatique',
                'email' => 'rsekoua@dap.ci',
                'telephone' => '0102030405',
                'fonction' => 'Cadre',
            ],
            [
                'service_id' => $igl?->id,
                'nom' => 'AMAN',
                'prenom' => 'Venance',
                'emploi' => 'Pharmacien',
                'email' => 'vaman@dap.ci',
                'telephone' => '0645678901',
                'fonction' => 'Chef de service',
            ],
            [
                'service_id' => $saf?->id,
                'nom' => 'Akaffou',
                'prenom' => 'Mothiki Marthe',
                'emploi' => 'Secretaire Médicale',
                'email' => 'mothiki@yahoo.fr',
                'telephone' => '0706050403',
                'fonction' => 'Secretaire',
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
