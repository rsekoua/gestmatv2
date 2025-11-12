# 📦 Package Complet - Migrations et Modèles UUID

## Application de Gestion de Matériel Informatique

---

## 📋 Contenu du Package

Ce package contient tous les fichiers nécessaires pour la base de données et les modèles de l'application de gestion de matériel informatique avec support UUID.

### 🗄️ Migrations (8 fichiers)

Toutes les migrations utilisent des UUID au lieu de BIGINT auto-increment.

1. **`2025_01_01_000001_create_services_table.php`**
   - Table pour les services/départements
   - Champs : nom, code, responsable

2. **`2025_01_01_000002_create_employees_table.php`**
   - Table pour les employés
   - Champs : nom, prenom, email, telephone, poste, service_id
   - Relation : belongsTo Service

3. **`2025_01_01_000003_create_materiel_types_table.php`**
   - Table pour les types de matériel
   - Champs : nom, description

4. **`2025_01_01_000004_create_materiels_table.php`**
   - Table principale pour le matériel
   - Champs : nom, marque, modele, numero_serie, specifications, purchase_date, purchase_price, statut, etat_physique, notes
   - Relation : belongsTo MaterielType
   - Statuts : disponible, attribué, en_panne, en_maintenance, rebuté
   - États physiques : excellent, bon, moyen, mauvais

5. **`2025_01_01_000005_create_accessories_table.php`**
   - Table pour les accessoires
   - Champs : nom, description

6. **`2025_01_01_000006_create_attributions_table.php`**
   - Table pour les attributions de matériel
   - Champs attribution : date_attribution, numero_decharge_att, observations_att
   - Champs restitution : date_restitution, numero_decharge_res, observations_res, etat_general_res, etat_fonctionnel_res, dommages_res, decision_res
   - Relations : belongsTo Materiel, belongsTo Employee

7. **`2025_01_01_000007_create_accessoire_attribution_table.php`**
   - Table pivot pour accessoires ↔ attributions
   - Champs : statut_att, statut_res

8. **`2025_01_01_000008_create_discharge_documents_table.php`**
   - Table pour stocker les métadonnées des PDF
   - Champs : type, numero_decharge, file_path, generated_at
   - Relation : belongsTo Attribution

### 💻 Modèles (7 fichiers)

Tous les modèles utilisent le trait `HasUuids` et `LogsActivity` (sauf pour les tables de référence).

1. **`Service.php`**
   - Gestion des services/départements
   - Relations : hasMany employees
   - Accessors : full_name (nom + code)

2. **`Employee.php`**
   - Gestion des employés
   - Relations : belongsTo service, hasMany attributions, hasMany activeAttributions
   - Accessors : full_name, full_name_with_email
   - Activity Log : ✅

3. **`MaterielType.php`**
   - Gestion des types de matériel
   - Relations : hasMany materiels
   - Méthodes : supportsAutoDepreciation()

4. **`Materiel.php`**
   - Gestion du matériel informatique
   - Relations : belongsTo materielType, hasMany attributions, hasOne activeAttribution
   - Accessors : is_amorti (uniquement pour ordinateurs > 3 ans), amortissement_status, full_description, full_description_with_serial
   - Scopes : available(), attributed(), depreciated(), ofType()
   - Activity Log : ✅

5. **`Accessory.php`**
   - Gestion des accessoires
   - Relations : belongsToMany attributions

6. **`Attribution.php`**
   - Gestion des attributions et restitutions
   - Relations : belongsTo materiel, belongsTo employee, belongsToMany accessories, hasMany dischargeDocuments
   - Accessors : duration_in_days
   - Méthodes : isActive(), isClosed(), generateAttributionNumber(), generateRestitutionNumber()
   - Scopes : active(), closed()
   - Activity Log : ✅
   - **Auto-génération des numéros** : ATT-YYYY-NNNN et RES-YYYY-NNNN

7. **`DischargeDocument.php`**
   - Gestion des documents PDF générés
   - Relations : belongsTo attribution
   - Accessors : url, file_name
   - Méthodes : fileExists(), deleteFile()
   - **Auto-suppression** du fichier physique lors de la suppression du modèle

### 🌱 Seeders (3 fichiers)

