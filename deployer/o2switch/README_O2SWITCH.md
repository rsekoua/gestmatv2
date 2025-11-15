# 🌐 Guide de Déploiement GestMat v2 sur O2switch (Hébergement Mutualisé)

**Hébergeur:** O2switch
**Type:** Hébergement mutualisé
**Panel:** cPanel
**Date:** 2025-11-15

---

## ⚠️ Limitations de l'Hébergement Mutualisé

### Ce qui N'EST PAS disponible sur O2switch mutualisé:

❌ **Accès root/sudo** - Pas de contrôle complet du serveur
❌ **PostgreSQL** - Uniquement MySQL/MariaDB
❌ **Redis** - Pas de serveur Redis disponible
❌ **Nginx** - Apache uniquement (géré par O2switch)
❌ **Supervisor** - Pas de queue workers persistants
❌ **Docker** - Pas de containerisation
❌ **SSH complet** - Accès SSH limité (selon formule)
❌ **Cron jobs illimités** - Limitation à quelques cron jobs
❌ **Node.js server** - Pas de serveur Node persistant

### Ce qui EST disponible sur O2switch:

✅ **cPanel** - Interface de gestion complète
✅ **PHP 8.x** - Versions PHP récentes (8.1, 8.2, 8.3+)
✅ **MySQL/MariaDB** - Base de données relationnelle
✅ **SSL Let's Encrypt** - Certificats SSL gratuits
✅ **Git** - Déploiement via Git possible
✅ **Composer** - Installation de dépendances PHP
✅ **Cron jobs** - Planification de tâches (limité)
✅ **.htaccess** - Configuration Apache
✅ **PHP-FPM** - Performance PHP optimisée
✅ **Stockage illimité** - Espace disque illimité
✅ **Bande passante illimitée** - Trafic illimité

---

## 🎯 Architecture Adaptée pour O2switch

### Stack Technique Ajusté

| Composant | Recommandation Initiale | **Adaptation O2switch** |
|-----------|------------------------|-------------------------|
| Base de données | PostgreSQL | **MySQL 8.0** |
| Cache | Redis | **Cache fichier** ou **Database** |
| Queue | Redis Queue | **Database Queue** + Cron |
| Sessions | Redis | **Database** ou **Fichier** |
| Web Server | Nginx | **Apache** (géré par O2switch) |
| PHP Version | 8.4 | **8.3** ou **8.2** (vérifier disponibilité) |

---

## 📋 Pré-requis O2switch

### 1. Configuration cPanel requise

