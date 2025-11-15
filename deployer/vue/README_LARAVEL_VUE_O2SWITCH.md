# 🚀 GestMat v2 - Laravel + Vue.js sur O2switch

Guide complet de déploiement **Laravel 12 + Vue 3 + Inertia.js** sur hébergement mutualisé O2switch.

---

## 🎯 Vue d'ensemble

**Stack Technique:**
- **Backend:** Laravel 12 + Eloquent ORM
- **Frontend:** Vue 3 (Composition API)
- **Bridge:** Inertia.js 1.x
- **UI:** Tailwind CSS 4 + Headless UI
- **Build:** Vite 5
- **Database:** MySQL 8.0

**Architecture:**
```
┌─────────────────────────────────────────┐
│           O2switch Serveur              │
├─────────────────────────────────────────┤
│  Apache + PHP 8.3                       │
│  ├── Laravel (Backend)                  │
│  ├── MySQL (Database)                   │
│  └── Assets compilés Vue (public/build) │
└─────────────────────────────────────────┘
```

**Différence clé vs version Filament:**
- **Filament:** Admin auto-généré (Livewire)
- **Vue.js:** Interface 100% personnalisée (Vue components)

---

## ⚠️ Point Crucial: Build Assets

### Principe Important

Sur **O2switch hébergement mutualisé:**
- ❌ **Node.js serveur persistant** non disponible
- ❌ **`npm run dev`** ne fonctionne pas en production
- ✅ **Assets pré-compilés** requis

### Workflow de Build

**Sur votre machine locale:**
```bash
# 1. Développement
npm run dev          # Serveur Vite local (http://localhost:5173)

# 2. Build production
npm run build        # Compile dans public/build/
```

**Sur O2switch:**
```bash
# Pas de npm run dev!
# Seulement les assets déjà compilés dans public/build/
```

---

## 📋 Pré-requis

### Machine Locale (Développement)

- [ ] PHP 8.3+ avec extensions (pdo, mysql, gd, etc.)
- [ ] Composer 2.x
- [ ] Node.js 20+ & npm
- [ ] Git
- [ ] MySQL local (ou SQLite pour dev)

### O2switch (Production)

- [ ] Compte O2switch actif
- [ ] Base MySQL créée (cPanel)
- [ ] PHP 8.3 activé
- [ ] Extensions PHP activées
- [ ] Domaine configuré
- [ ] SSL AutoSSL activé

---

## 🚀 Installation Locale

### Étape 1: Créer le Projet

```bash
# 1. Nouveau projet Laravel
composer create-project laravel/laravel gestmatv2-vue
cd gestmatv2-vue

# 2. Installer Inertia + Breeze (Vue)
composer require inertiajs/inertia-laravel
composer require laravel/breeze --dev
php artisan breeze:install vue

# Choisir:
# - Vue 3
# - Inertia
# - SSR: No
# - Pest: Yes

# 3. Installer dépendances NPM
npm install

# 4. Installer packages UI
npm install @headlessui/vue @heroicons/vue pinia chart.js vue-chartjs
```

### Étape 2: Configuration Base de Données

```bash
# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=gestmatv2_vue
DB_USERNAME=root
DB_PASSWORD=

# Créer la base
mysql -u root -p -e "CREATE DATABASE gestmatv2_vue"

# Migrations
php artisan migrate
```

### Étape 3: Lancer Dev Server

```bash
# Terminal 1: Laravel server
php artisan serve
# http://localhost:8000

# Terminal 2: Vite server
npm run dev
# http://localhost:5173 (proxy vers Laravel)
```

---

## 📦 Structure Projet

```
gestmatv2-vue/
├── app/
│   ├── Models/              # Eloquent models
│   ├── Http/
│   │   ├── Controllers/     # Inertia controllers
│   │   └── Requests/        # Form validation
│   └── Services/            # Business logic
│
├── resources/
│   ├── js/
│   │   ├── app.js           # Point d'entrée Vue
│   │   ├── Pages/           # Pages Inertia (Vue)
│   │   │   ├── Dashboard.vue
│   │   │   ├── Materiels/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   └── Edit.vue
│   │   │   ├── Employees/
│   │   │   └── Attributions/
│   │   ├── Components/      # Composants réutilisables
│   │   │   ├── Layout/
│   │   │   ├── UI/
│   │   │   └── Forms/
│   │   └── Composables/     # Composition API
│   └── css/
│       └── app.css
│
├── routes/
│   └── web.php              # Routes Inertia
│
├── public/
│   └── build/               # ⚠️ Assets compilés (Git ignored)
│
└── package.json
```

---

## 🎨 Développement Interface

### Page Inertia Exemple

