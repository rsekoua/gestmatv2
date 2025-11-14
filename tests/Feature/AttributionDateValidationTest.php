<?php

use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('first attribution of a materiel can be created with any date', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Première attribution - pas de contrainte de date
    $attribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(10), // 10 jours dans le passé
    ]);

    expect($attribution)->toBeInstanceOf(Attribution::class)
        ->and($attribution->date_attribution)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($materiel->fresh()->statut)->toBe('attribué');
});

test('second attribution date must be equal or after last restitution date', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Première attribution
    $firstAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(30),
    ]);

    // Restitution le 15 juin
    $restitutionDate = now()->subDays(15);
    $firstAttribution->update([
        'date_restitution' => $restitutionDate,
        'decision_res' => 'remis_en_stock',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'observations_res' => 'Restitution conforme',
    ]);

    expect($materiel->fresh()->statut)->toBe('disponible');

    // Deuxième attribution le 20 juin (5 jours après la restitution) - DOIT RÉUSSIR
    $secondAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => $restitutionDate->copy()->addDays(5),
    ]);

    expect($secondAttribution)->toBeInstanceOf(Attribution::class)
        ->and($materiel->fresh()->statut)->toBe('attribué');
});

test('second attribution date equal to last restitution date is allowed', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Première attribution
    $firstAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(30),
    ]);

    // Restitution le 15 juin
    $restitutionDate = now()->subDays(15);
    $firstAttribution->update([
        'date_restitution' => $restitutionDate,
        'decision_res' => 'remis_en_stock',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'observations_res' => 'Restitution conforme',
    ]);

    // Deuxième attribution le MÊME JOUR que la restitution - DOIT RÉUSSIR
    $secondAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => $restitutionDate->copy(),
    ]);

    expect($secondAttribution)->toBeInstanceOf(Attribution::class)
        ->and($materiel->fresh()->statut)->toBe('attribué');
});

test('second attribution date before last restitution date is rejected', function () {
    $service = Service::factory()->create();
    $employee = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Première attribution
    $firstAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee->id,
        'date_attribution' => now()->subDays(30),
    ]);

    // Restitution le 15 juin
    $restitutionDate = now()->subDays(15);
    $firstAttribution->update([
        'date_restitution' => $restitutionDate,
        'decision_res' => 'remis_en_stock',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'observations_res' => 'Restitution conforme',
    ]);

    expect($materiel->fresh()->statut)->toBe('disponible');

    // Essayer de créer une attribution le 10 juin (5 jours AVANT la restitution) - DOIT ÉCHOUER
    try {
        Attribution::create([
            'materiel_id' => $materiel->id,
            'employee_id' => $employee->id,
            'date_attribution' => $restitutionDate->copy()->subDays(5),
        ]);

        // Si on arrive ici, le test échoue car l'exception n'a pas été lancée
        expect(true)->toBeFalse('Une exception ValidationException aurait dû être lancée');
    } catch (ValidationException $e) {
        // L'exception est attendue
        expect($e->errors())->toHaveKey('date_attribution');
        expect($e->errors()['date_attribution'][0])->toContain('doit être égale ou postérieure');
    }

    // Le matériel doit rester disponible car l'attribution a échoué
    expect($materiel->fresh()->statut)->toBe('disponible');
});