1. **`MaterielTypeSeeder.php`**
   - Crée les 11 types de matériel par défaut :
     - Ordinateur Portable
     - Ordinateur Bureau
     - Imprimante
     - Écran
     - Smartphone
     - Tablette
     - Vidéoprojecteur
     - Serveur
     - Switch
     - Routeur
     - Autre

2. **`AccessorySeeder.php`**
   - Crée les 10 accessoires par défaut :
     - Chargeur/Câble alimentation
     - Souris
     - Câble réseau
     - Sacoche/Housse
     - Documentation
     - Clé USB
     - Casque audio
     - Webcam
     - Clavier externe
     - Adaptateur

3. **`DatabaseSeeder.php`**
   - Orchestre l'exécution des seeders

### 📚 Documentation (2 fichiers)

1. **`GUIDE_INSTALLATION.md`**
   - Guide complet d'installation
   - Configuration requise
   - Instructions pas à pas
   - Exemples d'utilisation
   - Tests
   - Dépannage

2. **`README.md`** (ce fichier)
   - Vue d'ensemble du package
   - Structure des fichiers

---

## 🎯 Caractéristiques Principales

### ✅ UUID Primary Keys

Tous les modèles utilisent des UUID au lieu de BIGINT auto-increment :
- Meilleure sécurité (identifiants non prédictibles)
- Facilite les migrations de données
- Compatible avec les architectures distribuées

### ✅ Relations Complètes

Toutes les relations Eloquent sont configurées :
- `belongsTo`, `hasMany`, `hasOne`, `belongsToMany`
- Eager loading optimisé
- Scopes personnalisés

### ✅ Audit Log Automatique

Intégration `spatie/laravel-activitylog` :
- Traçabilité complète des actions
- Historique des modifications
- Attribution des actions aux utilisateurs

### ✅ Règles Métier Implémentées

1. **Amortissement Intelligent**
   - Calcul automatique uniquement pour les ordinateurs (Portable et Bureau)
   - Durée : 3 ans
   - Autres équipements : gestion manuelle (Phase 2)

2. **Génération Automatique des Numéros**
   - Numéros d'attribution : `ATT-YYYY-NNNN`
   - Numéros de restitution : `RES-YYYY-NNNN`
   - Compteurs annuels

3. **Gestion des Statuts**
   - Matériel : disponible, attribué, en_panne, en_maintenance, rebuté
   - État physique : excellent, bon, moyen, mauvais
   - Accessoires : fourni, restitué, manquant

---

## 🚀 Installation Rapide

```bash
# 1. Copier les fichiers dans votre projet Laravel
cp migrations/*.php votre-projet/database/migrations/
cp models/*.php votre-projet/app/Models/
cp seeders/*.php votre-projet/database/seeders/

# 2. Installer les dépendances
cd votre-projet
composer require spatie/laravel-activitylog

# 3. Configurer activity log pour UUID
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
# Modifier la migration activity_log pour utiliser nullableUuidMorphs

# 4. Exécuter les migrations
php artisan migrate

# 5. Exécuter les seeders
php artisan db:seed
```

---

## 📊 Schéma de Base de Données

```
┌─────────────┐
│  services   │
└──────┬──────┘
       │
       │ 1:N
       │
┌──────▼──────┐     ┌──────────────────┐
│  employees  │     │ materiel_types   │
└──────┬──────┘     └────────┬─────────┘
       │                     │
       │ 1:N                 │ 1:N
       │                     │
       │            ┌────────▼─────────┐
       │            │   materiels      │
       │            └────────┬─────────┘
       │                     │
       │                     │ 1:N
       │                     │
┌──────▼─────────────────────▼────────┐
│          attributions               │
└──────┬──────────────────────┬───────┘
       │                      │
       │ 1:N                  │ 1:N
       │                      │
┌──────▼──────────┐   ┌───────▼──────────────┐
│ discharge_      │   │ accessoire_          │
│ documents       │   │ attribution          │
└─────────────────┘   └───────┬──────────────┘
                              │
                              │ N:1
                              │
                     ┌────────▼─────────┐
                     │   accessories    │
                     └──────────────────┘
```

---

## 🔑 Points Clés UUID

### Génération Automatique

Les UUID sont générés automatiquement par Laravel grâce au trait `HasUuids` :

