# 🚀 Guide de Déploiement et d'Optimisation - GestMat v2

**Date:** 2025-11-15
**Version du projet:** Laravel 12.x + Filament 4.0
**Statut:** Prêt pour déploiement avec optimisations recommandées

---

## 📋 Table des Matières

1. [Vue d'ensemble du projet](#vue-densemble-du-projet)
2. [Analyse de l'état actuel](#analyse-de-létat-actuel)
3. [Recommandations critiques pour production](#recommandations-critiques-pour-production)
4. [Optimisations de performance](#optimisations-de-performance)
5. [Sécurité](#sécurité)
6. [Infrastructure recommandée](#infrastructure-recommandée)
7. [Plan de déploiement](#plan-de-déploiement)
8. [Monitoring et maintenance](#monitoring-et-maintenance)
9. [Checklist de déploiement](#checklist-de-déploiement)

---

## 🎯 Vue d'ensemble du projet

### Application
**GestMat v2** est une application de gestion de matériel informatique (Asset Management System) développée avec:
- **Backend:** Laravel 12 + PHP 8.4.14
- **Interface Admin:** Filament 4.0
- **Frontend:** Livewire 3 + Tailwind CSS 4
- **Base de données actuelle:** SQLite (développement)
- **Tests:** Pest 4 avec browser testing

### Fonctionnalités principales
- ✅ Gestion des matériels informatiques (ordinateurs, accessoires, périphériques)
- ✅ Gestion des employés et services
- ✅ Système d'attribution avec documents de décharge (PDF)
- ✅ Suivi de l'état et dépréciation des équipements
- ✅ Import/Export en masse
- ✅ Logs d'activité complets (audit trail)
- ✅ Dashboard avec widgets statistiques
- ✅ Génération de QR codes

---

## 📊 Analyse de l'état actuel

### ✅ Points forts
1. **Architecture moderne**
   - Laravel 12 avec structure streamlined
   - PHP 8.4 avec les dernières fonctionnalités
   - UUID pour toutes les clés primaires (sécurité + scalabilité)
   - Tests Pest 4 complets (16 fichiers de tests)

2. **Code quality**
   - Laravel Pint configuré pour le formatage
   - Observers pour la logique métier
   - Form Requests pour validation
   - Activity logging pour audit trail

3. **Performance préliminaire**
   - Index créés sur les tables principales
   - Eager loading dans les Filament resources
   - OPcache activé (PHP 8.4)

### ⚠️ Points d'amélioration pour production

#### 1. **Base de données**
- ❌ SQLite inadapté pour production multi-utilisateurs
- ❌ Pas de réplication/backup automatisé
- ❌ Limites de concurrence

**Recommandation:** PostgreSQL ou MySQL

#### 2. **Caching & Performance**
- ❌ Cache en base de données (lent)
- ❌ Sessions en base de données (overhead)
- ❌ Queue en base de données (pas scalable)

**Recommandation:** Redis pour cache/sessions/queues

#### 3. **Déploiement**
- ❌ Pas de configuration Docker
- ❌ Pas de CI/CD pipeline
- ❌ Pas de gestion d'environnements multiples
- ❌ Pas de monitoring configuré

**Recommandation:** Containerisation + CI/CD

#### 4. **Sécurité**
- ⚠️ Pas de rate limiting configuré
- ⚠️ CORS non configuré
- ⚠️ Logs de production non sécurisés
- ⚠️ Pas de WAF (Web Application Firewall)

**Recommandation:** Configuration sécurité production

#### 5. **Assets & Frontend**
- ⚠️ 4.5MB d'assets publics non optimisés
- ⚠️ Pas de CDN configuré
- ⚠️ Pas de lazy loading

**Recommandation:** Optimisation assets + CDN

---

## 🔥 Recommandations critiques pour production

### Priorité 1 - Critique (À faire AVANT déploiement)

#### 1.1 Migration base de données
```bash
# Passer de SQLite à PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=gestmatv2_prod
DB_USERNAME=gestmat_user
DB_PASSWORD=STRONG_PASSWORD_HERE
```

**Impact:** Concurrence, performance, fiabilité

#### 1.2 Configuration Redis
```bash
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=STRONG_REDIS_PASSWORD
```

**Impact:** Performance x10, scalabilité

#### 1.3 Variables d'environnement production
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
LOG_LEVEL=error
LOG_CHANNEL=daily
```

**Impact:** Sécurité critique

#### 1.4 Configuration mail production
```bash
# Actuel: MAIL_MAILER=log (développement)
# Production: utiliser un service réel
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # ou SendGrid, Mailgun, SES
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
```

**Impact:** Fonctionnalité notifications

### Priorité 2 - Haute (Dans les 48h après déploiement)

#### 2.1 Optimisation cache Laravel
```bash
# Avant chaque déploiement
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components
```

**Impact:** Temps de réponse -50%

#### 2.2 Configuration queue workers
```bash
# Supervisor configuration pour queue workers
[program:gestmat-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/gestmatv2/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/gestmat-queue.log
```

**Impact:** PDF generation, imports/exports asynchrones

#### 2.3 Logs rotation
```bash
# config/logging.php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'error'),
    'days' => 14,
],
```

**Impact:** Gestion espace disque

### Priorité 3 - Moyenne (Première semaine)

#### 3.1 Monitoring & Alerting
- **Application Performance Monitoring (APM):** Laravel Pulse ou New Relic
- **Error Tracking:** Sentry ou Flare
- **Uptime Monitoring:** UptimeRobot ou Pingdom
- **Log aggregation:** Papertrail ou Loggly

#### 3.2 Backups automatisés
```bash
# Package recommandé: spatie/laravel-backup
composer require spatie/laravel-backup

# Configuration backup quotidien
php artisan backup:run --only-db
php artisan backup:clean
```

**Impact:** Disaster recovery

#### 3.3 CDN pour assets statiques
- Cloudflare (gratuit)
- AWS CloudFront
- DigitalOcean Spaces

**Impact:** Chargement pages -40%

---

## ⚡ Optimisations de performance

### Database Query Optimization

#### Index supplémentaires recommandés
```php
// Migration: 2025_11_15_add_production_indexes.php
Schema::table('attributions', function (Blueprint $table) {
    $table->index(['status', 'created_at']);
    $table->index('attribution_number');
    $table->index('restitution_number');
});

Schema::table('materiels', function (Blueprint $table) {
    $table->index(['status', 'physical_condition']);
    $table->index('serial_number');
});

Schema::table('employees', function (Blueprint $table) {
    $table->index('matricule');
    $table->index(['service_id', 'created_at']);
});

Schema::table('activity_log', function (Blueprint $table) {
    $table->index(['subject_type', 'subject_id']);
    $table->index(['causer_type', 'causer_id']);
    $table->index('created_at');
});
```

#### Eager Loading Optimization
```php
// Dans les Filament Resources
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['service', 'materiel.materielType', 'accessories'])
        ->withCount('attributions');
}
```

### Caching Strategy

#### Cache des queries fréquentes
```php
// app/Services/CacheService.php
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function getActiveEmployees()
    {
        return Cache::remember('employees.active', 3600, function () {
            return Employee::with('service')->get();
        });
    }

    public function getDashboardStats()
    {
        return Cache::remember('dashboard.stats', 600, function () {
            return [
                'total_materials' => Materiel::count(),
                'available_materials' => Materiel::where('status', 'disponible')->count(),
                'active_attributions' => Attribution::where('status', 'en_cours')->count(),
            ];
        });
    }
}
```

### Asset Optimization

#### Vite configuration production
```javascript
// vite.config.js
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
                    'filament': ['@filament/forms', '@filament/tables'],
                    'vendor': ['axios', 'alpinejs'],
                },
            },
        },
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
            },
        },
    },
});
```

---

## 🔒 Sécurité

### Configuration sécurité production

#### Rate Limiting
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->throttleApi();

    // Rate limit pour les routes web sensibles
    RateLimiter::for('web', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    // Rate limit strict pour login
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });
})
```