test('third attribution respects last restitution date not first one', function () {
    $service = Service::factory()->create();
    $employee1 = Employee::factory()->create(['service_id' => $service->id]);
    $employee2 = Employee::factory()->create(['service_id' => $service->id]);
    $employee3 = Employee::factory()->create(['service_id' => $service->id]);
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    // Première attribution - 30 jours dans le passé
    $firstAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee1->id,
        'date_attribution' => now()->subDays(30),
    ]);

    // Première restitution - 20 jours dans le passé
    $firstRestitutionDate = now()->subDays(20);
    $firstAttribution->update([
        'date_restitution' => $firstRestitutionDate,
        'decision_res' => 'remis_en_stock',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'observations_res' => 'Première restitution',
    ]);

    // Deuxième attribution - 18 jours dans le passé
    $secondAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee2->id,
        'date_attribution' => now()->subDays(18),
    ]);

    // Deuxième restitution (LA PLUS RÉCENTE) - 10 jours dans le passé
    $secondRestitutionDate = now()->subDays(10);
    $secondAttribution->update([
        'date_restitution' => $secondRestitutionDate,
        'decision_res' => 'remis_en_stock',
        'etat_general_res' => 'bon',
        'etat_fonctionnel_res' => 'parfait',
        'observations_res' => 'Deuxième restitution',
    ]);

    expect($materiel->fresh()->statut)->toBe('disponible');

    // Troisième attribution - Doit être >= à la DERNIÈRE restitution (10 jours dans le passé)
    // On teste avec 8 jours dans le passé (2 jours après la dernière restitution) - DOIT RÉUSSIR
    $thirdAttribution = Attribution::create([
        'materiel_id' => $materiel->id,
        'employee_id' => $employee3->id,
        'date_attribution' => $secondRestitutionDate->copy()->addDays(2),
    ]);

    expect($thirdAttribution)->toBeInstanceOf(Attribution::class);
    expect($materiel->fresh()->statut)->toBe('attribué');

    // Vérifier qu'on ne peut PAS créer une attribution à 15 jours dans le passé
    // (qui serait entre les deux restitutions mais AVANT la dernière)
    try {
        Attribution::create([
            'materiel_id' => $materiel->id,
            'employee_id' => $employee3->id,
            'date_attribution' => now()->subDays(15), // Entre première (20j) et deuxième (10j) restitution
        ]);

        expect(true)->toBeFalse('Une exception ValidationException aurait dû être lancée');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('date_attribution');
    }
});

test('materiel with multiple attributions and restitutions maintains chronological integrity', function () {
    $service = Service::factory()->create();
    $materielType = MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    $materiel = Materiel::factory()->create([
        'materiel_type_id' => $materielType->id,
        'statut' => 'disponible',
    ]);

    $employees = Employee::factory()->count(5)->create(['service_id' => $service->id]);

    // Simuler un cycle complet sur plusieurs mois
    $currentDate = now()->subMonths(6);

    foreach ($employees as $index => $employee) {
        // Attribution
        $attribution = Attribution::create([
            'materiel_id' => $materiel->id,
            'employee_id' => $employee->id,
            'date_attribution' => $currentDate->copy(),
        ]);

        expect($materiel->fresh()->statut)->toBe('attribué');

        // Utilisation pendant 20-40 jours
        $usageDays = rand(20, 40);
        $currentDate->addDays($usageDays);

        // Restitution
        $attribution->update([
            'date_restitution' => $currentDate->copy(),
            'decision_res' => 'remis_en_stock',
            'etat_general_res' => 'bon',
            'etat_fonctionnel_res' => 'parfait',
            'observations_res' => 'Restitution #'.($index + 1),
        ]);

        expect($materiel->fresh()->statut)->toBe('disponible');

        // Attendre quelques jours avant la prochaine attribution
        $currentDate->addDays(rand(1, 5));
    }

    // Vérifier l'historique complet
    $allAttributions = Attribution::where('materiel_id', $materiel->id)
        ->orderBy('date_attribution')
        ->get();

    expect($allAttributions)->toHaveCount(5);

    // Vérifier que chaque attribution respecte la chronologie
    for ($i = 1; $i < $allAttributions->count(); $i++) {
        $previous = $allAttributions[$i - 1];
        $current = $allAttributions[$i];

        // La date d'attribution actuelle doit être >= à la date de restitution précédente
        expect($current->date_attribution->greaterThanOrEqualTo($previous->date_restitution))
            ->toBeTrue('Attribution #'.$i.' date should be >= previous restitution date');
    }
});