```vue
<!-- resources/js/Pages/Materiels/Index.vue -->
<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  materiels: Object,
})

const search = ref('')

const searchMaterials = () => {
  router.get('/materiels', { search: search.value }, {
    preserveState: true,
  })
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold">Matériels</h1>

      <!-- Search -->
      <input
        v-model="search"
        @input="searchMaterials"
        type="text"
        placeholder="Rechercher..."
        class="w-full rounded-lg"
      />

      <!-- Table -->
      <table class="min-w-full">
        <thead>
          <tr>
            <th>Désignation</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="materiel in materiels.data" :key="materiel.id">
            <td>{{ materiel.designation }}</td>
            <td>{{ materiel.materiel_type?.nom }}</td>
            <td>{{ materiel.status }}</td>
            <td>
              <Link :href="`/materiels/${materiel.id}/edit`">
                Modifier
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>
```

### Controller Inertia

```php
// app/Http/Controllers/MaterielController.php
use Inertia\Inertia;

class MaterielController extends Controller
{
    public function index(Request $request)
    {
        $materiels = Materiel::query()
            ->with('materielType')
            ->when($request->search, function ($query, $search) {
                $query->where('designation', 'like', "%{$search}%");
            })
            ->paginate(25);

        return Inertia::render('Materiels/Index', [
            'materiels' => $materiels,
        ]);
    }
}
```

---

## 🏗️ Build pour Production

### Sur Machine Locale

```bash
# 1. Build assets production
npm run build

# ✅ Génère public/build/ avec:
# - app-[hash].js
# - app-[hash].css
# - manifest.json
# - assets optimisés

# 2. Vérifier build
ls -lh public/build/

# 3. Tester en local (mode production)
php artisan serve
# Vérifier que l'app fonctionne sans `npm run dev`
```

### Options de Déploiement Build

**Option 1: Commiter dans Git** ⭐ (Recommandé pour petits projets)
```bash
# Ajouter public/build dans Git
echo "!public/build" >> .gitignore  # Annuler l'ignore
git add public/build
git commit -m "Build production assets"
git push
```

**Option 2: Upload manuel**
```bash
# Via cPanel Gestionnaire de fichiers
# Upload local public/build/ → serveur public/build/
```

**Option 3: GitHub Actions** (Avancé)
```yaml
# .github/workflows/build.yml
- run: npm ci
- run: npm run build
- run: git add public/build && git commit && git push
```

---

## 🚀 Déploiement O2switch

### Méthode 1: Installation Automatique

```bash
# Via SSH sur O2switch
ssh votrenom@votredomaine.com

# Cloner projet
git clone https://github.com/votre-org/gestmatv2-vue.git
cd gestmatv2-vue

# Lancer script
bash deployer/vue/deploy-laravel-vue.sh
```

### Méthode 2: Manuel

#### Étape 1: Upload Code

```bash
# Via SSH
git clone https://github.com/votre-org/gestmatv2-vue.git ~/gestmatv2-vue

# Ou via cPanel Gestionnaire de fichiers
# Upload ZIP + Extract
```

#### Étape 2: Configuration

```bash
cd ~/gestmatv2-vue

# Copier .env
cp deployer/vue/.env.laravel-vue.o2switch .env

# Éditer .env
nano .env
# Remplir DB_*, MAIL_*, APP_URL
```

#### Étape 3: Installation

```bash
# Composer
composer install --no-dev --optimize-autoloader

# Générer clé
php artisan key:generate

# Migrations
php artisan migrate --force

# Storage link
php artisan storage:link

# Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chmod -R 755 storage bootstrap/cache
```

#### Étape 4: Lien public_html

```bash
# Backup ancien public_html
mv ~/public_html ~/public_html.backup

# Lien symbolique
ln -s ~/gestmatv2-vue/public ~/public_html
```

#### Étape 5: Cron Job

**cPanel > Tâches Cron:**
```bash
*/5 * * * * cd /home/cpaneluser/gestmatv2-vue && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

---

## ⚙️ Configuration Vite pour Production

```javascript
// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    'vue-vendor': ['vue', '@inertiajs/vue3'],
                    'ui-vendor': ['@headlessui/vue', '@heroicons/vue'],
                },
            },
        },
    },
})
```

---

## 🔄 Workflow Mise à Jour

### Développement → Production

```bash
# 1. LOCAL: Développement
git checkout -b feature/new-feature
# ... développement ...
npm run dev  # Tester

# 2. LOCAL: Build
npm run build
git add .
git commit -m "Add new feature"
git push

# 3. LOCAL: Merge to main
git checkout main
git merge feature/new-feature
git push origin main

