<?php

use App\Filament\Widgets\DashboardOverviewWidget;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses()->group('widgets');

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render the dashboard overview widget', function () {
    actingAs($this->user);

    Livewire::test(DashboardOverviewWidget::class)
        ->assertSuccessful();
});

it('displays correct total materiels count', function () {
    actingAs($this->user);

    Materiel::factory()->count(5)->create();

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[0]->getValue())->toBe('5');
    expect($stats[0]->getLabel())->toBe('Matériels Totaux');
});

it('calculates availability rate correctly', function () {
    actingAs($this->user);

    // 3 disponibles sur 10 = 30%
    Materiel::factory()->count(3)->create(['statut' => 'disponible']);
    Materiel::factory()->count(5)->create(['statut' => 'attribué']);
    Materiel::factory()->count(2)->create(['statut' => 'en_panne']);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[1]->getValue())->toBe('30%');
    expect($stats[1]->getLabel())->toBe('Taux de Disponibilité');
});

it('displays availability rate with correct color based on value', function () {
    actingAs($this->user);

    // Test avec taux élevé (success)
    Materiel::factory()->count(8)->create(['statut' => 'disponible']);
    Materiel::factory()->count(2)->create(['statut' => 'attribué']);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[1]->getColor())->toBe('success');

    // Test avec taux moyen (warning)
    Materiel::query()->delete();
    Materiel::factory()->count(3)->create(['statut' => 'disponible']);
    Materiel::factory()->count(7)->create(['statut' => 'attribué']);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[1]->getColor())->toBe('warning');

    // Test avec taux faible (danger)
    Materiel::query()->delete();
    Materiel::factory()->count(1)->create(['statut' => 'disponible']);
    Materiel::factory()->count(9)->create(['statut' => 'attribué']);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[1]->getColor())->toBe('danger');
});

it('displays active attributions count', function () {
    actingAs($this->user);

    Attribution::factory()->count(3)->create(['date_restitution' => null]);
    Attribution::factory()->count(2)->create(['date_restitution' => now()]);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[2]->getValue())->toBe('3');
    expect($stats[2]->getLabel())->toBe('Attributions Actives');
});

it('displays closed attributions this month in description', function () {
    actingAs($this->user);

    Attribution::factory()->count(2)->create([
        'date_restitution' => now(),
    ]);
    Attribution::factory()->count(3)->create([
        'date_restitution' => now()->subMonths(2),
    ]);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[2]->getDescription())->toContain('2 clôturées ce mois');
});

it('calculates employees equipped rate correctly', function () {
    actingAs($this->user);

    // 2 employés avec attributions actives sur 5 total = 40%
    $employees = Employee::factory()->count(5)->create();
    Attribution::factory()->create([
        'employee_id' => $employees[0]->id,
        'date_restitution' => null,
    ]);
    Attribution::factory()->create([
        'employee_id' => $employees[1]->id,
        'date_restitution' => null,
    ]);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[3]->getValue())->toBe('40%');
    expect($stats[3]->getDescription())->toContain('2 / 5 employés');
});

it('displays services count', function () {
    actingAs($this->user);

    Service::factory()->count(7)->create();

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[4]->getValue())->toBe('7');
    expect($stats[4]->getLabel())->toBe('Services');
});

it('generates correct chart data for last 7 months', function () {
    actingAs($this->user);

    // Créer des matériels à différentes dates
    Materiel::factory()->create(['created_at' => now()->subMonths(1)]);
    Materiel::factory()->create(['created_at' => now()->subMonths(1)]);
    Materiel::factory()->create(['created_at' => now()->subMonths(3)]);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $chartData = $widget->instance()->call('getMaterielsMonthlyData');

    expect($chartData)->toBeArray();
    expect(count($chartData))->toBe(7);
});

it('handles zero materiels correctly', function () {
    actingAs($this->user);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[0]->getValue())->toBe('0');
    expect($stats[1]->getValue())->toBe('0%');
});

it('handles zero employees correctly', function () {
    actingAs($this->user);

    $widget = Livewire::test(DashboardOverviewWidget::class);
    $stats = $widget->instance()->getStats();

    expect($stats[3]->getValue())->toBe('0%');
    expect($stats[3]->getDescription())->toContain('0 / 0 employés');
});
