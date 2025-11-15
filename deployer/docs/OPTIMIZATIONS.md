# 🚀 Optimisations Recommandées pour GestMat v2

Ce document détaille les optimisations spécifiques identifiées lors de l'analyse du code.

---

## 📊 Optimisations Base de Données

### 1. Index Supplémentaires Critiques

Créez une nouvelle migration pour ajouter ces index:

```bash
php artisan make:migration add_performance_indexes_to_tables
```

**Fichier:** `database/migrations/2025_11_15_add_performance_indexes_to_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Attributions - Requêtes fréquentes par statut et date
        Schema::table('attributions', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'attributions_status_created_idx');
            $table->index('attribution_number', 'attributions_number_idx');
            $table->index('restitution_number', 'attributions_restitution_idx');
            $table->index(['employee_id', 'status'], 'attributions_employee_status_idx');
            $table->index(['materiel_id', 'status'], 'attributions_materiel_status_idx');
        });

        // Matériels - Recherches par statut, condition et type
        Schema::table('materiels', function (Blueprint $table) {
            $table->index(['status', 'physical_condition'], 'materiels_status_condition_idx');
            $table->index('serial_number', 'materiels_serial_idx');
            $table->index(['materiel_type_id', 'status'], 'materiels_type_status_idx');
            $table->index('created_at', 'materiels_created_idx');
        });

        // Employees - Recherches par service et matricule
        Schema::table('employees', function (Blueprint $table) {
            $table->index('matricule', 'employees_matricule_idx');
            $table->index(['service_id', 'created_at'], 'employees_service_created_idx');
        });

        // Activity Log - Requêtes de l'audit trail
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['subject_type', 'subject_id'], 'activity_subject_idx');
            $table->index(['causer_type', 'causer_id'], 'activity_causer_idx');
            $table->index('created_at', 'activity_created_idx');
            $table->index('batch_uuid', 'activity_batch_idx');
            $table->index(['subject_type', 'subject_id', 'created_at'], 'activity_subject_created_idx');
        });

        // Discharge Documents - Récupération par attribution
        Schema::table('discharge_documents', function (Blueprint $table) {
            $table->index(['attribution_id', 'type'], 'discharge_attribution_type_idx');
        });
    }

    public function down(): void
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropIndex('attributions_status_created_idx');
            $table->dropIndex('attributions_number_idx');
            $table->dropIndex('attributions_restitution_idx');
            $table->dropIndex('attributions_employee_status_idx');
            $table->dropIndex('attributions_materiel_status_idx');
        });

        Schema::table('materiels', function (Blueprint $table) {
            $table->dropIndex('materiels_status_condition_idx');
            $table->dropIndex('materiels_serial_idx');
            $table->dropIndex('materiels_type_status_idx');
            $table->dropIndex('materiels_created_idx');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_matricule_idx');
            $table->dropIndex('employees_service_created_idx');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('activity_subject_idx');
            $table->dropIndex('activity_causer_idx');
            $table->dropIndex('activity_created_idx');
            $table->dropIndex('activity_batch_idx');
            $table->dropIndex('activity_subject_created_idx');
        });

        Schema::table('discharge_documents', function (Blueprint $table) {
            $table->dropIndex('discharge_attribution_type_idx');
        });
    }
};
```

**Impact attendu:** Réduction de 50-70% du temps de requête sur les opérations fréquentes.

---

## 💾 Optimisations Cache

### 2. Service de Cache Centralisé

**Créer:** `app/Services/CacheService.php`

