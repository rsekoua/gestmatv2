# 🚀 GestMat v2 - Laravel + Vue.js
## Plan Complet de Développement pour O2switch

**Date:** 2025-11-15
**Stack:** Laravel 12 + Vue 3 + Inertia.js
**Déploiement:** O2switch hébergement mutualisé

---

## 🎯 Vue d'ensemble

### Pourquoi Laravel + Vue.js ?

✅ **Meilleur des deux mondes:**
- Backend Laravel (que vous connaissez déjà)
- Frontend Vue.js moderne et réactif
- Interface utilisateur riche type SPA
- Réutilisation de votre code Laravel actuel

✅ **Avantages sur Laravel + Filament:**
- Interface 100% personnalisée
- Expérience utilisateur moderne
- Performance frontend optimale
- Contrôle total du design

✅ **Compatible O2switch:**
- Backend PHP Laravel (mutualisé friendly)
- Frontend compilé en assets statiques
- Pas de serveur Node.js requis en production

---

## 🏗️ Deux Architectures Possibles

### Architecture 1: Laravel + Inertia.js + Vue 3 ⭐ (RECOMMANDÉ)

**Principe:**
- Inertia.js = "Pont" entre Laravel et Vue
- Pas d'API à créer
- Routing Laravel classique
- Components Vue pour l'UI

**Avantages:**
✅ Plus simple et rapide à développer
✅ Moins de code boilerplate
✅ Authentification Laravel native
✅ Partage de données facile
✅ SEO possible avec SSR
✅ Parfait pour applications monolithiques

**Inconvénients:**
⚠️ Frontend et backend couplés
⚠️ Pas d'app mobile native facilement

**Compatible O2switch:** ✅✅✅✅✅ (100%)

---

### Architecture 2: Laravel API + Vue 3 SPA

**Principe:**
- Laravel comme API REST/GraphQL pure
- Vue 3 SPA totalement découplée
- Communication via axios

**Avantages:**
✅ Frontend/Backend totalement séparés
✅ Réutilisation API (mobile, etc.)
✅ Équipes frontend/backend indépendantes
✅ Scalabilité optimale

**Inconvénients:**
⚠️ Plus complexe (2 apps à gérer)
⚠️ Authentification plus compliquée (tokens)
⚠️ Déploiement double
⚠️ Développement plus long

**Compatible O2switch:** ✅✅✅✅ (90%)

---

## 🎯 Recommandation: Laravel + Inertia.js + Vue 3

**Pourquoi Inertia.js:**

1. **Simplicité:** Combine la simplicité de Laravel avec la puissance de Vue
2. **Productivité:** Développement rapide (comme Livewire mais avec Vue)
3. **Moderne:** Interface SPA sans complexité API
4. **Migration facile:** Réutilisez vos models, controllers Laravel actuels
5. **O2switch friendly:** Compilation en assets statiques

---

## 📋 Stack Technique Complète

```yaml
Backend:
  Framework: Laravel 12.x
  ORM: Eloquent
  Auth: Laravel Breeze avec Inertia
  Validation: Form Requests
  Queue: Database (+ Cron O2switch)

Frontend:
  Framework: Vue 3 (Composition API)
  Bridge: Inertia.js 1.x
  State: Pinia
  Router: Inertia Router (pas Vue Router)
  UI Library:
    - Tailwind CSS 4
    - Headless UI
    - Heroicons

Build:
  Bundler: Vite 5
  CSS: PostCSS + Tailwind

Database:
  Engine: MySQL 8.0

Libraries:
  PDF: Laravel DomPDF
  Excel: Maatwebsite Excel
  Forms: Inertia Forms
  Tables: Custom Vue components
  Charts: Chart.js ou ApexCharts

Admin UI:
  Dashboard: Custom Vue components
  Tables: TanStack Table (Vue)
  Forms: Vuelidate ou VeeValidate
  Notifications: Notivue ou vue-toastification

Deploy:
  Server: O2switch (Apache + cPanel)
  Build: npm run build (local ou CI/CD)
  Deploy: Git push + composer install
```

