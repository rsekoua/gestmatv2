<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Validation\ValidationException;

class EmployeeObserver
{
    /**
     * Handle the Employee "deleting" event.
     */
    public function deleting(Employee $employee): void
    {
        // Vérifier si l'employé a des attributions
        $hasAttributions = $employee->attributions()->exists();

        if ($hasAttributions) {
            // Compter les attributions actives et fermées
            $activeCount = $employee->attributions()->whereNull('date_restitution')->count();
            $totalCount = $employee->attributions()->count();

            if ($activeCount > 0) {
                throw ValidationException::withMessages([
                    'employee' => "Impossible de supprimer cet employé : il a {$activeCount} attribution(s) active(s). Veuillez d'abord restituer tous les matériels.",
                ]);
            }

            throw ValidationException::withMessages([
                'employee' => "Impossible de supprimer cet employé : il a {$totalCount} attribution(s) dans l'historique. Les données d'attribution doivent être préservées.",
            ]);
        }
    }
}