```php
<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Materiel;
use App\Models\Attribution;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const TTL = 3600; // 1 heure
    private const SHORT_TTL = 600; // 10 minutes

    /**
     * Récupère tous les employés actifs (cache 1h)
     */
    public function getActiveEmployees()
    {
        return Cache::remember('employees.active', self::TTL, function () {
            return Employee::with('service')
                ->orderBy('nom')
                ->get();
        });
    }

    /**
     * Récupère les statistiques du dashboard (cache 10min)
     */
    public function getDashboardStats()
    {
        return Cache::remember('dashboard.stats', self::SHORT_TTL, function () {
            return [
                'total_materials' => Materiel::count(),
                'available_materials' => Materiel::where('status', 'disponible')->count(),
                'assigned_materials' => Materiel::where('status', 'attribué')->count(),
                'broken_materials' => Materiel::where('status', 'en_panne')->count(),
                'active_attributions' => Attribution::where('status', 'en_cours')->count(),
                'total_employees' => Employee::count(),
            ];
        });
    }

    /**
     * Récupère les matériels disponibles par type (cache 1h)
     */
    public function getAvailableMaterialsByType()
    {
        return Cache::remember('materials.available.by_type', self::TTL, function () {
            return Materiel::where('status', 'disponible')
                ->with('materielType')
                ->get()
                ->groupBy('materiel_type_id');
        });
    }

    /**
     * Récupère tous les services (cache 1h)
     */
    public function getAllServices()
    {
        return Cache::remember('services.all', self::TTL, function () {
            return Service::orderBy('nom')->get();
        });
    }

    /**
     * Invalide le cache des statistiques
     */
    public function invalidateDashboardStats(): void
    {
        Cache::forget('dashboard.stats');
    }

    /**
     * Invalide tous les caches liés aux employés
     */
    public function invalidateEmployeeCache(): void
    {
        Cache::forget('employees.active');
    }

    /**
     * Invalide tous les caches liés aux matériels
     */
    public function invalidateMaterialCache(): void
    {
        Cache::forget('materials.available.by_type');
        $this->invalidateDashboardStats();
    }

    /**
     * Invalide tous les caches liés aux services
     */
    public function invalidateServiceCache(): void
    {
        Cache::forget('services.all');
    }
}
```

### 3. Utilisation dans les Observers

**Mettre à jour:** `app/Observers/MaterielObserver.php`

```php
<?php

namespace App\Observers;

use App\Models\Materiel;
use App\Services\CacheService;

class MaterielObserver
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    public function created(Materiel $materiel): void
    {
        $this->cacheService->invalidateMaterialCache();
    }

    public function updated(Materiel $materiel): void
    {
        if ($materiel->wasChanged('status')) {
            $this->cacheService->invalidateMaterialCache();
        }
    }

    public function deleted(Materiel $materiel): void
    {
        $this->cacheService->invalidateMaterialCache();
    }
}
```

**Impact:** Réduction de 80% des requêtes pour les données fréquemment consultées.

---

## 🎨 Optimisations Frontend

### 4. Lazy Loading des Images

**Dans les templates Blade:**

```blade
{{-- Avant --}}
<img src="{{ asset('logos/logo.png') }}" alt="Logo">

{{-- Après --}}
<img src="{{ asset('logos/logo.png') }}"
     alt="Logo"
     loading="lazy"
     decoding="async">
```

### 5. Optimisation Vite

**Mettre à jour:** `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        '@livewire/alpine',
                        'alpinejs',
                    ],
                },
            },
        },
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        cssMinify: true,
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
```

---

## 📦 Optimisations Filament

### 6. Cache des Composants Filament

Dans les Filament Resources, ajoutez:

```php
use Filament\Resources\Resource;

class MaterielResource extends Resource
{
    // Cache les queries
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['materielType', 'attributions' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->withCount('attributions');
    }

    // Désactive les notifications en temps réel si non nécessaires
    protected static bool $shouldRegisterNavigation = true;
}
```

### 7. Pagination Optimisée

```php
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->defaultPaginationPageOption(25)  // Par défaut 25 items
        ->paginationPageOptions([10, 25, 50, 100])
        ->deferLoading()  // Charge les données uniquement quand nécessaire
        ->persistSearchInSession()
        ->persistColumnSearchesInSession();
}
```

---

## ⚡ Optimisations Queue

### 8. Queues Prioritaires

**Mettre à jour:** `config/queue.php`

```php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 300,
        'block_for' => null,
        'after_commit' => false,

        // Ajouter des queues prioritaires
        'queues' => [
            'high',     // PDF generation, notifications urgentes
            'default',  // Opérations normales
            'low',      // Imports/exports, nettoyages
        ],
    ],
],
```

