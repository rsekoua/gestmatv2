<?php

use App\Filament\Widgets\RecentAttributionsWidget;
use App\Filament\Widgets\TopEmployeesWidget;
use App\Filament\Widgets\TopMaterielsWidget;
use App\Models\Attribution;
use App\Models\Employee;
use App\Models\Materiel;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses()->group('widgets');

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('RecentAttributionsWidget', function () {
    it('can render the recent attributions widget', function () {
        actingAs($this->user);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertSuccessful();
    });

    it('displays most recent attributions first', function () {
        actingAs($this->user);

        $old = Attribution::factory()->create([
            'date_attribution' => now()->subDays(5),
        ]);
        $recent = Attribution::factory()->create([
            'date_attribution' => now()->subDays(1),
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertCanSeeTableRecords([$recent, $old], inOrder: true);
    });

    it('limits to 10 records', function () {
        actingAs($this->user);

        Attribution::factory()->count(15)->create();

        $widget = Livewire::test(RecentAttributionsWidget::class);

        expect($widget->instance()->table($widget->instance()->table)
            ->getQuery()
            ->toBase()
            ->limit)->toBe(10);
    });

    it('displays numero decharge column', function () {
        actingAs($this->user);

        $attribution = Attribution::factory()->create([
            'numero_decharge_att' => 'TEST-001',
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertCanRenderTableColumn('numero_decharge_att');
    });

    it('displays materiel column with description', function () {
        actingAs($this->user);

        $materiel = Materiel::factory()->create([
            'numero_serie' => 'SN123456',
        ]);
        $attribution = Attribution::factory()->create([
            'materiel_id' => $materiel->id,
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertCanRenderTableColumn('materiel.nom');
    });

    it('displays employee column with service description', function () {
        actingAs($this->user);

        $employee = Employee::factory()->create();
        $attribution = Attribution::factory()->create([
            'employee_id' => $employee->id,
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertCanRenderTableColumn('employee.full_name');
    });

    it('displays status badge with correct color for active attribution', function () {
        actingAs($this->user);

        $attribution = Attribution::factory()->create([
            'date_restitution' => null,
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertTableColumnFormattedStateSet('date_restitution', 'Active', $attribution);
    });

    it('displays status badge with correct color for closed attribution', function () {
        actingAs($this->user);

        $attribution = Attribution::factory()->create([
            'date_restitution' => now(),
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertTableColumnFormattedStateSet('date_restitution', 'Clôturée', $attribution);
    });

    it('displays duration in days', function () {
        actingAs($this->user);

        $attribution = Attribution::factory()->create([
            'date_attribution' => now()->subDays(10),
            'date_restitution' => null,
        ]);

        Livewire::test(RecentAttributionsWidget::class)
            ->assertCanRenderTableColumn('duration_in_days');
    });

    it('has view action', function () {
        actingAs($this->user);

        $attribution = Attribution::factory()->create();

        Livewire::test(RecentAttributionsWidget::class)
            ->assertTableActionExists('view');
    });
});

describe('TopEmployeesWidget', function () {
    it('can render the top employees widget', function () {
        actingAs($this->user);

        Livewire::test(TopEmployeesWidget::class)
            ->assertSuccessful();
    });

    it('displays top 10 employees by attribution count', function () {
        actingAs($this->user);

        $employees = Employee::factory()->count(15)->create();

        // Créer des attributions pour les employés
        foreach ($employees as $index => $employee) {
            Attribution::factory()->count(15 - $index)->create([
                'employee_id' => $employee->id,
            ]);
        }

        $widget = Livewire::test(TopEmployeesWidget::class);

        expect($widget->instance()->table($widget->instance()->table)
            ->getQuery()
            ->toBase()
            ->limit)->toBe(10);
    });

    it('orders employees by attribution count descending', function () {
        actingAs($this->user);

        $employee1 = Employee::factory()->create(['nom' => 'Alpha']);
        $employee2 = Employee::factory()->create(['nom' => 'Beta']);
        $employee3 = Employee::factory()->create(['nom' => 'Gamma']);

        Attribution::factory()->count(2)->create(['employee_id' => $employee1->id]);
        Attribution::factory()->count(5)->create(['employee_id' => $employee2->id]);
        Attribution::factory()->count(3)->create(['employee_id' => $employee3->id]);

        Livewire::test(TopEmployeesWidget::class)
            ->assertCanSeeTableRecords([$employee2, $employee3, $employee1], inOrder: true);
    });

    it('displays only employees with attributions', function () {
        actingAs($this->user);

        $employeeWithAttr = Employee::factory()->create();
        $employeeWithoutAttr = Employee::factory()->create();

        Attribution::factory()->create(['employee_id' => $employeeWithAttr->id]);

        Livewire::test(TopEmployeesWidget::class)
            ->assertCanSeeTableRecords([$employeeWithAttr])
            ->assertCanNotSeeTableRecords([$employeeWithoutAttr]);
    });

    it('displays rank column with badges', function () {
        actingAs($this->user);

        $employee = Employee::factory()->create();
        Attribution::factory()->create(['employee_id' => $employee->id]);

        Livewire::test(TopEmployeesWidget::class)
            ->assertCanRenderTableColumn('rank');
    });

    it('displays full name with service description', function () {
        actingAs($this->user);

        $employee = Employee::factory()->create();
        Attribution::factory()->create(['employee_id' => $employee->id]);

        Livewire::test(TopEmployeesWidget::class)
            ->assertCanRenderTableColumn('full_name');
    });

    it('displays total attributions count', function () {
        actingAs($this->user);

        $employee = Employee::factory()->create();
        Attribution::factory()->count(5)->create(['employee_id' => $employee->id]);

        Livewire::test(TopEmployeesWidget::class)
            ->assertCanRenderTableColumn('attributions_count');
    });

    it('displays active attributions count', function () {
        actingAs($this->user);

        $employee = Employee::factory()->create();
        Attribution::factory()->count(3)->create([
            'employee_id' => $employee->id,
            'date_restitution' => null,
        ]);
        Attribution::factory()->count(2)->create([
            'employee_id' => $employee->id,
            'date_restitution' => now(),
        ]);

        Livewire::test(TopEmployeesWidget::class)
            ->assertCanRenderTableColumn('active_attributions_count');
    });
});

describe('TopMaterielsWidget', function () {
    it('can render the top materiels widget', function () {
        actingAs($this->user);

        Livewire::test(TopMaterielsWidget::class)
            ->assertSuccessful();
    });

    it('displays top 10 materiels by attribution count', function () {
        actingAs($this->user);

        $materiels = Materiel::factory()->count(15)->create();

        foreach ($materiels as $index => $materiel) {
            Attribution::factory()->count(15 - $index)->create([
                'materiel_id' => $materiel->id,
            ]);
        }

        $widget = Livewire::test(TopMaterielsWidget::class);

        expect($widget->instance()->table($widget->instance()->table)
            ->getQuery()
            ->toBase()
            ->limit)->toBe(10);
    });

    it('orders materiels by attribution count descending', function () {
        actingAs($this->user);

        $materiel1 = Materiel::factory()->create();
        $materiel2 = Materiel::factory()->create();
        $materiel3 = Materiel::factory()->create();

        Attribution::factory()->count(2)->create(['materiel_id' => $materiel1->id]);
        Attribution::factory()->count(5)->create(['materiel_id' => $materiel2->id]);
        Attribution::factory()->count(3)->create(['materiel_id' => $materiel3->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanSeeTableRecords([$materiel2, $materiel3, $materiel1], inOrder: true);
    });

    it('displays only materiels with attributions', function () {
        actingAs($this->user);

        $materielWithAttr = Materiel::factory()->create();
        $materielWithoutAttr = Materiel::factory()->create();

        Attribution::factory()->create(['materiel_id' => $materielWithAttr->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanSeeTableRecords([$materielWithAttr])
            ->assertCanNotSeeTableRecords([$materielWithoutAttr]);
    });

    it('displays rank column with badges', function () {
        actingAs($this->user);

        $materiel = Materiel::factory()->create();
        Attribution::factory()->create(['materiel_id' => $materiel->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanRenderTableColumn('rank');
    });

    it('displays materiel name with serial number description', function () {
        actingAs($this->user);

        $materiel = Materiel::factory()->create([
            'numero_serie' => 'SN123',
        ]);
        Attribution::factory()->create(['materiel_id' => $materiel->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanRenderTableColumn('nom');
    });

    it('displays materiel type badge', function () {
        actingAs($this->user);

        $materiel = Materiel::factory()->create();
        Attribution::factory()->create(['materiel_id' => $materiel->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanRenderTableColumn('materielType.nom');
    });

    it('displays attributions count', function () {
        actingAs($this->user);

        $materiel = Materiel::factory()->create();
        Attribution::factory()->count(7)->create(['materiel_id' => $materiel->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanRenderTableColumn('attributions_count');
    });

    it('displays status with correct badge color', function () {
        actingAs($this->user);

        $materiel = Materiel::factory()->create(['statut' => 'disponible']);
        Attribution::factory()->create(['materiel_id' => $materiel->id]);

        Livewire::test(TopMaterielsWidget::class)
            ->assertCanRenderTableColumn('statut');
    });
});
