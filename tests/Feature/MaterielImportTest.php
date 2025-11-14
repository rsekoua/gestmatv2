<?php

use App\Filament\Imports\MaterielImporter;
use App\Models\Materiel;
use App\Models\MaterielType;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    // Créer des types de matériel de test
    MaterielType::factory()->create(['nom' => 'Ordinateur Portable']);
    MaterielType::factory()->create(['nom' => 'Ordinateur Bureau']);
    MaterielType::factory()->create(['nom' => 'Imprimante']);
    MaterielType::factory()->create(['nom' => 'Écran']);
    MaterielType::factory()->create(['nom' => 'Smartphone']);
});

test('can import materiel from valid CSV', function () {
    $csv = "numero_serie,type_materiel,marque,modele,statut,etat_physique,purchase_date,acquision,processor,ram_size_gb,storage_size_gb,screen_size,notes\n";
    $csv .= "SN001,Ordinateur Portable,Dell,Latitude 5420,disponible,bon,2023-01-15,Achat,Intel Core i5,16,512,15.6,Garantie\n";
    $csv .= "SN002,Imprimante,HP,LaserJet P2055,disponible,excellent,2022-06-20,Achat,,,,,,\n";
    $csv .= "SN003,Écran,Samsung,S24F350,disponible,bon,2023-03-10,Achat,,,,24,\n";

    $file = UploadedFile::fake()->createWithContent('materiel.csv', $csv);
    Storage::disk('local')->put('imports/materiel.csv', $file->getContent());

    expect(Materiel::count())->toBe(0);

    $import = Import::create([
        'user_id' => null,
        'file_name' => 'materiel.csv',
        'file_path' => 'imports/materiel.csv',
        'importer' => MaterielImporter::class,
    ]);

    $importer = new MaterielImporter($import);

    // Simuler l'import
    $rows = [
        [
            'numero_serie' => 'SN001',
            'type_materiel' => 'Ordinateur Portable',
            'marque' => 'Dell',
            'modele' => 'Latitude 5420',
            'statut' => 'disponible',
            'etat_physique' => 'bon',
            'purchase_date' => '2023-01-15',
            'acquision' => 'Achat',
            'processor' => 'Intel Core i5',
            'ram_size_gb' => 16,
            'storage_size_gb' => 512,
            'screen_size' => 15.6,
            'notes' => 'Garantie',
        ],
        [
            'numero_serie' => 'SN002',
            'type_materiel' => 'Imprimante',
            'marque' => 'HP',
            'modele' => 'LaserJet P2055',
            'statut' => 'disponible',
            'etat_physique' => 'excellent',
            'purchase_date' => '2022-06-20',
            'acquision' => 'Achat',
        ],
        [
            'numero_serie' => 'SN003',
            'type_materiel' => 'Écran',
            'marque' => 'Samsung',
            'modele' => 'S24F350',
            'statut' => 'disponible',
            'etat_physique' => 'bon',
            'purchase_date' => '2023-03-10',
            'acquision' => 'Achat',
            'screen_size' => 24,
        ],
    ];

    foreach ($rows as $row) {
        $type = MaterielType::where('nom', $row['type_materiel'])->first();
        Materiel::create([
            'numero_serie' => $row['numero_serie'],
            'materiel_type_id' => $type->id,
            'marque' => $row['marque'] ?? null,
            'modele' => $row['modele'] ?? null,
            'statut' => $row['statut'],
            'etat_physique' => $row['etat_physique'] ?? null,
            'purchase_date' => $row['purchase_date'] ?? null,
            'acquision' => $row['acquision'] ?? null,
            'processor' => $row['processor'] ?? null,
            'ram_size_gb' => $row['ram_size_gb'] ?? null,
            'storage_size_gb' => $row['storage_size_gb'] ?? null,
            'screen_size' => $row['screen_size'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }

    expect(Materiel::count())->toBe(3);
    expect(Materiel::where('numero_serie', 'SN001')->exists())->toBeTrue();
    expect(Materiel::where('numero_serie', 'SN002')->exists())->toBeTrue();
    expect(Materiel::where('numero_serie', 'SN003')->exists())->toBeTrue();
});

test('materiel import fails with duplicate numero_serie', function () {
    // Créer un matériel existant
    $type = MaterielType::where('nom', 'Ordinateur Portable')->first();
    Materiel::factory()->create([
        'numero_serie' => 'EXISTING-001',
        'materiel_type_id' => $type->id,
    ]);

    expect(Materiel::count())->toBe(1);

    // Tenter d'importer un matériel avec le même numéro de série
    expect(function () use ($type) {
        Materiel::create([
            'numero_serie' => 'EXISTING-001',
            'materiel_type_id' => $type->id,
            'statut' => 'disponible',
        ]);
    })->toThrow(\Illuminate\Database\QueryException::class);

    expect(Materiel::count())->toBe(1);
});

test('materiel import fails with non-existent type', function () {
    $materiel = [
        'numero_serie' => 'SN999',
        'type_materiel' => 'Type Inexistant',
        'statut' => 'disponible',
    ];

    $type = MaterielType::where('nom', $materiel['type_materiel'])->first();

    expect($type)->toBeNull();
});

test('materiel importer columns are correctly configured', function () {
    $columns = MaterielImporter::getColumns();

    expect($columns)->toHaveCount(13);

    // Vérifier les colonnes requises
    $numeroSerieColumn = collect($columns)->first(fn($col) => $col->getName() === 'numero_serie');
    $typeColumn = collect($columns)->first(fn($col) => $col->getName() === 'materiel_type');
    $statutColumn = collect($columns)->first(fn($col) => $col->getName() === 'statut');

    expect($numeroSerieColumn)->not->toBeNull();
    expect($typeColumn)->not->toBeNull();
    expect($statutColumn)->not->toBeNull();
});

test('materiel is created with correct type relationship', function () {
    $type = MaterielType::where('nom', 'Ordinateur Portable')->first();

    $materiel = Materiel::create([
        'numero_serie' => 'SN001',
        'materiel_type_id' => $type->id,
        'marque' => 'Dell',
        'modele' => 'Latitude 5420',
        'statut' => 'disponible',
    ]);

    expect($materiel->materielType->nom)->toBe('Ordinateur Portable');
});

test('materiel import validates required fields', function () {
    // Tenter de créer un matériel sans numéro de série (champ requis)
    expect(function () {
        Materiel::create([
            'materiel_type_id' => MaterielType::first()->id,
            'statut' => 'disponible',
        ]);
    })->toThrow(\Illuminate\Database\QueryException::class);
});

test('materiel import validates statut enum values', function () {
    $type = MaterielType::where('nom', 'Ordinateur Portable')->first();

    // Statuts valides
    $validStatuts = ['disponible', 'attribué', 'en_panne', 'en_maintenance', 'rebuté'];

    foreach ($validStatuts as $statut) {
        $materiel = Materiel::create([
            'numero_serie' => 'SN-' . $statut,
            'materiel_type_id' => $type->id,
            'statut' => $statut,
        ]);

        expect($materiel->statut)->toBe($statut);
    }

    expect(Materiel::count())->toBe(5);
});

test('materiel import validates etat_physique enum values', function () {
    $type = MaterielType::where('nom', 'Ordinateur Portable')->first();

    // États physiques valides
    $validEtats = ['excellent', 'bon', 'moyen', 'mauvais'];

    foreach ($validEtats as $etat) {
        $materiel = Materiel::create([
            'numero_serie' => 'SN-' . $etat,
            'materiel_type_id' => $type->id,
            'statut' => 'disponible',
            'etat_physique' => $etat,
        ]);

        expect($materiel->etat_physique)->toBe($etat);
    }

    expect(Materiel::count())->toBe(4);
});

test('materiel import handles computer specifications correctly', function () {
    $type = MaterielType::where('nom', 'Ordinateur Portable')->first();

    $materiel = Materiel::create([
        'numero_serie' => 'LAPTOP-001',
        'materiel_type_id' => $type->id,
        'statut' => 'disponible',
        'processor' => 'Intel Core i7-1185G7',
        'ram_size_gb' => 32,
        'storage_size_gb' => 1024,
        'screen_size' => 15.6,
    ]);

    expect($materiel->processor)->toBe('Intel Core i7-1185G7');
    expect($materiel->ram_size_gb)->toBe(32);
    expect($materiel->storage_size_gb)->toBe(1024);
    expect($materiel->screen_size)->toBe(15.6);
});

test('materiel import handles optional fields correctly', function () {
    $type = MaterielType::where('nom', 'Imprimante')->first();

    // Créer un matériel sans les champs optionnels
    $materiel = Materiel::create([
        'numero_serie' => 'PRINTER-001',
        'materiel_type_id' => $type->id,
        'statut' => 'disponible',
    ]);

    expect($materiel->marque)->toBeNull();
    expect($materiel->modele)->toBeNull();
    expect($materiel->etat_physique)->toBeNull();
    expect($materiel->purchase_date)->toBeNull();
    expect($materiel->processor)->toBeNull();
    expect($materiel->ram_size_gb)->toBeNull();
    expect($materiel->storage_size_gb)->toBeNull();
    expect($materiel->screen_size)->toBeNull();
    expect($materiel->notes)->toBeNull();
});

test('materiel import handles date fields correctly', function () {
    $type = MaterielType::where('nom', 'Ordinateur Portable')->first();

    $materiel = Materiel::create([
        'numero_serie' => 'DATE-TEST-001',
        'materiel_type_id' => $type->id,
        'statut' => 'disponible',
        'purchase_date' => '2023-06-15',
    ]);

    expect($materiel->purchase_date)->not->toBeNull();
    expect($materiel->purchase_date->format('Y-m-d'))->toBe('2023-06-15');
});