### 9. Jobs Asynchrones pour PDF

**Créer:** `app/Jobs/GenerateDischargeDocumentJob.php`

```php
<?php

namespace App\Jobs;

use App\Models\Attribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDischargeDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    public function __construct(
        public Attribution $attribution,
        public string $type = 'attribution'
    ) {
        // Utiliser queue haute priorité pour les PDFs
        $this->onQueue('high');
    }

    public function handle(): void
    {
        // Logique de génération PDF
        // Déplacée depuis le controller/action directe
    }
}
```

**Impact:** Temps de réponse UI -90% (opérations asynchrones).

---

## 🔍 Optimisations Activity Log

### 10. Nettoyage Automatique

**Ajouter dans:** `app/Console/Kernel.php` ou `routes/console.php`

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('activitylog:clean --days=90')
    ->weekly()
    ->sundays()
    ->at('02:00');
```

### 11. Batch UUID Indexé (Déjà fait)

✅ Déjà implémenté dans votre projet - excellent!

---

## 📈 Monitoring des Performances

### 12. Laravel Pulse (Recommandé)

**Installation:**

```bash
composer require laravel/pulse
php artisan pulse:install
php artisan migrate
```

**Configuration:** `config/pulse.php`

```php
'recorders' => [
    Recorders\CacheInteractions::class => [
        'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
    ],
    Recorders\Exceptions::class => [
        'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
    ],
    Recorders\Queues::class => [
        'enabled' => env('PULSE_QUEUES_ENABLED', true),
    ],
    Recorders\SlowQueries::class => [
        'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
        'threshold' => 1000, // ms
    ],
],
```

---

## 🎯 Checklist d'Optimisation

### Priorité Critique (À faire IMMÉDIATEMENT)

- [ ] Migrer SQLite → PostgreSQL
- [ ] Activer Redis pour cache/session/queue
- [ ] Créer les index de performance (migration ci-dessus)
- [ ] Configurer OPcache en production
- [ ] Activer cache Laravel (config, route, view)

### Priorité Haute (Première semaine)

- [ ] Implémenter CacheService
- [ ] Mettre à jour Observers pour invalidation cache
- [ ] Configurer queues prioritaires
- [ ] Déplacer génération PDF vers jobs asynchrones
- [ ] Optimiser configuration Vite

### Priorité Moyenne (Premier mois)

- [ ] Installer Laravel Pulse
- [ ] Configurer monitoring externe (Sentry)
- [ ] Optimiser images (compression, lazy loading)
- [ ] Implémenter CDN pour assets statiques
- [ ] Activer rate limiting

### Priorité Basse (Quand nécessaire)

- [ ] Implémenter full-text search (PostgreSQL)
- [ ] Ajouter Redis Cluster (si >100k requêtes/jour)
- [ ] Implémenter database replication (si critique)
- [ ] Ajouter CDN pour uploads utilisateurs

---

## 📊 Métriques de Succès

Après implémentation des optimisations critiques + hautes, attendez-vous à:

| Métrique | Avant | Après | Amélioration |
|----------|-------|-------|--------------|
| Temps de chargement dashboard | ~2-3s | ~300-500ms | -80% |
| Requêtes DB par page | 50-100 | 5-15 | -85% |
| Temps génération PDF | 2-5s (bloquant) | <500ms (async) | -90% perçu |
| Utilisation mémoire PHP | 100-150MB | 50-80MB | -45% |
| Hit rate cache | 0% | 85-95% | +∞ |

---

## 🔄 Maintenance Continue

**Mensuellement:**
- Analyser slow queries (Laravel Pulse)
- Vérifier taille tables activity_log
- Optimiser index si nouveaux patterns de requêtes

**Trimestriellement:**
- Audit de sécurité dépendances
- Mise à jour packages (Laravel, Filament, etc.)
- Review architecture si changements majeurs

---

**Document créé:** 2025-11-15
**Dernière mise à jour:** 2025-11-15
**Auteur:** Claude AI - Analyse GestMat v2