---

## 📁 Structure Projet Laravel + Inertia + Vue

```
gestmatv2-vue/
├── app/
│   ├── Models/
│   │   ├── Materiel.php
│   │   ├── Employee.php
│   │   ├── Attribution.php
│   │   └── ...
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── MaterielController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── AttributionController.php
│   │   │   └── DashboardController.php
│   │   └── Requests/
│   │       ├── StoreMaterielRequest.php
│   │       └── ...
│   └── Services/
│       ├── PdfService.php
│       └── ExportService.php
│
├── resources/
│   ├── js/
│   │   ├── app.js                    # Point d'entrée
│   │   ├── Pages/                    # Pages Inertia
│   │   │   ├── Dashboard.vue
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   └── Register.vue
│   │   │   ├── Materiels/
│   │   │   │   ├── Index.vue         # Liste
│   │   │   │   ├── Create.vue        # Création
│   │   │   │   ├── Edit.vue          # Édition
│   │   │   │   └── Show.vue          # Détails
│   │   │   ├── Employees/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   └── Edit.vue
│   │   │   ├── Attributions/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   └── Show.vue
│   │   │   └── Reports/
│   │   │       └── Index.vue
│   │   ├── Components/              # Composants réutilisables
│   │   │   ├── Layout/
│   │   │   │   ├── AppLayout.vue
│   │   │   │   ├── Sidebar.vue
│   │   │   │   └── Navbar.vue
│   │   │   ├── UI/
│   │   │   │   ├── Button.vue
│   │   │   │   ├── Input.vue
│   │   │   │   ├── Select.vue
│   │   │   │   ├── Modal.vue
│   │   │   │   └── Table.vue
│   │   │   ├── Materiels/
│   │   │   │   ├── MaterielCard.vue
│   │   │   │   ├── MaterielTable.vue
│   │   │   │   └── MaterielForm.vue
│   │   │   └── Dashboard/
│   │   │       ├── StatsCard.vue
│   │   │       └── ChartWidget.vue
│   │   ├── Composables/             # Composition API
│   │   │   ├── useMateriel.js
│   │   │   ├── useEmployee.js
│   │   │   └── useAttribution.js
│   │   └── Stores/                  # Pinia stores
│   │       ├── auth.js
│   │       └── notification.js
│   └── css/
│       └── app.css                  # Tailwind imports
│
├── routes/
│   └── web.php                      # Routes Inertia
│
├── database/
│   ├── migrations/                  # Réutiliser existantes
│   └── factories/
│
├── public/
│   └── build/                       # Assets compilés (Vite)
│
├── deployer/
│   └── vue/
│       ├── .env.vue.o2switch
│       ├── deploy-vue.sh
│       └── README_VUE_O2SWITCH.md
│
├── package.json
├── vite.config.js
├── tailwind.config.js
└── composer.json
```

---

## 🚀 Plan de Développement Détaillé

### Phase 1: Setup & Installation (2-3 jours)

#### Jour 1: Installation de base

```bash
# 1. Créer nouveau projet Laravel
composer create-project laravel/laravel gestmatv2-vue
cd gestmatv2-vue

# 2. Installer Inertia.js server-side
composer require inertiajs/inertia-laravel

# 3. Installer Laravel Breeze avec Inertia + Vue
composer require laravel/breeze --dev
php artisan breeze:install vue

# Sélectionner:
# - Vue 3
# - Inertia
# - SSR: No (pour O2switch)
# - Pest: Yes

# 4. Installer dépendances NPM
npm install

# 5. Installer dépendances UI supplémentaires
npm install @headlessui/vue @heroicons/vue
npm install pinia
npm install chart.js vue-chartjs
npm install @tanstack/vue-table
```

#### Jour 2: Configuration base de données