- [ ] Compte O2switch actif
- [ ] Accès cPanel
- [ ] Domaine configuré
- [ ] SSL activé (Let's Encrypt)
- [ ] PHP 8.2+ sélectionné
- [ ] Base de données MySQL créée
- [ ] Utilisateur MySQL créé

### 2. Extensions PHP requises

Vérifier dans cPanel > "Sélectionner une version de PHP" que ces extensions sont activées:

- [x] `mbstring`
- [x] `pdo`
- [x] `pdo_mysql`
- [x] `zip`
- [x] `gd`
- [x] `curl`
- [x] `xml`
- [x] `bcmath`
- [x] `fileinfo`
- [x] `tokenizer`
- [x] `json`
- [x] `openssl`

---

## 🚀 Déploiement Étape par Étape

### Étape 1: Préparation cPanel (15 min)

#### 1.1 Créer la base de données

1. **Connexion cPanel** : `https://cpanel.o2switch.fr`
2. **Bases de données MySQL** > **Assistant bases de données MySQL**
3. Créer:
   - **Nom de la base:** `gestmatv2` (préfixe automatique ajouté)
   - **Utilisateur:** `gestmat_user`
   - **Mot de passe:** Générer un mot de passe fort (min. 16 caractères)
4. **Privilèges:** Cocher "TOUS LES PRIVILÈGES"
5. **Noter les informations:**
   ```
   DB_DATABASE=cpaneluser_gestmatv2
   DB_USERNAME=cpaneluser_gestmat_user
   DB_PASSWORD=le_mot_de_passe_généré
   DB_HOST=localhost
   ```

#### 1.2 Configurer PHP

1. **cPanel** > **Sélectionner une version de PHP**
2. Sélectionner: **PHP 8.3** (ou la version la plus récente disponible)
3. Activer les extensions listées ci-dessus
4. **Options PHP** (php.ini):
   ```ini
   max_execution_time = 300
   max_input_time = 300
   memory_limit = 512M
   post_max_size = 50M
   upload_max_filesize = 50M
   ```

#### 1.3 Configurer le domaine

1. **cPanel** > **Domaines**
2. **Ajouter un domaine** (si pas déjà fait)
3. **Racine du document:** `/home/cpaneluser/public_html` (ou sous-dossier)
4. **SSL/TLS** > Activer **AutoSSL** (Let's Encrypt gratuit)

---

### Étape 2: Téléchargement et Installation (30 min)

#### 2.1 Connexion SSH (si disponible)

O2switch offre SSH sur tous les plans. Connexion:

```bash
ssh cpaneluser@votredomaine.com
# Ou
ssh cpaneluser@serveur.o2switch.net
```

**Note:** Si SSH non disponible, utilisez **Gestionnaire de fichiers cPanel** pour tout.

#### 2.2 Clone du projet

**Option A: Via SSH (recommandé)**

```bash
# Se placer dans le home
cd ~

# Cloner le repository
git clone https://github.com/votre-org/gestmatv2.git gestmatv2

# Accéder au dossier
cd gestmatv2
```

**Option B: Via cPanel Gestionnaire de fichiers**

1. Télécharger le ZIP du projet depuis GitHub
2. Upload via **Gestionnaire de fichiers** > **Télécharger**
3. Extraire l'archive

#### 2.3 Installation des dépendances Composer

```bash
cd ~/gestmatv2

# Installer Composer (si pas installé)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Installer les dépendances
php composer.phar install --optimize-autoloader --no-dev

# Ou si composer global disponible:
composer install --optimize-autoloader --no-dev
```

#### 2.4 Configuration .env

```bash
# Copier le fichier d'exemple O2switch
cp deployer/o2switch/.env.o2switch .env

# Éditer avec nano ou via cPanel
nano .env
```

Remplir avec vos vraies valeurs (voir section Configuration ci-dessous).

#### 2.5 Générer la clé d'application

```bash
php artisan key:generate
```

#### 2.6 Migrations de base de données

```bash
# Exécuter les migrations
php artisan migrate --force

# Si vous avez des seeders de production
php artisan db:seed --class=ProductionSeeder
```

---

### Étape 3: Configuration Apache (.htaccess) (10 min)

#### 3.1 Lien symbolique vers public

O2switch attend les fichiers dans `public_html`. Deux options:

**Option A: Lien symbolique (recommandé si SSH disponible)**

```bash
# Supprimer le public_html existant (backup d'abord!)
mv ~/public_html ~/public_html.backup

# Créer le lien symbolique vers le dossier public de Laravel
ln -s ~/gestmatv2/public ~/public_html
```

**Option B: Déplacer les fichiers (via cPanel)**

1. Déplacer tout le contenu de `gestmatv2/public/` vers `public_html/`
2. Éditer `public_html/index.php`:
   ```php
   // Changer les chemins
   require __DIR__.'/../gestmatv2/vendor/autoload.php';
   $app = require_once __DIR__.'/../gestmatv2/bootstrap/app.php';
   ```

#### 3.2 Fichier .htaccess dans public_html

Créer/vérifier `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Disable directory browsing
Options -Indexes

# Prevent access to .env
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# PHP Configuration (si non défini globalement)
<IfModule mod_php8.c>
    php_value upload_max_filesize 50M
    php_value post_max_size 50M
    php_value max_execution_time 300
    php_value memory_limit 512M
</IfModule>
```

#### 3.3 Protéger les dossiers sensibles

Créer `.htaccess` dans la racine du projet (`~/gestmatv2/.htaccess`):

```apache
# Deny access to root folder
Order deny,allow
Deny from all
```

---

### Étape 4: Optimisation Laravel pour Mutualisé (15 min)

#### 4.1 Cache de configuration

```bash
cd ~/gestmatv2

# Cacher les configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Pour Filament
php artisan filament:cache-components
```

#### 4.2 Optimisation Composer

```bash
composer install --optimize-autoloader --classmap-authoritative --no-dev
php artisan optimize
```

#### 4.3 Permissions

```bash
# Donner les bonnes permissions
chmod -R 755 ~/gestmatv2/storage
chmod -R 755 ~/gestmatv2/bootstrap/cache

# Créer le lien symbolique pour storage
php artisan storage:link
```

---

### Étape 5: Configuration Cron pour Queue/Scheduler (10 min)

O2switch limite les cron jobs, mais on peut configurer le scheduler Laravel.

#### 5.1 Configurer Cron dans cPanel

1. **cPanel** > **Tâches Cron**
2. Ajouter une nouvelle tâche cron:
   - **Minute:** `*/5` (toutes les 5 minutes)
   - **Commande:**
     ```bash
     cd /home/cpaneluser/gestmatv2 && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
     ```

**Note:** Vérifier le chemin PHP avec `which php` via SSH.

#### 5.2 Configurer le Scheduler Laravel

Le scheduler va gérer les queues périodiquement. Créer/modifier `app/Console/Kernel.php` ou `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

// Traiter les queues toutes les 5 minutes
Schedule::command('queue:work --stop-when-empty --tries=3')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Nettoyer les anciens logs d'activité
Schedule::command('activitylog:clean --days=90')
    ->weekly();
```

**Limitation:** Les jobs ne seront pas traités en temps réel, mais toutes les 5 minutes maximum.

---

## ⚙️ Configuration .env pour O2switch

Voici le fichier `.env` adapté pour O2switch:

```env
# ==================================================
# CONFIGURATION O2SWITCH - HÉBERGEMENT MUTUALISÉ
# ==================================================

APP_NAME="GestMat v2"
APP_ENV=production
APP_KEY=  # Générer avec: php artisan key:generate
APP_DEBUG=false
APP_URL=https://votre-domaine.com
APP_TIMEZONE=UTC

APP_LOCALE=fr
APP_FALLBACK_LOCALE=en

# ==================================================
# BASE DE DONNÉES - MySQL (O2switch)
# ==================================================
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_gestmatv2  # Avec préfixe cPanel
DB_USERNAME=cpaneluser_gestmat_user
DB_PASSWORD=  # Mot de passe MySQL généré

# ==================================================
# CACHE - FICHIER (Pas de Redis sur mutualisé)
# ==================================================
CACHE_STORE=file
CACHE_PREFIX=gestmat_

# ==================================================
# SESSIONS - DATABASE (Recommandé pour mutualisé)
# ==================================================
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# ==================================================
# QUEUE - DATABASE (Traité par cron toutes les 5min)
# ==================================================
QUEUE_CONNECTION=database

# ==================================================
# FILESYSTEM - LOCAL
# ==================================================
FILESYSTEM_DISK=local

# ==================================================
# MAIL - SMTP O2switch
# ==================================================
MAIL_MAILER=smtp
MAIL_HOST=mail.votre-domaine.com  # Serveur SMTP O2switch
MAIL_PORT=587
MAIL_USERNAME=noreply@votre-domaine.com  # Email créé dans cPanel
MAIL_PASSWORD=  # Mot de passe email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"

# ==================================================
# LOGGING
# ==================================================
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# ==================================================
# BROADCAST
# ==================================================
BROADCAST_CONNECTION=log

# ==================================================
# FILAMENT
# ==================================================
FILAMENT_FILESYSTEM_DISK=public

# ==================================================
# OPTIMISATION
# ==================================================
# Cache routes et config pour performance
VIEW_COMPILED_PATH=/home/cpaneluser/gestmatv2/storage/framework/views
```

---

## 🔧 Optimisations pour Hébergement Mutualisé

### 1. Cache Fichier au lieu de Redis

**Créer:** `config/cache.php` (déjà existant, vérifier):

```php
'default' => env('CACHE_STORE', 'file'),

'stores' => [
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
],
```

### 2. Utiliser OPcache (déjà activé par O2switch)

O2switch active OPcache par défaut. Vérifier:

```bash
php -i | grep opcache
```

### 3. Optimiser les Assets

Avant déploiement, compiler les assets en local:

```bash
# Sur votre machine locale
npm run build

# Uploader le dossier public/build/ vers O2switch
```

### 4. Limiter la taille des logs

```php
// config/logging.php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'error',  // Seulement les erreurs
    'days' => 7,         // Garder 7 jours max
],
```

---

## 📊 Limitations et Solutions

| Limitation | Impact | Solution O2switch |
|------------|--------|------------------|
| Pas de Redis | Pas de cache rapide | Cache fichier + OPcache PHP |
| Pas de queue workers temps réel | Jobs traités toutes les 5min | Cron + database queue |
| Pas de PostgreSQL | Utiliser MySQL | MySQL 8.0 très performant |
| Limites ressources CPU/RAM | Performance variable | Cache agressif, optimisations |
| Pas de Supervisor | Queue workers non persistants | Scheduler Laravel via cron |
| Pas de CLI temps réel | Maintenance manuelle | Scripts cron réguliers |

---

## ✅ Checklist Post-Déploiement O2switch

### Tests Fonctionnels

- [ ] Site accessible via HTTPS
- [ ] Certificat SSL valide (AutoSSL)
- [ ] Dashboard Filament charge correctement
- [ ] Login administrateur fonctionne
- [ ] Création matériel/employé fonctionne
- [ ] Upload fichiers fonctionne
- [ ] Génération PDF fonctionne
- [ ] Import/Export fonctionnent (traités via queue)
- [ ] Emails envoyés correctement

### Performance

- [ ] Temps de chargement < 3s (acceptable sur mutualisé)
- [ ] Cache fichier fonctionne (`storage/framework/cache`)
- [ ] OPcache actif (vérifier avec `php -i`)
- [ ] Logs rotation active (7 jours)

### Sécurité

- [ ] `.env` non accessible (tester: `https://domain.com/.env`)
- [ ] Dossiers sensibles protégés
- [ ] HTTPS forcé (pas de HTTP)
- [ ] Backups cPanel configurés

---

## 🔄 Déploiement des Mises à Jour

### Via SSH (recommandé)

```bash
# Connexion
ssh cpaneluser@votredomaine.com
cd ~/gestmatv2

# Mettre en maintenance
php artisan down

# Pull dernières modifications
git pull origin main

# Mettre à jour dépendances
composer install --optimize-autoloader --no-dev

# Migrations
php artisan migrate --force

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

# Sortir de maintenance
php artisan up
```

### Via cPanel (sans SSH)

1. **Backup** de la base de données (cPanel > phpMyAdmin > Exporter)
2. **Upload** nouveaux fichiers via Gestionnaire de fichiers
3. **Remplacer** les fichiers existants
4. **Exécuter** via **Terminal cPanel** ou créer un script `update.php`:
   ```php
   <?php
   require __DIR__.'/vendor/autoload.php';
   $app = require __DIR__.'/bootstrap/app.php';

   // Migrations
   Artisan::call('migrate', ['--force' => true]);

   // Cache
   Artisan::call('config:cache');
   Artisan::call('route:cache');
   Artisan::call('view:cache');

   echo "Mise à jour terminée!";
   ```

---

## 🎯 Performance Attendue sur O2switch

### Métriques Réalistes

| Métrique | Valeur Attendue |
|----------|----------------|
| Temps de chargement initial | 1-3s |
| Temps de chargement pages suivantes | 0.5-1.5s |
| Génération PDF | 2-5s |
| Import 100 lignes | 10-30s (via queue) |
| Concurrent users supportés | 10-50 (selon trafic) |

**Note:** Performance peut varier selon charge serveur mutualisé.

---

## 📞 Support O2switch

- **Support technique:** support@o2switch.fr
- **Documentation:** https://faq.o2switch.fr
- **Chat live:** Disponible sur le site O2switch
- **Téléphone:** 04 44 44 60 40

---

## 🔥 Troubleshooting

### Erreur 500 après déploiement

```bash
# Vérifier les logs
tail -f ~/gestmatv2/storage/logs/laravel.log

# Vérifier permissions
chmod -R 755 ~/gestmatv2/storage
chmod -R 755 ~/gestmatv2/bootstrap/cache

# Re-générer cache
php artisan config:cache
```

### Queue jobs ne s'exécutent pas

```bash
# Vérifier le cron
crontab -l

# Exécuter manuellement
php artisan queue:work --stop-when-empty

# Vérifier la table jobs
mysql -u user -p database -e "SELECT * FROM jobs;"
```

### Uploads ne fonctionnent pas

```bash
# Vérifier storage link
php artisan storage:link

# Vérifier permissions
chmod -R 755 ~/gestmatv2/storage/app/public
```

---

**Document créé:** 2025-11-15
**Hébergeur:** O2switch (Hébergement Mutualisé)
**Stack:** Apache + PHP 8.3 + MySQL 8.0
**Auteur:** Claude AI - Configuration GestMat v2
