<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        $this->clearCache();
    }

    /**
     * Clear all employee-related caches
     */
    protected function clearCache(): void
    {
        Cache::forget('dashboard.overview.stats');
        Cache::forget('services.options');
        Cache::forget('navigation.badge.employees');
        Cache::forget('navigation.badge.employees.tooltip');
    }
}