```bash
# 1. Copier migrations de votre projet actuel
cp ../gestmatv2/database/migrations/* database/migrations/

# 2. Copier models
cp -r ../gestmatv2/app/Models/* app/Models/

# 3. Configuration .env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=gestmatv2_vue
DB_USERNAME=root
DB_PASSWORD=

# 4. Migrations
php artisan migrate

# 5. Seeders (optionnel)
php artisan db:seed
```

#### Jour 3: Structure Vue + Layout de base

**Créer le layout principal:**

```vue
<!-- resources/js/Pages/Layouts/AppLayout.vue -->
<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
const page = usePage()
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg">
      <nav class="mt-8">
        <Link href="/dashboard" class="nav-link">
          Dashboard
        </Link>
        <Link href="/materiels" class="nav-link">
          Matériels
        </Link>
        <Link href="/employees" class="nav-link">
          Employés
        </Link>
        <Link href="/attributions" class="nav-link">
          Attributions
        </Link>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64">
      <header class="bg-white shadow">
        <div class="px-4 py-6">
          <h1 class="text-2xl font-bold">
            {{ page.props.title }}
          </h1>
        </div>
      </header>

      <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
          <slot />
        </div>
      </div>
    </main>
  </div>
</template>
```

---

### Phase 2: CRUD Matériels (3-4 jours)

#### Backend - Controller

```php
// app/Http/Controllers/MaterielController.php
<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\MaterielType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaterielController extends Controller
{
    public function index(Request $request)
    {
        $materiels = Materiel::query()
            ->with(['materielType'])
            ->when($request->search, function ($query, $search) {
                $query->where('designation', 'like', "%{$search}%")
                      ->orWhere('serial_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Materiels/Index', [
            'materiels' => $materiels,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Materiels/Create', [
            'materielTypes' => MaterielType::all(),
        ]);
    }

    public function store(StoreMaterielRequest $request)
    {
        $materiel = Materiel::create($request->validated());

        return redirect()->route('materiels.index')
            ->with('success', 'Matériel créé avec succès.');
    }

    public function edit(Materiel $materiel)
    {
        return Inertia::render('Materiels/Edit', [
            'materiel' => $materiel->load('materielType'),
            'materielTypes' => MaterielType::all(),
        ]);
    }

    public function update(UpdateMaterielRequest $request, Materiel $materiel)
    {
        $materiel->update($request->validated());

        return redirect()->route('materiels.index')
            ->with('success', 'Matériel mis à jour.');
    }

    public function destroy(Materiel $materiel)
    {
        $materiel->delete();

        return redirect()->route('materiels.index')
            ->with('success', 'Matériel supprimé.');
    }
}
```

#### Frontend - Liste des matériels

```vue
<!-- resources/js/Pages/Materiels/Index.vue -->
<script setup>
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Pages/Layouts/AppLayout.vue'
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  materiels: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')
const status = ref(props.filters.status || '')

// Recherche avec debounce
const searchMateriales = () => {
  router.get('/materiels', {
    search: search.value,
    status: status.value,
  }, {
    preserveState: true,
    replace: true,
  })
}

// Status badge color
const statusColor = (status) => {
  const colors = {
    disponible: 'bg-green-100 text-green-800',
    attribué: 'bg-blue-100 text-blue-800',
    en_panne: 'bg-red-100 text-red-800',
    maintenance: 'bg-yellow-100 text-yellow-800',
    obsolète: 'bg-gray-100 text-gray-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Matériels</h2>
        <Link
          :href="route('materiels.create')"
          class="btn-primary"
        >
          <PlusIcon class="w-5 h-5 mr-2" />
          Nouveau matériel
        </Link>
      </div>

      <!-- Filtres -->
      <div class="bg-white p-4 rounded-lg shadow space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Recherche -->
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-3 w-5 h-5 text-gray-400" />
            <input
              v-model="search"
              @input="searchMateriales"
              type="text"
              placeholder="Rechercher..."
              class="pl-10 w-full rounded-lg border-gray-300"
            />
          </div>

          <!-- Filtre statut -->
          <select
            v-model="status"
            @change="searchMateriales"
            class="rounded-lg border-gray-300"
          >
            <option value="">Tous les statuts</option>
            <option value="disponible">Disponible</option>
            <option value="attribué">Attribué</option>
            <option value="en_panne">En panne</option>
            <option value="maintenance">Maintenance</option>
            <option value="obsolète">Obsolète</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Désignation
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                N° Série
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Statut
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="materiel in materiels.data"
              :key="materiel.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">
                  {{ materiel.designation }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ materiel.marque }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ materiel.materiel_type?.nom }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ materiel.serial_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="statusColor(materiel.status)"
                  class="px-2 py-1 text-xs rounded-full"
                >
                  {{ materiel.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <Link
                  :href="route('materiels.edit', materiel.id)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Modifier
                </Link>
                <button
                  @click="deleteMateriel(materiel.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t">
          <Pagination :links="materiels.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
```

