<?php

namespace App\Console\Commands;

use App\Models\{Service, Employee, MaterielType, Materiel, Accessory, Attribution};
use Carbon\Carbon;
use Illuminate\Console\Command;

class VerifyUuidSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-uuid-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie que la configuration UUID fonctionne correctement';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Vérification de la configuration UUID...');
        $this->newLine();

        $allTestsPassed = true;

        // Test 1: Vérification des seeders
        if (!$this->testSeeders()) {
            $allTestsPassed = false;
        }

        // Test 2: Création avec UUID
        if (!$this->testUuidCreation()) {
            $allTestsPassed = false;
        }

        // Test 3: Relations
        if (!$this->testRelations()) {
            $allTestsPassed = false;
        }

        // Test 4: Amortissement
        if (!$this->testDepreciation()) {
            $allTestsPassed = false;
        }

        // Test 5: Génération numéros
        if (!$this->testNumberGeneration()) {
            $allTestsPassed = false;
        }

        // Test 6: Accessors
        if (!$this->testAccessors()) {
            $allTestsPassed = false;
        }

        $this->newLine();

        if ($allTestsPassed) {
            $this->info('✅ Tous les tests sont passés avec succès !');
            $this->info('🚀 Vous pouvez commencer le développement Filament.');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Certains tests ont échoué. Veuillez vérifier l\'installation.');
            return Command::FAILURE;
        }
    }

    /**
     * Test 1: Vérification des seeders
     */
    private function testSeeders(): bool
    {
        $this->info('Test 1: Vérification des données seedées');

        try {
            $typesCount = MaterielType::count();
            $accessoriesCount = Accessory::count();

            if ($typesCount < 11) {
                $this->error("   ❌ Types de matériel manquants (attendu: 11, trouvé: {$typesCount})");
                $this->warn("   💡 Exécutez: php artisan db:seed --class=MaterielTypeSeeder");
                return false;
            }

            if ($accessoriesCount < 10) {
                $this->error("   ❌ Accessoires manquants (attendu: 10, trouvé: {$accessoriesCount})");
                $this->warn("   💡 Exécutez: php artisan db:seed --class=AccessorySeeder");
                return false;
            }

            $this->line("   ✅ {$typesCount} types de matériel");
            $this->line("   ✅ {$accessoriesCount} accessoires");
            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Test 2: Création avec UUID
     */
    private function testUuidCreation(): bool
    {
        $this->info('Test 2: Création d\'enregistrements avec UUID');

        try {
            // Créer un service de test
            $service = Service::create([
                'nom' => 'Test Service UUID',
                'code' => 'TEST-UUID-' . time(),
            ]);

            // Vérifier que l'ID est un UUID valide
            $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

            if (!preg_match($uuidPattern, $service->id)) {
                $this->error("   ❌ L'ID n'est pas un UUID valide: {$service->id}");
                return false;
            }

            $this->line("   ✅ UUID généré: {$service->id}");

            // Nettoyer
            $service->delete();

            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Test 3: Relations
     */
    private function testRelations(): bool
    {
        $this->info('Test 3: Vérification des relations');

        try {
            // Créer un service
            $service = Service::create([
                'nom' => 'Test Relations',
                'code' => 'REL-' . time(),
            ]);

            // Créer un employé
            $employee = Employee::create([
                'service_id' => $service->id,
                'nom' => 'Test',
                'prenom' => 'User',
                'email' => 'test-' . time() . '@example.com',
            ]);

            // Vérifier la relation
            if ($employee->service->id !== $service->id) {
                $this->error("   ❌ Relation employee->service incorrecte");
                return false;
            }

            if (!$service->employees->contains($employee)) {
                $this->error("   ❌ Relation service->employees incorrecte");
                return false;
            }

            $this->line("   ✅ Relation Service ↔ Employee");

            // Nettoyer
            $employee->delete();
            $service->delete();

            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Test 4: Amortissement
     */
    private function testDepreciation(): bool
    {
        $this->info('Test 4: Calcul de l\'amortissement');

        try {
            $ordinateurType = MaterielType::where('nom', 'Ordinateur Portable')->first();
            $imprimanteType = MaterielType::where('nom', 'Imprimante')->first();

            if (!$ordinateurType || !$imprimanteType) {
                $this->error("   ❌ Types de matériel manquants (requis pour le test)");
                $this->warn("   💡 Exécutez d'abord: php artisan db:seed");
                return false;
            }

            // Test 1: Ordinateur de plus de 3 ans
            $oldPc = Materiel::create([
                'materiel_type_id' => $ordinateurType->id,
                'nom' => 'Old PC Test',
                'numero_serie' => 'OLD-PC-' . time(),
                'purchase_date' => Carbon::now()->subYears(4),
                'statut' => 'disponible',
            ]);

            if (!$oldPc->is_amorti) {
                $this->error("   ❌ Ordinateur de 4 ans devrait être amorti");
                $oldPc->delete();
                return false;
            }

            $this->line("   ✅ Ordinateur > 3 ans : Amorti");

            // Test 2: Ordinateur récent
            $newPc = Materiel::create([
                'materiel_type_id' => $ordinateurType->id,
                'nom' => 'New PC Test',
                'numero_serie' => 'NEW-PC-' . time(),
                'purchase_date' => Carbon::now()->subYear(),
                'statut' => 'disponible',
            ]);

            if ($newPc->is_amorti) {
                $this->error("   ❌ Ordinateur de 1 an ne devrait pas être amorti");
                $oldPc->delete();
                $newPc->delete();
                return false;
            }

            $this->line("   ✅ Ordinateur < 3 ans : Actif");

            // Test 3: Imprimante ancienne (pas d'amortissement auto)
            $oldPrinter = Materiel::create([
                'materiel_type_id' => $imprimanteType->id,
                'nom' => 'Old Printer Test',
                'numero_serie' => 'OLD-PRINT-' . time(),
                'purchase_date' => Carbon::now()->subYears(5),
                'statut' => 'disponible',
            ]);

            if ($oldPrinter->is_amorti) {
                $this->error("   ❌ Imprimante ne devrait pas avoir d'amortissement automatique");
                $oldPc->delete();
                $newPc->delete();
                $oldPrinter->delete();
                return false;
            }

            $this->line("   ✅ Imprimante : Pas d'amortissement auto");

            // Nettoyer
            $oldPc->delete();
            $newPc->delete();
            $oldPrinter->delete();

            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Test 5: Génération des numéros
     */
    private function testNumberGeneration(): bool
    {
        $this->info('Test 5: Génération des numéros de décharge');

        try {
            $currentYear = now()->year;

            // Générer un numéro d'attribution
            $attNumber = Attribution::generateAttributionNumber();
            $attPattern = "/^ATT-{$currentYear}-\d{4}$/";

            if (!preg_match($attPattern, $attNumber)) {
                $this->error("   ❌ Format numéro attribution incorrect: {$attNumber}");
                return false;
            }

            $this->line("   ✅ Numéro attribution: {$attNumber}");

            // Générer un numéro de restitution
            $resNumber = Attribution::generateRestitutionNumber();
            $resPattern = "/^RES-{$currentYear}-\d{4}$/";

            if (!preg_match($resPattern, $resNumber)) {
                $this->error("   ❌ Format numéro restitution incorrect: {$resNumber}");
                return false;
            }

            $this->line("   ✅ Numéro restitution: {$resNumber}");

            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Test 6: Accessors
     */
    private function testAccessors(): bool
    {
        $this->info('Test 6: Vérification des accessors');

        try {
            // Test Employee accessors
            $service = Service::create([
                'nom' => 'Test Accessors',
                'code' => 'ACC-' . time(),
            ]);

            $employee = Employee::create([
                'service_id' => $service->id,
                'nom' => 'Kouassi',
                'prenom' => 'Jean',
                'email' => 'accessor-test-' . time() . '@example.com',
            ]);

            if ($employee->full_name !== 'Jean Kouassi') {
                $this->error("   ❌ Accessor full_name incorrect: {$employee->full_name}");
                return false;
            }

            $this->line("   ✅ Employee->full_name: {$employee->full_name}");

            // Test Materiel accessors
            $type = MaterielType::where('nom', 'Ordinateur Portable')->first();
            $materiel = Materiel::create([
                'materiel_type_id' => $type->id,
                'nom' => 'Test PC',
                'marque' => 'Dell',
                'modele' => 'Latitude',
                'numero_serie' => 'ACC-TEST-' . time(),
                'purchase_date' => now(),
                'statut' => 'disponible',
            ]);

            $expectedDescription = 'Ordinateur Portable - Dell - Latitude';
            if ($materiel->full_description !== $expectedDescription) {
                $this->error("   ❌ Accessor full_description incorrect: {$materiel->full_description}");
                return false;
            }

            $this->line("   ✅ Materiel->full_description: {$materiel->full_description}");

            // Nettoyer
            $materiel->delete();
            $employee->delete();
            $service->delete();

            return true;

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: {$e->getMessage()}");
            return false;
        }
    }
}
