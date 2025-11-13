<?php

use App\Models\Accessory;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

// ============================================================================
// TESTS DE CRÉATION D'ATTRIBUTION
// ============================================================================

test('can create attribution with valid data', function () {
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
        'observations_att' => 'Première attribution',
    ]);

    expect($attribution)->toBeInstanceOf(Attribution::class)
        ->and($attribution->materiel_id)->toBe($materiel->id)
        ->and($attribution->employee_id)->toBe($employee->id)
        ->and($attribution->isActive())->toBeTrue()
        ->and($materiel->fresh()->statut)->toBe('attribué');
});

test('attribution automatically generates numero_decharge_att on creation', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($attribution->numero_decharge_att)
        ->not()->toBeNull()
        ->and($attribution->numero_decharge_att)->toMatch('/^ATT-\d{4}-\d{4}$/');
});

test('attribution numero_decharge_att is unique and sequential', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();

    $materiel1 = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $materiel2 = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution1 = Attribution::create([
        'materiel_id' => $materiel1->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $attribution2 = Attribution::create([
        'materiel_id' => $materiel2->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($attribution1->numero_decharge_att)->not()->toBe($attribution2->numero_decharge_att);

    $year = now()->year;
    expect($attribution1->numero_decharge_att)->toBe("ATT-{$year}-0001")
        ->and($attribution2->numero_decharge_att)->toBe("ATT-{$year}-0002");
});

test('cannot create attribution for materiel with statut attribué', function () {
    $service = Service::factory()->create();
    $employee1 = Employee::factory()->create(['service_id' => $service->id]);
    $employee2 = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Créer une première attribution pour mettre le statut à "attribué"
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee1->id,
        'date_attribution' => now(),
    ]);

    // Tenter de créer une seconde attribution (devrait échouer)
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee2->id,
        'date_attribution' => now(),
    ]);
})->throws(ValidationException::class);

test('cannot create attribution for materiel with active attribution', function () {
    $service = Service::factory()->create();
    $employee1 = Employee::factory()->create(['service_id' => $service->id]);
    $employee2 = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Première attribution
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee1->id,
        'date_attribution' => now(),
    ]);

    // Tentative de seconde attribution sans restitution de la première
    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee2->id,
        'date_attribution' => now(),
    ]);
})->throws(ValidationException::class);

test('cannot create attribution for rebute materiel', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'rebuté',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);
})->throws(ValidationException::class);

// ============================================================================
// TESTS DE MODIFICATION DU STATUT DU MATÉRIEL
// ============================================================================

test('cannot manually change materiel statut to disponible when it has active attribution', function () {
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

    expect($materiel->fresh()->statut)->toBe('attribué');

    // Tenter de changer manuellement le statut (utiliser fresh() pour récupérer l'instance)
    $materiel->fresh()->update(['statut' => 'disponible']);
})->throws(ValidationException::class);

test('cannot manually change materiel statut to en_maintenance when it has active attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $materiel->fresh()->update(['statut' => 'en_maintenance']);
})->throws(ValidationException::class);

test('cannot manually change materiel statut to rebute when it has active attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $materiel->fresh()->update(['statut' => 'rebuté']);
})->throws(ValidationException::class);

test('can change materiel statut when no active attribution exists', function () {
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $materiel->update(['statut' => 'en_maintenance']);

    expect($materiel->fresh()->statut)->toBe('en_maintenance');
});

// ============================================================================
// TESTS DE RESTITUTION
// ============================================================================

test('can close attribution with date_restitution and required fields', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(10),
    ]);

    $attribution->update([
        'date_restitution' => now(),
        'observations_res' => 'Restitution normale',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    expect($attribution->isClosed())->toBeTrue()
        ->and($attribution->isActive())->toBeFalse()
        ->and($attribution->numero_decharge_res)->not()->toBeNull()
        ->and($attribution->numero_decharge_res)->toMatch('/^RES-\d{4}-\d{4}$/');
});

test('materiel statut changes to disponible on restitution with remis_en_stock decision', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    expect($materiel->fresh()->statut)->toBe('disponible');
});

test('materiel statut changes to en_maintenance on restitution with a_reparer decision', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'observations_res' => 'Nécessite réparation',
        'etat_general_res' => 'moyen',
        'etat_fonctionnel_res' => 'dysfonctionnements',
        'decision_res' => 'a_reparer',
    ]);

    expect($materiel->fresh()->statut)->toBe('en_maintenance');
});

test('materiel statut changes to rebute on restitution with rebut decision', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'observations_res' => 'Hors service',
        'etat_general_res' => 'mauvais',
        'etat_fonctionnel_res' => 'hors_service',
        'decision_res' => 'rebut',
    ]);

    expect($materiel->fresh()->statut)->toBe('rebuté');
});

