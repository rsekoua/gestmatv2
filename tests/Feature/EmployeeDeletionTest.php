<?php

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('can delete employee without attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);

    expect($employee->delete())->toBeTrue();
});

test('cannot delete employee with active attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Créer une attribution active
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $employee->delete();
})->throws(ValidationException::class);

test('cannot delete employee with closed attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Créer une attribution et la clôturer
    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(10),
    ]);

    $attribution->update([
        'date_restitution' => now(),
        'observations_res' => 'Restitution OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    $employee->delete();
})->throws(ValidationException::class);

test('error message indicates number of active attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();

    // Créer 3 attributions actives
    for ($i = 0; $i < 3; $i++) {
        $materiel = Materiel::factory()->create([
            'materiel_type_id' => $materielType->id,
            'statut' => 'disponible',
        ]);

        Attribution::create([
            'materiel_id' => $materiel->id,
            'employee_id' => $employee->id,
            'date_attribution' => now(),
        ]);
    }

    try {
        $employee->delete();
        $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
        expect($e->errors()['employee'][0])
            ->toContain('3 attribution(s) active(s)')
            ->toContain('Veuillez d\'abord restituer tous les matériels');
    }
});

test('error message indicates total attributions for closed ones', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();

    // Créer 2 attributions fermées
    for ($i = 0; $i < 2; $i++) {
        $materiel = Materiel::factory()->create([
            'materiel_type_id' => $materielType->id,
            'statut' => 'disponible',
        ]);

        $attribution = Attribution::create([
            'materiel_id' => $materiel->id,
            'employee_id' => $employee->id,
            'date_attribution' => now()->subDays(20),
        ]);

        $attribution->update([
            'date_restitution' => now()->subDays(10),
            'observations_res' => 'OK',
            'etat_general_res' => 'bon',
            'etat_fonctionnel_res' => 'parfait',
            'decision_res' => 'remis_en_stock',
        ]);
    }

    try {
        $employee->delete();
        $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
        expect($e->errors()['employee'][0])
            ->toContain('2 attribution(s) dans l\'historique')
            ->toContain('Les données d\'attribution doivent être préservées');
    }
});
