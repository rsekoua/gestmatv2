<?php

use App\Filament\Widgets\AlertsWidget;
use App\Models\Attribution;
use App\Models\Materiel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses()->group('widgets');

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'admin User',
        'email' => 'admin@local.host',
        'password' => Hash::make('password'),
    ]);
});

it('can render the alerts widget', function () {
    actingAs($this->user);

    Livewire::test(AlertsWidget::class)
        ->assertSuccessful();
});

it('displays alert when materiels are broken', function () {
    actingAs($this->user);

    Materiel::factory()->count(3)->create(['statut' => 'en_panne']);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $brokenAlert = collect($alerts)->firstWhere('type', 'warning');

    expect($brokenAlert)->not->toBeNull()
        ->and($brokenAlert['title'])->toBe('Matériels en panne')
        ->and($brokenAlert['message'])->toContain('3 matériel(s)');
});

it('displays alert for depreciated materiels', function () {
    actingAs($this->user);

    // Créer des ordinateurs de plus de 3 ans
    Materiel::factory()->count(2)->create([
        'purchase_date' => now()->subYears(4),
    ]);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $depreciatedAlert = collect($alerts)->firstWhere('title', 'Matériels amortis');

    expect($depreciatedAlert)->not->toBeNull();
    expect($depreciatedAlert['type'])->toBe('info');
    expect($depreciatedAlert['message'])->toContain('2 ordinateur(s)');
});

it('displays alert for long duration attributions', function () {
    actingAs($this->user);

    // Créer des attributions de plus d'un an
    Attribution::factory()->count(2)->create([
        'date_attribution' => now()->subYears(2),
        'date_restitution' => null,
    ]);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $longDurationAlert = collect($alerts)->firstWhere('title', 'Attributions de longue durée');

    expect($longDurationAlert)->not->toBeNull();
    expect($longDurationAlert['type'])->toBe('warning');
    expect($longDurationAlert['message'])->toContain('2 attribution(s)');
});

it('displays critical stock alert when availability is low', function () {
    actingAs($this->user);

    // 1 disponible sur 10 = 10% (< 20%)
    Materiel::factory()->count(1)->create(['statut' => 'disponible']);
    Materiel::factory()->count(9)->create(['statut' => 'attribué']);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $stockAlert = collect($alerts)->firstWhere('title', 'Stock critique');

    expect($stockAlert)->not->toBeNull();
    expect($stockAlert['type'])->toBe('danger');
    expect($stockAlert['message'])->toContain('1 matériel(s) disponible(s)');
    expect($stockAlert['message'])->toContain('10');
});

it('does not display stock alert when availability is sufficient', function () {
    actingAs($this->user);

    // 5 disponibles sur 10 = 50% (> 20%)
    Materiel::factory()->count(5)->create(['statut' => 'disponible']);
    Materiel::factory()->count(5)->create(['statut' => 'attribué']);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $stockAlert = collect($alerts)->firstWhere('title', 'Stock critique');

    expect($stockAlert)->toBeNull();
});

it('displays success message when no alerts', function () {
    actingAs($this->user);

    // Créer un système sain
    Materiel::factory()->count(5)->create(['statut' => 'disponible']);
    Materiel::factory()->count(3)->create(['statut' => 'attribué']);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $successAlert = collect($alerts)->firstWhere('type', 'success');

    expect($successAlert)->not->toBeNull();
    expect($successAlert['title'])->toBe('Système opérationnel');
    expect($successAlert['message'])->toContain('Aucune alerte');
});

it('displays multiple alerts when multiple issues exist', function () {
    actingAs($this->user);

    // Créer plusieurs problèmes
    Materiel::factory()->count(2)->create(['statut' => 'en_panne']);
    Materiel::factory()->count(1)->create(['statut' => 'disponible']);
    Materiel::factory()->count(9)->create(['statut' => 'attribué']);

    Attribution::factory()->create([
        'date_attribution' => now()->subYears(2),
        'date_restitution' => null,
    ]);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    expect(count($alerts))->toBeGreaterThan(1);
    expect(collect($alerts)->pluck('title')->toArray())->toContain('Matériels en panne');
    expect(collect($alerts)->pluck('title')->toArray())->toContain('Stock critique');
});

it('includes action urls in alerts', function () {
    actingAs($this->user);

    Materiel::factory()->count(2)->create(['statut' => 'en_panne']);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $brokenAlert = collect($alerts)->firstWhere('type', 'warning');

    expect($brokenAlert['action'])->not->toBeNull();
    expect($brokenAlert['url'])->not->toBeNull();
    expect($brokenAlert['url'])->toContain('filament');
});

it('does not include action url for success message', function () {
    actingAs($this->user);

    Materiel::factory()->count(5)->create(['statut' => 'disponible']);

    $widget = Livewire::test(AlertsWidget::class);
    $alerts = $widget->instance()->getAlerts();

    $successAlert = collect($alerts)->firstWhere('type', 'success');

    expect($successAlert['action'])->toBeNull();
    expect($successAlert['url'])->toBeNull();
});
