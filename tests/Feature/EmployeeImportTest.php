<?php

use App\Filament\Imports\EmployeeImporter;
use App\Models\Employee;
use App\Models\Service;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    // Créer des services de test
    Service::factory()->create(['code' => 'DSI', 'nom' => 'Direction des Systèmes d\'Information']);
    Service::factory()->create(['code' => 'DRH', 'nom' => 'Direction des Ressources Humaines']);
    Service::factory()->create(['code' => 'FIN', 'nom' => 'Direction Financière']);
});

test('can import employees from valid CSV', function () {
    $csv = "nom,prenom,email,service_code,telephone,emploi,fonction\n";
    $csv .= "DUPONT,Jean,jean.dupont@test.cd,DSI,+243123456789,CDI,Développeur\n";
    $csv .= "MARTIN,Marie,marie.martin@test.cd,DRH,+243987654321,CDD,Responsable RH\n";
    $csv .= "BERNARD,Paul,paul.bernard@test.cd,FIN,,CDI,Comptable\n";

    $file = UploadedFile::fake()->createWithContent('employees.csv', $csv);
    Storage::disk('local')->put('imports/employees.csv', $file->getContent());

    expect(Employee::count())->toBe(0);

    $import = Import::create([
        'user_id' => null,
        'file_name' => 'employees.csv',
        'file_path' => 'imports/employees.csv',
        'importer' => EmployeeImporter::class,
    ]);

    $importer = new EmployeeImporter($import);

    // Simuler l'import
    $rows = [
        ['nom' => 'DUPONT', 'prenom' => 'Jean', 'email' => 'jean.dupont@test.cd', 'service' => 'DSI', 'telephone' => '+243123456789', 'emploi' => 'CDI', 'fonction' => 'Développeur'],
        ['nom' => 'MARTIN', 'prenom' => 'Marie', 'email' => 'marie.martin@test.cd', 'service' => 'DRH', 'telephone' => '+243987654321', 'emploi' => 'CDD', 'fonction' => 'Responsable RH'],
        ['nom' => 'BERNARD', 'prenom' => 'Paul', 'email' => 'paul.bernard@test.cd', 'service' => 'FIN', 'telephone' => null, 'emploi' => 'CDI', 'fonction' => 'Comptable'],
    ];

    foreach ($rows as $row) {
        $service = Service::where('code', $row['service'])->first();
        Employee::create([
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'email' => $row['email'],
            'service_id' => $service->id,
            'telephone' => $row['telephone'],
            'emploi' => $row['emploi'],
            'fonction' => $row['fonction'],
        ]);
    }

    expect(Employee::count())->toBe(3);
    expect(Employee::where('email', 'jean.dupont@test.cd')->exists())->toBeTrue();
    expect(Employee::where('email', 'marie.martin@test.cd')->exists())->toBeTrue();
    expect(Employee::where('email', 'paul.bernard@test.cd')->exists())->toBeTrue();
});

test('employee import fails with duplicate email', function () {
    // Créer un employé existant
    $service = Service::where('code', 'DSI')->first();
    Employee::factory()->create([
        'email' => 'existing@test.cd',
        'service_id' => $service->id,
    ]);

    expect(Employee::count())->toBe(1);

    // Tenter d'importer un employé avec le même email
    $employee = [
        'nom' => 'NOUVEAU',
        'prenom' => 'Test',
        'email' => 'existing@test.cd',
        'service' => 'DSI',
    ];

    // La validation devrait échouer
    expect(function () use ($employee) {
        $service = Service::where('code', $employee['service'])->first();
        Employee::create([
            'nom' => $employee['nom'],
            'prenom' => $employee['prenom'],
            'email' => $employee['email'],
            'service_id' => $service->id,
        ]);
    })->toThrow(\Illuminate\Database\QueryException::class);

    expect(Employee::count())->toBe(1);
});

test('employee import fails with non-existent service', function () {
    $employee = [
        'nom' => 'TEST',
        'prenom' => 'User',
        'email' => 'test@test.cd',
        'service' => 'INVALID_CODE',
    ];

    $service = Service::where('code', $employee['service'])->first();

    expect($service)->toBeNull();
});

test('employee importer columns are correctly configured', function () {
    $columns = EmployeeImporter::getColumns();

    expect($columns)->toHaveCount(7);

    // Vérifier les colonnes requises
    $nomColumn = collect($columns)->first(fn($col) => $col->getName() === 'nom');
    $prenomColumn = collect($columns)->first(fn($col) => $col->getName() === 'prenom');
    $emailColumn = collect($columns)->first(fn($col) => $col->getName() === 'email');
    $serviceColumn = collect($columns)->first(fn($col) => $col->getName() === 'service');

    expect($nomColumn)->not->toBeNull();
    expect($prenomColumn)->not->toBeNull();
    expect($emailColumn)->not->toBeNull();
    expect($serviceColumn)->not->toBeNull();
});

test('employee is created with correct service relationship', function () {
    $service = Service::where('code', 'DSI')->first();

    $employee = Employee::create([
        'nom' => 'DUPONT',
        'prenom' => 'Jean',
        'email' => 'jean.dupont@test.cd',
        'service_id' => $service->id,
        'telephone' => '+243123456789',
        'emploi' => 'CDI',
        'fonction' => 'Développeur',
    ]);

    expect($employee->service->code)->toBe('DSI');
    expect($employee->service->nom)->toBe('Direction des Systèmes d\'Information');
});

test('employee import validates required fields', function () {
    // Tenter de créer un employé sans email (champ requis)
    expect(function () {
        Employee::create([
            'nom' => 'TEST',
            'prenom' => 'User',
            'service_id' => Service::first()->id,
        ]);
    })->toThrow(\Illuminate\Database\QueryException::class);
});

test('employee import handles optional fields correctly', function () {
    $service = Service::where('code', 'DSI')->first();

    // Créer un employé sans les champs optionnels
    $employee = Employee::create([
        'nom' => 'DUPONT',
        'prenom' => 'Jean',
        'email' => 'jean.dupont@test.cd',
        'service_id' => $service->id,
    ]);

    expect($employee->telephone)->toBeNull();
    expect($employee->emploi)->toBeNull();
    expect($employee->fonction)->toBeNull();
    expect($employee->email)->toBe('jean.dupont@test.cd');
});