#### Frontend - Formulaire création

```vue
<!-- resources/js/Pages/Materiels/Create.vue -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Pages/Layouts/AppLayout.vue'

const props = defineProps({
  materielTypes: Array,
})

const form = useForm({
  designation: '',
  marque: '',
  modele: '',
  serial_number: '',
  materiel_type_id: null,
  status: 'disponible',
  physical_condition: 'excellent',
  specifications: '',
  date_acquisition: null,
  prix_acquisition: null,
  fournisseur: '',
})

const submit = () => {
  form.post(route('materiels.store'))
}
</script>

<template>
  <AppLayout>
    <div class="max-w-2xl">
      <h2 class="text-2xl font-bold mb-6">Nouveau Matériel</h2>

      <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6 space-y-6">
        <!-- Désignation -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Désignation *
          </label>
          <input
            v-model="form.designation"
            type="text"
            required
            class="w-full rounded-lg border-gray-300"
          />
          <div v-if="form.errors.designation" class="text-red-600 text-sm mt-1">
            {{ form.errors.designation }}
          </div>
        </div>

        <!-- Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Type de matériel *
          </label>
          <select
            v-model="form.materiel_type_id"
            required
            class="w-full rounded-lg border-gray-300"
          >
            <option :value="null">Sélectionner un type</option>
            <option
              v-for="type in materielTypes"
              :key="type.id"
              :value="type.id"
            >
              {{ type.nom }}
            </option>
          </select>
          <div v-if="form.errors.materiel_type_id" class="text-red-600 text-sm mt-1">
            {{ form.errors.materiel_type_id }}
          </div>
        </div>

        <!-- Marque et Modèle -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Marque
            </label>
            <input
              v-model="form.marque"
              type="text"
              class="w-full rounded-lg border-gray-300"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Modèle
            </label>
            <input
              v-model="form.modele"
              type="text"
              class="w-full rounded-lg border-gray-300"
            />
          </div>
        </div>

        <!-- N° Série -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Numéro de série *
          </label>
          <input
            v-model="form.serial_number"
            type="text"
            required
            class="w-full rounded-lg border-gray-300"
          />
        </div>

        <!-- Statut et Condition -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Statut
            </label>
            <select v-model="form.status" class="w-full rounded-lg border-gray-300">
              <option value="disponible">Disponible</option>
              <option value="attribué">Attribué</option>
              <option value="en_panne">En panne</option>
              <option value="maintenance">Maintenance</option>
              <option value="obsolète">Obsolète</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              État physique
            </label>
            <select v-model="form.physical_condition" class="w-full rounded-lg border-gray-300">
              <option value="excellent">Excellent</option>
              <option value="bon">Bon</option>
              <option value="moyen">Moyen</option>
              <option value="mauvais">Mauvais</option>
            </select>
          </div>
        </div>

        <!-- Spécifications -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Spécifications techniques
          </label>
          <textarea
            v-model="form.specifications"
            rows="3"
            class="w-full rounded-lg border-gray-300"
          />
        </div>

        <!-- Prix et Date -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Date d'acquisition
            </label>
            <input
              v-model="form.date_acquisition"
              type="date"
              class="w-full rounded-lg border-gray-300"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Prix d'acquisition
            </label>
            <input
              v-model="form.prix_acquisition"
              type="number"
              step="0.01"
              class="w-full rounded-lg border-gray-300"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3 pt-4">
          <Link
            :href="route('materiels.index')"
            class="btn-secondary"
          >
            Annuler
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="btn-primary"
          >
            {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
```