# 4. O2SWITCH: Deploy
ssh votrenom@votredomaine.com
cd ~/gestmatv2-vue
bash deployer/vue/deploy-laravel-vue.sh
```

---

## 🎨 Composants Vue Réutilisables

### Button Component

```vue
<!-- resources/js/Components/UI/Button.vue -->
<script setup>
defineProps({
  variant: {
    type: String,
    default: 'primary'
  },
  size: {
    type: String,
    default: 'md'
  }
})

const variants = {
  primary: 'bg-blue-600 hover:bg-blue-700 text-white',
  secondary: 'bg-gray-200 hover:bg-gray-300 text-gray-800',
  danger: 'bg-red-600 hover:bg-red-700 text-white',
}

const sizes = {
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-4 py-2 text-base',
  lg: 'px-6 py-3 text-lg',
}
</script>

<template>
  <button
    :class="[
      'rounded-lg font-medium transition',
      variants[variant],
      sizes[size]
    ]"
  >
    <slot />
  </button>
</template>
```

### Modal Component

```vue
<!-- resources/js/Components/UI/Modal.vue -->
<script setup>
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'

defineProps({
  show: Boolean,
  maxWidth: {
    type: String,
    default: '2xl'
  }
})

const emit = defineEmits(['close'])
</script>

<template>
  <TransitionRoot :show="show" as="template">
    <Dialog @close="emit('close')" class="relative z-50">
      <!-- Backdrop -->
      <TransitionChild
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50" />
      </TransitionChild>

      <!-- Modal -->
      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            enter="ease-out duration-300"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              :class="`w-full max-w-${maxWidth} bg-white rounded-lg p-6`"
            >
              <slot />
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
```

---

## 📊 Performance Optimisations

### Lazy Loading Routes

```javascript
// resources/js/app.js
const pages = import.meta.glob('./Pages/**/*.vue')

createInertiaApp({
    resolve: name => {
        const page = pages[`./Pages/${name}.vue`]
        return page()  // Lazy load
    },
    // ...
})
```

### Code Splitting

```javascript
// vite.config.js
rollupOptions: {
    output: {
        manualChunks(id) {
            if (id.includes('node_modules')) {
                return 'vendor'
            }
            if (id.includes('Pages')) {
                return 'pages'
            }
        }
    }
}
```

---

## 🐛 Troubleshooting

### Assets non chargés

**Symptôme:** CSS/JS manquants, console errors

**Solution:**
```bash
# Vérifier public/build existe
ls -la public/build/

# Re-build si nécessaire (LOCAL)
npm run build

# Vérifier APP_URL dans .env
grep APP_URL .env
```

### Erreur "Vite manifest not found"

**Cause:** Build assets manquant

**Solution:**
```bash
# LOCAL: Build
npm run build

# Upload public/build/ vers serveur
# Ou commit dans Git
```

### Page blanche après déploiement

```bash
# Vérifier logs
tail -50 ~/gestmatv2-vue/storage/logs/laravel.log

# Clear cache
php artisan optimize:clear

# Rebuild cache
php artisan optimize
```

---

## 📦 Package.json Recommandé

```json
{
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "devDependencies": {
    "@headlessui/vue": "^1.7.16",
    "@heroicons/vue": "^2.1.1",
    "@inertiajs/vue3": "^1.0.0",
    "@tailwindcss/forms": "^0.5.7",
    "@tailwindcss/vite": "^4.0.0",
    "@vitejs/plugin-vue": "^5.0.0",
    "autoprefixer": "^10.4.16",
    "axios": "^1.6.2",
    "laravel-vite-plugin": "^1.0.0",
    "pinia": "^2.1.7",
    "tailwindcss": "^4.0.0",
    "vite": "^5.0.0",
    "vue": "^3.4.0"
  },
  "dependencies": {
    "chart.js": "^4.4.1",
    "vue-chartjs": "^5.3.0"
  }
}
```

---

## 💰 Coûts Estimés

| Poste | Montant |
|-------|---------|
| **Développement** (4 semaines) | 15-20k€ |
| **Hébergement O2switch** (an) | 100€ |
| **Maintenance** (an) | 1-2k€ |
| **TOTAL 3 ans** | **18-24k€** |

---

## 🎯 Avantages Laravel + Vue vs Filament

| Aspect | Filament | Laravel + Vue |
|--------|----------|---------------|
| **Rapidité dev** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Personnalisation UI** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Performance** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **UX moderne** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Courbe apprentissage** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Flexibilité** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

**Choisir Filament si:** Admin rapide, peu de custom UI
**Choisir Vue si:** Interface unique, UX premium, évolutivité

---

## 📚 Ressources

- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Vue 3](https://vuejs.org)
- [Documentation Inertia.js](https://inertiajs.com)
- [Headless UI](https://headlessui.com)
- [Tailwind CSS](https://tailwindcss.com)

---

**Document créé:** 2025-11-15
**Auteur:** Claude AI - GestMat v2 Laravel + Vue Edition