test('date_restitution must be after or equal to date_attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'date_restitution' => now()->subDays(5),
        'observations_res' => 'Test',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
    ]);
})->throws(ValidationException::class);

test('restitution requires observations_res', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
    ]);
})->throws(ValidationException::class);

test('restitution requires etat_general_res', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'observations_res' => 'Test',
        'etat_fonctionnel_res' => 'parfait',
    ]);
})->throws(ValidationException::class);

test('restitution requires etat_fonctionnel_res', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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
        'observations_res' => 'Test',
        'etat_general_res' => 'bon',
    ]);
})->throws(ValidationException::class);

// ============================================================================
// TESTS DES ACCESSOIRES
// ============================================================================

test('can attach accessories to attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $accessory1 = Accessory::factory()->create();
    $accessory2 = Accessory::factory()->create();

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $attribution->accessories()->attach($accessory1->id, ['statut_att' => 'fourni']);
    $attribution->accessories()->attach($accessory2->id, ['statut_att' => 'fourni']);

    expect($attribution->accessories)->toHaveCount(2)
        ->and($attribution->accessories->pluck('id')->toArray())->toContain($accessory1->id, $accessory2->id);
});

test('can update accessory status on restitution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $accessory = Accessory::factory()->create();

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $attribution->accessories()->attach($accessory->id, ['statut_att' => 'fourni']);

    $attribution->accessories()->updateExistingPivot($accessory->id, ['statut_res' => 'restitué']);

    expect($attribution->accessories()->first()->pivot->statut_att)->toBe('fourni')
        ->and($attribution->accessories()->first()->pivot->statut_res)->toBe('restitué');
});

// ============================================================================
// TESTS DE SUPPRESSION
// ============================================================================

test('deleting active attribution restores materiel to disponible', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
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

test('can delete closed attribution without changing materiel status', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(10),
    ]);

    $attribution->update([
        'date_restitution' => now(),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    $currentStatut = $materiel->fresh()->statut;
    $attribution->delete();

    expect($materiel->fresh()->statut)->toBe($currentStatut);
});

// ============================================================================
// TESTS DES RELATIONS
// ============================================================================

test('attribution belongs to materiel', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($attribution->materiel)->toBeInstanceOf(Materiel::class)
        ->and($attribution->materiel->id)->toBe($materiel->id);
});

test('attribution belongs to employee', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($attribution->employee)->toBeInstanceOf(Employee::class)
        ->and($attribution->employee->id)->toBe($employee->id);
});

test('materiel has many attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(20),
    ])->update([
        'date_restitution' => now()->subDays(10),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($materiel->attributions)->toHaveCount(2);
});

test('materiel has one active attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(20),
    ])->update([
        'date_restitution' => now()->subDays(10),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    $activeAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    expect($materiel->activeAttribution)->toBeInstanceOf(Attribution::class)
        ->and($materiel->activeAttribution->id)->toBe($activeAttribution->id);
});

// ============================================================================
// TESTS DES SCOPES
// ============================================================================

test('active scope returns only active attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel1 = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);
    $materiel2 = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel1->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(20),
    ])->update([
        'date_restitution' => now()->subDays(10),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    Attribution::create([
        'materiel_id' => $materiel2->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $activeAttributions = Attribution::active()->get();

    expect($activeAttributions)->toHaveCount(1)
        ->and($activeAttributions->first()->materiel_id)->toBe($materiel2->id);
});

test('closed scope returns only closed attributions', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel1 = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);
    $materiel2 = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    Attribution::create([
        'materiel_id' => $materiel1->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(20),
    ])->update([
        'date_restitution' => now()->subDays(10),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    Attribution::create([
        'materiel_id' => $materiel2->id,
        'employee_id' => $employee->id,
        'date_attribution' => now(),
    ]);

    $closedAttributions = Attribution::closed()->get();

    expect($closedAttributions)->toHaveCount(1)
        ->and($closedAttributions->first()->materiel_id)->toBe($materiel1->id);
});

// ============================================================================
// TESTS DES MÉTHODES UTILITAIRES
// ============================================================================

test('duration_in_days calculates correctly for active attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(15),
    ]);

    expect($attribution->duration_in_days)->toBe(15);
});

test('duration_in_days calculates correctly for closed attribution', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create();
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(30),
    ]);

    $attribution->update([
        'date_restitution' => now()->subDays(10),
        'observations_res' => 'OK',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'decision_res' => 'remis_en_stock',
    ]);

    expect($attribution->duration_in_days)->toBe(20);
});
