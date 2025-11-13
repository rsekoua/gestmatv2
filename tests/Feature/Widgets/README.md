# Tests des Widgets du Tableau de Bord

Ce dossier contient tous les tests pour les widgets du tableau de bord de l'application de gestion de matériel.

## Structure des Tests

### 1. **DashboardOverviewWidgetTest.php** (13 tests)
Tests pour le widget de vue d'ensemble principal qui affiche les statistiques clés.

**Couverture:**
- Rendu du widget
- Comptage des matériels et calcul du taux de disponibilité
- Couleurs des badges selon les seuils
- Statistiques des attributions actives et clôturées
- Taux d'employés équipés
- Génération des données de graphiques
- Gestion des cas limites (0 enregistrements)

### 2. **AlertsWidgetTest.php** (10 tests)
Tests pour le système d'alertes qui signale les problèmes critiques.

**Couverture:**
- Alertes pour matériels en panne
- Alertes pour matériels amortis (>3 ans)
- Alertes pour attributions de longue durée (>1 an)
- Alertes de stock critique (<20% disponible)
- Message de succès quand tout va bien
- URLs d'action dans les alertes

### 3. **ChartWidgetsTest.php** (14 tests)
Tests pour les 3 widgets de graphiques.

**Widgets testés:**
- `AttributionsChartWidget` - Graphique linéaire des attributions/restitutions
- `MaterielsStatusChartWidget` - Graphique en donut de répartition par statut
- `MaterielsTypeChartWidget` - Graphique en barres par type de matériel

**Couverture:**
- Types de graphiques corrects
- Génération des données
- Labels et datasets
- Couleurs et légendes
- Tri et filtrage des données

### 4. **TableWidgetsTest.php** (30 tests)
Tests pour les 3 widgets de tableaux.

**Widgets testés:**
- `RecentAttributionsWidget` - 10 dernières attributions
- `TopEmployeesWidget` - Top 10 employés par nombre d'attributions
- `TopMaterielsWidget` - Top 10 matériels par nombre d'attributions

**Couverture:**
- Affichage et rendu des tableaux
- Tri et ordre des enregistrements
- Limitation à 10 résultats
- Affichage des colonnes
- Badges et couleurs
- Actions sur les lignes

## Exécution des Tests

### Tous les tests de widgets
```bash
php artisan test --filter=Widget
```

Ou avec le groupe:
```bash
php artisan test --group=widgets
```

### Tests par fichier
```bash
# Vue d'ensemble
php artisan test tests/Feature/Widgets/DashboardOverviewWidgetTest.php

# Alertes
php artisan test tests/Feature/Widgets/AlertsWidgetTest.php

# Graphiques
php artisan test tests/Feature/Widgets/ChartWidgetsTest.php

# Tableaux
php artisan test tests/Feature/Widgets/TableWidgetsTest.php
```

### Tests spécifiques
```bash
# Un test précis
php artisan test --filter="displays correct total materiels count"

# Tests d'un describe group
php artisan test --filter="AttributionsChartWidget"
```

### Avec couverture de code
```bash
php artisan test --coverage --filter=Widget
```

## Statistiques

- **Total de tests:** 67
- **Fichiers de test:** 4
- **Widgets testés:** 8
- **Couverture:** Tous les widgets du tableau de bord

## Notes Importantes

1. **RefreshDatabase**: Tous les tests utilisent `RefreshDatabase` pour garantir un environnement propre.

2. **Factories**: Les tests utilisent les factories Laravel pour créer des données de test réalistes.

3. **Livewire Testing**: Les widgets Filament sont testés avec les utilitaires de test Livewire.

4. **Assertions Filament**: Tests spécifiques pour les tableaux Filament (colonnes, actions, badges).

5. **Cas limites**: Tous les tests incluent des cas avec 0 enregistrement pour vérifier la robustesse.

## Ajout de Nouveaux Tests

Pour ajouter des tests pour un nouveau widget:

1. Créer un nouveau fichier dans `tests/Feature/Widgets/`
2. Ajouter `uses()->group('widgets');` en début de fichier
3. Utiliser `beforeEach()` pour créer un utilisateur authentifié
4. Tester le rendu, les données, et les fonctionnalités spécifiques

### Template de base:
```php
<?php

use App\Filament\Widgets\MonNouveauWidget;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses()->group('widgets');

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render the widget', function () {
    actingAs($this->user);

    Livewire::test(MonNouveauWidget::class)
        ->assertSuccessful();
});
```

## Maintenance

Ces tests doivent être mis à jour si:
- Un widget est modifié (logique, colonnes, etc.)
- De nouveaux widgets sont ajoutés au tableau de bord
- Les modèles ou relations changent
- Les seuils d'alerte sont modifiés

## Contribution

Lors de l'ajout de fonctionnalités aux widgets:
1. Écrire les tests AVANT le code (TDD)
2. Vérifier que tous les tests passent
3. Ajouter des tests pour les cas limites
4. Documenter les nouveaux comportements
