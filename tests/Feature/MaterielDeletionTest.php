<?php

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('can delete materiel without attributions', function () {
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    expect($materiel->delete())->toBeTrue();
});

test('cannot delete materiel with active attributions', function () {
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

    $materiel->fresh()->delete();
})->throws(ValidationException::class);

test('cannot delete materiel with closed attributions', function () {
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

    $materiel->fresh()->delete();
})->throws(ValidationException::class);

test('error message indicates number of active attributions for materiel', function () {
    $service = Service::factory()->create();
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Créer 2 attributions actives (histoire alternative)
    $employee1 = Employee::factory()->create(['service_id' => $service->id]);
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee1->id,
        'date_attribution' => now()->subDays(20),
    ])->update([
        'date_restitution' => now()->subDays(10),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    $employee2 = Employee::factory()->create(['service_id' => $service->id]);
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee2->id,
        'date_attribution' => now(),
    ]);

    try {
        $materiel->fresh()->delete();
        $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
        expect($e->errors()['materiel'][0])
            ->toContain('1 attribution(s) active(s)')
            ->toContain('Veuillez d\'abord restituer le matériel');
    }
});

test('error message indicates total attributions for materiel with closed ones', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Créer 3 attributions fermées
    for ($i = 0; $i < 3; $i++) {
        $attribution = Attribution::create([
            'materiel_id' => $materiel->id,
            'employee_id' => $employee->id,
            'date_attribution' => now()->subDays(20 + $i * 30),
        ]);

        $attribution->update([
            'date_restitution' => now()->subDays(10 + $i * 30),
            'observations_res' => 'OK',
            'etat_general_res' => 'bon',
            'etat_fonctionnel_res' => 'parfait',
            'decision_res' => 'remis_en_stock',
        ]);
    }

    try {
        $materiel->fresh()->delete();
        $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
        expect($e->errors()['materiel'][0])
            ->toContain('3 attribution(s) dans l\'historique')
            ->toContain('Les données d\'attribution doivent être préservées');
    }
});