#### CORS Configuration
```php
// config/cors.php
'paths' => ['api/*'],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
'allowed_origins' => [env('FRONTEND_URL', 'https://votre-domaine.com')],
'allowed_headers' => ['Content-Type', 'Authorization'],
'max_age' => 3600,
```

#### Headers de sécurité
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);

    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

    return $response;
}
```

#### Protection fichiers sensibles
```nginx
# nginx configuration
location ~ /\.(?!well-known).* {
    deny all;
}

location ~* ^/(storage|deployer|database|tests|vendor)/ {
    deny all;
}
```

---

## 🏗️ Infrastructure recommandée

### Option 1: Cloud simple (Recommandé pour démarrage)

**Hébergeur:** DigitalOcean App Platform, Heroku, ou Laravel Forge + DigitalOcean

**Configuration minimale:**
- **App Server:** 2 CPU, 4GB RAM (Droplet $24/mois)
- **Database:** PostgreSQL Managed Database 1GB ($15/mois)
- **Cache:** Redis Managed Database 1GB ($15/mois)
- **Storage:** DigitalOcean Spaces 250GB ($5/mois)
- **CDN:** Cloudflare (gratuit)

**Total:** ~$59/mois

### Option 2: Cloud entreprise (Scalabilité)

**Hébergeur:** AWS, Google Cloud, ou Azure

**Configuration:**
- **Compute:** ECS/EKS avec auto-scaling
- **Database:** RDS PostgreSQL Multi-AZ
- **Cache:** ElastiCache Redis
- **Storage:** S3
- **CDN:** CloudFront
- **Load Balancer:** ALB

**Total:** ~$200-500/mois (selon trafic)

### Option 3: Serveur dédié (Contrôle total)

**Configuration serveur:**
- Ubuntu 22.04 LTS ou 24.04 LTS
- Nginx 1.24+
- PHP 8.4 avec OPcache, Redis extension
- PostgreSQL 16
- Redis 7.2
- Supervisor pour queues
- Certbot pour SSL

---

## 📦 Plan de déploiement

### Étape 1: Préparation (1-2 jours)

1. **Setup infrastructure**
   - Provisionner serveurs
   - Configurer base de données PostgreSQL
   - Installer Redis
   - Configurer backup automatique

2. **Configuration environnement**
   - Copier `.env.production.example` → `.env`
   - Générer APP_KEY: `php artisan key:generate`
   - Configurer toutes les variables d'environnement

3. **Tests pré-déploiement**
   - Exécuter la suite de tests: `php artisan test`
   - Vérifier migrations sur base de test
   - Tester génération PDF
   - Vérifier imports/exports

### Étape 2: Déploiement initial (2-4h)

```bash
# Sur le serveur de production
git clone git@github.com:votre-org/gestmatv2.git /var/www/gestmatv2
cd /var/www/gestmatv2