---

### Phase 3: Dashboard avec Charts (2-3 jours)

```vue
<!-- resources/js/Pages/Dashboard.vue -->
<script setup>
import { computed } from 'vue'
import { Bar, Doughnut, Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
} from 'chart.js'
import AppLayout from '@/Pages/Layouts/AppLayout.vue'
import StatsCard from '@/Components/Dashboard/StatsCard.vue'

// Enregistrer Chart.js components
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement
)

const props = defineProps({
  stats: Object,
  chartData: Object,
})

// Configuration graphique
const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h2 class="text-2xl font-bold">Tableau de bord</h2>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <StatsCard
          title="Total Matériels"
          :value="stats.total_materials"
          icon="📦"
          color="blue"
        />
        <StatsCard
          title="Disponibles"
          :value="stats.available_materials"
          icon="✅"
          color="green"
        />
        <StatsCard
          title="Attribués"
          :value="stats.assigned_materials"
          icon="👥"
          color="yellow"
        />
        <StatsCard
          title="En Panne"
          :value="stats.broken_materials"
          icon="⚠️"
          color="red"
        />
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Répartition par type -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-semibold mb-4">Répartition par type</h3>
          <div class="h-64">
            <Doughnut
              :data="chartData.materialsByType"
              :options="barChartOptions"
            />
          </div>
        </div>

        <!-- Évolution attributions -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-semibold mb-4">Attributions mensuelles</h3>
          <div class="h-64">
            <Line
              :data="chartData.attributionsPerMonth"
              :options="barChartOptions"
            />
          </div>
        </div>
      </div>

      <!-- Dernières attributions -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6">
          <h3 class="text-lg font-semibold mb-4">Dernières attributions</h3>
          <table class="min-w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left py-2">N° Attribution</th>
                <th class="text-left py-2">Employé</th>
                <th class="text-left py-2">Matériel</th>
                <th class="text-left py-2">Date</th>
                <th class="text-left py-2">Statut</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="attribution in stats.recent_attributions"
                :key="attribution.id"
                class="border-b hover:bg-gray-50"
              >
                <td class="py-3">{{ attribution.attribution_number }}</td>
                <td class="py-3">{{ attribution.employee?.full_name }}</td>
                <td class="py-3">{{ attribution.materiel?.designation }}</td>
                <td class="py-3">{{ attribution.date_attribution }}</td>
                <td class="py-3">
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    {{ attribution.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
```

---

### Phase 4: Système Attribution (4-5 jours)

**Backend Controller:**

```php
// app/Http/Controllers/AttributionController.php
public function create()
{
    return Inertia::render('Attributions/Create', [
        'employees' => Employee::select('id', 'nom', 'prenom', 'matricule')->get(),
        'materiels' => Materiel::where('status', 'disponible')
            ->with('materielType')
            ->get(),
        'accessories' => Accessory::all(),
    ]);
}

public function store(StoreAttributionRequest $request)
{
    $attribution = Attribution::create([
        'materiel_id' => $request->materiel_id,
        'employee_id' => $request->employee_id,
        'service_id' => $request->service_id,
        'date_attribution' => now(),
        'attribution_number' => Attribution::generateAttributionNumber(),
        'status' => 'en_cours',
    ]);

    // Attacher accessoires
    if ($request->accessories) {
        $attribution->accessories()->attach($request->accessories);
    }

    // Mettre à jour statut matériel
    Materiel::find($request->materiel_id)->update(['status' => 'attribué']);

    // Générer PDF de décharge
    $pdf = app(PdfService::class)->generateDischarge($attribution);

    return redirect()->route('attributions.show', $attribution)
        ->with('success', 'Attribution créée avec succès.');
}
```

