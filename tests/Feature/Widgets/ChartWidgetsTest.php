<?php

use App\Filament\Widgets\AttributionsChartWidget;
use App\Filament\Widgets\MaterielsStatusChartWidget;
use App\Filament\Widgets\MaterielsTypeChartWidget;
use App\Models\Attribution;
use App\Models\Materiel;
use App\Models\MaterielType;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses()->group('widgets');

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('AttributionsChartWidget', function () {
    it('can render the attributions chart widget', function () {
        actingAs($this->user);

        Livewire::test(AttributionsChartWidget::class)
            ->assertSuccessful();
    });

    it('returns correct chart type', function () {
        actingAs($this->user);

        $widget = Livewire::test(AttributionsChartWidget::class);

        expect($widget->instance()->getType())->toBe('line');
    });

    it('generates data for 12 months', function () {
        actingAs($this->user);

        $widget = Livewire::test(AttributionsChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['labels'])->toHaveCount(12);
        expect($data['datasets'])->toHaveCount(2);
    });

    it('tracks new attributions correctly', function () {
        actingAs($this->user);

        // Créer des attributions ce mois
        Attribution::factory()->count(3)->create([
            'date_attribution' => now(),
        ]);

        // Créer des attributions il y a 2 mois
        Attribution::factory()->count(2)->create([
            'date_attribution' => now()->subMonths(2),
        ]);

        $widget = Livewire::test(AttributionsChartWidget::class);
        $data = $widget->instance()->getData();

        $newAttributionsData = $data['datasets'][0]['data'];

        // Le dernier élément devrait être 3 (ce mois)
        expect($newAttributionsData[11])->toBe(3);
    });

    it('tracks restitutions correctly', function () {
        actingAs($this->user);

        // Créer des restitutions ce mois
        Attribution::factory()->count(2)->create([
            'date_restitution' => now(),
        ]);

        $widget = Livewire::test(AttributionsChartWidget::class);
        $data = $widget->instance()->getData();

        $restitutionsData = $data['datasets'][1]['data'];

        // Le dernier élément devrait être 2 (ce mois)
        expect($restitutionsData[11])->toBe(2);
    });

    it('has proper dataset labels', function () {
        actingAs($this->user);

        $widget = Livewire::test(AttributionsChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['datasets'][0]['label'])->toBe('Nouvelles Attributions');
        expect($data['datasets'][1]['label'])->toBe('Restitutions');
    });
});

describe('MaterielsStatusChartWidget', function () {
    it('can render the materiels status chart widget', function () {
        actingAs($this->user);

        Livewire::test(MaterielsStatusChartWidget::class)
            ->assertSuccessful();
    });

    it('returns correct chart type', function () {
        actingAs($this->user);

        $widget = Livewire::test(MaterielsStatusChartWidget::class);

        expect($widget->instance()->getType())->toBe('doughnut');
    });

    it('counts materiels by status correctly', function () {
        actingAs($this->user);

        Materiel::factory()->count(3)->create(['statut' => 'disponible']);
        Materiel::factory()->count(2)->create(['statut' => 'attribué']);
        Materiel::factory()->count(1)->create(['statut' => 'en_panne']);
        Materiel::factory()->count(1)->create(['statut' => 'en_maintenance']);
        Materiel::factory()->count(1)->create(['statut' => 'rebuté']);

        $widget = Livewire::test(MaterielsStatusChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['datasets'][0]['data'])->toBe([3, 2, 1, 1, 1]);
    });

    it('has correct labels for each status', function () {
        actingAs($this->user);

        $widget = Livewire::test(MaterielsStatusChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['labels'])->toBe([
            'Disponibles',
            'Attribués',
            'En Panne',
            'En Maintenance',
            'Rebutés',
        ]);
    });

    it('has 5 different colors for statuses', function () {
        actingAs($this->user);

        $widget = Livewire::test(MaterielsStatusChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['datasets'][0]['backgroundColor'])->toHaveCount(5);
        expect($data['datasets'][0]['borderColor'])->toHaveCount(5);
    });
});

describe('MaterielsTypeChartWidget', function () {
    it('can render the materiels type chart widget', function () {
        actingAs($this->user);

        Livewire::test(MaterielsTypeChartWidget::class)
            ->assertSuccessful();
    });

    it('returns correct chart type', function () {
        actingAs($this->user);

        $widget = Livewire::test(MaterielsTypeChartWidget::class);

        expect($widget->instance()->getType())->toBe('bar');
    });

    it('displays only types with materiels', function () {
        actingAs($this->user);

        $type1 = MaterielType::factory()->create(['nom' => 'Ordinateur']);
        $type2 = MaterielType::factory()->create(['nom' => 'Écran']);
        $type3 = MaterielType::factory()->create(['nom' => 'Clavier']);

        Materiel::factory()->count(5)->create(['materiel_type_id' => $type1->id]);
        Materiel::factory()->count(2)->create(['materiel_type_id' => $type2->id]);
        // type3 n'a pas de matériels

        $widget = Livewire::test(MaterielsTypeChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['labels'])->toHaveCount(2);
        expect($data['labels'])->toContain('Ordinateur');
        expect($data['labels'])->toContain('Écran');
        expect($data['labels'])->not->toContain('Clavier');
    });

    it('orders types by count descending', function () {
        actingAs($this->user);

        $type1 = MaterielType::factory()->create(['nom' => 'Type A']);
        $type2 = MaterielType::factory()->create(['nom' => 'Type B']);
        $type3 = MaterielType::factory()->create(['nom' => 'Type C']);

        Materiel::factory()->count(2)->create(['materiel_type_id' => $type1->id]);
        Materiel::factory()->count(5)->create(['materiel_type_id' => $type2->id]);
        Materiel::factory()->count(3)->create(['materiel_type_id' => $type3->id]);

        $widget = Livewire::test(MaterielsTypeChartWidget::class);
        $data = $widget->instance()->getData();

        // Type B devrait être premier avec 5
        expect($data['labels'][0])->toBe('Type B');
        expect($data['datasets'][0]['data'][0])->toBe(5);

        // Type C devrait être deuxième avec 3
        expect($data['labels'][1])->toBe('Type C');
        expect($data['datasets'][0]['data'][1])->toBe(3);

        // Type A devrait être troisième avec 2
        expect($data['labels'][2])->toBe('Type A');
        expect($data['datasets'][0]['data'][2])->toBe(2);
    });

    it('generates correct number of colors', function () {
        actingAs($this->user);

        $type1 = MaterielType::factory()->create();
        $type2 = MaterielType::factory()->create();
        $type3 = MaterielType::factory()->create();

        Materiel::factory()->create(['materiel_type_id' => $type1->id]);
        Materiel::factory()->create(['materiel_type_id' => $type2->id]);
        Materiel::factory()->create(['materiel_type_id' => $type3->id]);

        $widget = Livewire::test(MaterielsTypeChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['datasets'][0]['backgroundColor'])->toHaveCount(3);
    });

    it('handles empty types correctly', function () {
        actingAs($this->user);

        $widget = Livewire::test(MaterielsTypeChartWidget::class);
        $data = $widget->instance()->getData();

        expect($data['labels'])->toBeArray();
        expect($data['labels'])->toHaveCount(0);
        expect($data['datasets'][0]['data'])->toHaveCount(0);
    });
});