```php
$service = Service::create(['nom' => 'IT']);
echo $service->id; // 9d8f7e6d-5c4b-3a2f-1e0d-9c8b7a6f5e4d
```

### Relations avec UUID

Les foreign keys utilisent `foreignUuid()` au lieu de `foreignId()` :

```php
$table->foreignUuid('service_id')
    ->constrained('services')
    ->onDelete('set null');
```

### Requêtes avec UUID

Les requêtes fonctionnent exactement comme avec BIGINT :

```php
$employee = Employee::find('9d8f7e6d-5c4b-3a2f-1e0d-9c8b7a6f5e4d');
$service = Service::findOrFail($uuid);
```

---

## 📝 Exemples d'Utilisation

### Créer une Attribution Complète

```php
use App\Models\{Service, Employee, MaterielType, Materiel, Accessory, Attribution};

// 1. Créer un service
$service = Service::create([
    'nom' => 'Service IT',
    'code' => 'IT',
]);

// 2. Créer un employé
$employee = Employee::create([
    'service_id' => $service->id,
    'nom' => 'Kouassi',
    'prenom' => 'Jean',
    'email' => 'jean.kouassi@example.com',
]);

// 3. Créer un matériel
$type = MaterielType::where('nom', 'Ordinateur Portable')->first();
$materiel = Materiel::create([
    'materiel_type_id' => $type->id,
    'nom' => 'PC-001',
    'marque' => 'Dell',
    'modele' => 'Latitude 5420',
    'numero_serie' => 'SN123456',
    'purchase_date' => now()->subYears(2),
    'statut' => 'disponible',
]);

// 4. Créer une attribution
$attribution = Attribution::create([
    'materiel_id' => $materiel->id,
    'employee_id' => $employee->id,
    'date_attribution' => now(),
]);

// Le numéro est généré automatiquement
echo $attribution->numero_decharge_att; // ATT-2025-0001

// 5. Ajouter des accessoires
$chargeur = Accessory::where('nom', 'Chargeur/Câble alimentation')->first();
$attribution->accessories()->attach($chargeur->id, [
    'statut_att' => 'fourni'
]);

// 6. Mettre à jour le statut du matériel
$materiel->update(['statut' => 'attribué']);
```

---

## ⚠️ Points d'Attention

### 1. Activity Log et UUID

La migration `activity_log` de spatie doit être modifiée pour supporter les UUID :

```php
// ❌ Incorrect
$table->nullableMorphs('subject');

// ✅ Correct
$table->nullableUuidMorphs('subject');
```

### 2. Performance

Les UUID sont légèrement plus lents que les BIGINT, mais la différence est négligeable. Optimisations :
- Utiliser des index sur les colonnes UUID fréquemment requêtées
- Utiliser `eager loading` pour réduire le nombre de requêtes

### 3. Amortissement

Le calcul automatique d'amortissement ne s'applique **QUE** aux types :
- Ordinateur Portable
- Ordinateur Bureau

Les autres équipements nécessitent une gestion manuelle (prévu Phase 2).

---

## 🔄 Prochaines Étapes

Après l'installation de la base de données et des modèles :

1. **Développement Filament**
   - Créer les ressources CRUD
   - Configurer les formulaires
   - Créer les actions personnalisées

2. **Génération PDF**
   - Implémenter les templates de fiches de décharge
   - Intégrer les QR codes
   - Gérer le stockage des documents

3. **Dashboard**
   - Créer les widgets statistiques
   - Implémenter les graphiques
   - Configurer les alertes

4. **Import/Export**
   - Actions d'import CSV/Excel
   - Validation des données
   - Rapports d'erreurs

---

## 📞 Support

Pour toute question ou problème :

1. Consultez le **GUIDE_INSTALLATION.md**
2. Vérifiez la section **Dépannage**
3. Consultez la documentation Laravel et Filament

---

## 📄 Licence

Ce package fait partie de l'application de gestion de matériel informatique développée pour l'organisation.

---

**Version** : 1.0  
**Date** : 12 Novembre 2025  
**Auteur** : Équipe DSI

---

**✨ Package Prêt à l'Emploi !**

Tous les fichiers sont configurés avec UUID et prêts pour le développement Filament.