**Frontend:**

```vue
<!-- resources/js/Pages/Attributions/Create.vue -->
<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Pages/Layouts/AppLayout.vue'
import Combobox from '@/Components/UI/Combobox.vue'

const props = defineProps({
  employees: Array,
  materiels: Array,
  accessories: Array,
})

const form = useForm({
  employee_id: null,
  materiel_id: null,
  accessories: [],
  observations: '',
})

const selectedEmployee = computed(() => {
  return props.employees.find(e => e.id === form.employee_id)
})

const selectedMateriel = computed(() => {
  return props.materiels.find(m => m.id === form.materiel_id)
})

const submit = () => {
  form.post(route('attributions.store'))
}
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl">
      <h2 class="text-2xl font-bold mb-6">Nouvelle Attribution</h2>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Étape 1: Sélection employé -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">1. Employé</h3>

          <Combobox
            v-model="form.employee_id"
            :options="employees"
            label-key="full_name"
            value-key="id"
            placeholder="Rechercher un employé..."
          />

          <!-- Aperçu employé sélectionné -->
          <div v-if="selectedEmployee" class="mt-4 p-4 bg-gray-50 rounded">
            <p><strong>Nom:</strong> {{ selectedEmployee.full_name }}</p>
            <p><strong>Matricule:</strong> {{ selectedEmployee.matricule }}</p>
            <p><strong>Service:</strong> {{ selectedEmployee.service?.nom }}</p>
          </div>
        </div>

        <!-- Étape 2: Sélection matériel -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">2. Matériel</h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
              v-for="materiel in materiels"
              :key="materiel.id"
              @click="form.materiel_id = materiel.id"
              :class="[
                'p-4 border-2 rounded-lg cursor-pointer transition',
                form.materiel_id === materiel.id
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-200 hover:border-gray-300'
              ]"
            >
              <h4 class="font-semibold">{{ materiel.designation }}</h4>
              <p class="text-sm text-gray-600">{{ materiel.marque }}</p>
              <p class="text-xs text-gray-500">{{ materiel.materiel_type?.nom }}</p>
            </div>
          </div>
        </div>

        <!-- Étape 3: Accessoires (optionnel) -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">3. Accessoires (optionnel)</h3>

          <div class="space-y-2">
            <label
              v-for="accessory in accessories"
              :key="accessory.id"
              class="flex items-center space-x-2"
            >
              <input
                v-model="form.accessories"
                type="checkbox"
                :value="accessory.id"
                class="rounded"
              />
              <span>{{ accessory.designation }}</span>
            </label>
          </div>
        </div>

        <!-- Observations -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">4. Observations</h3>

          <textarea
            v-model="form.observations"
            rows="4"
            class="w-full rounded-lg border-gray-300"
            placeholder="Notes additionnelles..."
          />
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
          <Link :href="route('attributions.index')" class="btn-secondary">
            Annuler
          </Link>
          <button
            type="submit"
            :disabled="form.processing || !form.employee_id || !form.materiel_id"
            class="btn-primary"
          >
            Créer l'attribution
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
```

---

### Phase 5: PDF & Export (2-3 jours)

**Continuer dans le prochain message...**

Voulez-vous que je continue avec:
1. ✅ La suite du plan (PDF, Export, Tests, Déploiement O2switch)
2. ✅ Les fichiers de configuration complets
3. ✅ Script d'installation automatisé

?