# Checkout production branch
git checkout main

# Installation dépendances
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Configuration
cp .env.production .env
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder  # si nécessaire

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components

# Permissions
chown -R www-data:www-data /var/www/gestmatv2
chmod -R 755 /var/www/gestmatv2
chmod -R 775 /var/www/gestmatv2/storage
chmod -R 775 /var/www/gestmatv2/bootstrap/cache

# Queue workers
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start gestmat-queue:*

# Redémarrage services
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

### Étape 3: Vérification post-déploiement (30min)

```bash
# Health checks
php artisan about
php artisan optimize:clear
php artisan queue:restart

# Tests fonctionnels
curl https://votre-domaine.com/health
php artisan tinker  # Vérifier connexion DB
```

### Étape 4: Migration données (si migration depuis ancien système)

```bash
# Backup avant migration
php artisan backup:run

# Import données
php artisan migrate:fresh --seed  # ATTENTION: efface tout
# OU
php artisan db:seed --class=MigrationSeeder
```

---

## 📈 Monitoring et maintenance

### Métriques à surveiller

1. **Performance application**
   - Temps de réponse moyen (< 200ms)
   - Taux d'erreur (< 0.1%)
   - Nombre de requêtes/minute
   - Utilisation mémoire PHP

2. **Base de données**
   - Temps de requête moyen
   - Connexions actives
   - Taille de la base
   - Slow queries (> 1s)

3. **Infrastructure**
   - CPU utilisation (< 70%)
   - RAM utilisation (< 80%)
   - Espace disque (> 20% libre)
   - Bande passante réseau

### Maintenance régulière

#### Quotidienne
```bash
# Backup automatique (cron)
0 2 * * * cd /var/www/gestmatv2 && php artisan backup:run --only-db
0 3 * * * cd /var/www/gestmatv2 && php artisan backup:clean
```

#### Hebdomadaire
```bash
# Nettoyage logs activity
0 1 * * 0 cd /var/www/gestmatv2 && php artisan activitylog:clean --days=90

# Optimisation base de données
0 2 * * 0 cd /var/www/gestmatv2 && php artisan db:optimize
```

#### Mensuelle
- Review des logs d'erreur
- Audit de sécurité
- Mise à jour dépendances (patch)
- Vérification backups

---

## ✅ Checklist de déploiement

Voir fichier: `deployer/DEPLOYMENT_CHECKLIST.md`

---

## 📚 Ressources

### Documentation
- [Documentation Laravel 12](https://laravel.com/docs/12.x)
- [Documentation Filament 4](https://filamentphp.com/docs)
- [Documentation Livewire 3](https://livewire.laravel.com/docs)

### Fichiers de configuration
- `deployer/configs/.env.production` - Variables d'environnement production
- `deployer/configs/.env.staging` - Variables d'environnement staging
- `deployer/configs/nginx.conf` - Configuration Nginx
- `deployer/configs/supervisor.conf` - Configuration queue workers

### Scripts
- `deployer/scripts/deploy.sh` - Script de déploiement automatisé
- `deployer/scripts/rollback.sh` - Script de rollback
- `deployer/scripts/backup.sh` - Script de backup manuel

### Docker
- `deployer/docker/docker-compose.yml` - Configuration Docker
- `deployer/docker/Dockerfile` - Image Docker personnalisée
- `deployer/docker/nginx.conf` - Nginx pour Docker

---

**Créé par:** Claude AI
**Dernière mise à jour:** 2025-11-15
