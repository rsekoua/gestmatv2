<?php

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('materiel statut is updated to attribue when attribution is created', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    expect($materiel->fresh()->statut)->toBe('disponible');

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($materiel->fresh()->statut)->toBe('attribué');
});

test('materiel statut is updated to disponible when attribution is closed with remis_en_stock decision', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($materiel->fresh()->statut)->toBe('attribué');

    $attribution->update([
        'date_restitution' => now(),
        'decision_res' => 'remis_en_stock',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'observations_res' => 'Restitution conforme',
    ]);

    expect($materiel->fresh()->statut)->toBe('disponible');
});

test('materiel statut is updated to en_maintenance when attribution is closed with a_reparer decision', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $attribution->update([
        'date_restitution' => now(),
        'decision_res' => 'a_reparer',
        'etat_general_res' => 'moyen',
        'etat_fonctionnel_res' => 'dysfonctionnements',
        'observations_res' => 'Nécessite réparation',
    ]);

    expect($materiel->fresh()->statut)->toBe('en_maintenance');
});

test('materiel statut is updated to rebute when attribution is closed with rebut decision', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $attribution->update([
        'date_restitution' => now(),
        'decision_res' => 'rebut',
        'etat_general_res' => 'mauvais',
        'etat_fonctionnel_res' => 'hors_service',
        'observations_res' => 'Matériel à réformer',
    ]);

    expect($materiel->fresh()->statut)->toBe('rebuté');
});

test('cannot create attribution for already attributed materiel', function () {
    $service = Service::factory()->create();
    $employee1 = Employee::factory()->create(['service_id' => $service->id]);
    $employee2 = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee1->id,
        'date_attribution' => now(),
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee2->id,
        'date_attribution' => now(),
    ]);
})->throws(Exception::class, 'Ce matériel est déjà attribué à un autre employé.');

test('materiel statut is restored to disponible when attribution is deleted', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($materiel->fresh()->statut)->toBe('attribué');

    $attribution->delete();

    expect($materiel->fresh()->statut)->toBe('disponible');
});
