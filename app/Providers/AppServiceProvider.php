<?php

namespace App\Providers;

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Observers\AttributionObserver;
use App\Observers\EmployeeObserver;
use App\Observers\MaterielObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Attribution::observe(AttributionObserver::class);
        Employee::observe(EmployeeObserver::class);
        Materiel::observe(MaterielObserver::class);
    }
}
