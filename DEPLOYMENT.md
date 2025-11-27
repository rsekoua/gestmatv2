# 🚀 Guide de Déploiement - GestMat V2 sur O2Switch via GitHub

## 📋 Table des Matières

1. [Prérequis](#prérequis)
2. [Préparation du Dépôt GitHub](#préparation-du-dépôt-github)
3. [Configuration O2Switch](#configuration-o2switch)
4. [Cloner le Projet depuis GitHub](#cloner-le-projet-depuis-github)
5. [Configuration de l'Application](#configuration-de-lapplication)
6. [Configuration de la Base de Données](#configuration-de-la-base-de-données)
7. [Installation des Dépendances](#installation-des-dépendances)
8. [Compilation des Assets](#compilation-des-assets)
9. [Exécution des Seeders](#exécution-des-seeders)
10. [Configuration du Domaine](#configuration-du-domaine)
11. [Optimisations Production](#optimisations-production)
12. [Vérifications Post-Déploiement](#vérifications-post-déploiement)
13. [Mises à Jour via GitHub](#mises-à-jour-via-github)
14. [Dépannage](#dépannage)

---

## 🎯 Prérequis

### Hébergement O2Switch
- ✅ Accès cPanel
- ✅ Accès SSH (obligatoire pour GitHub)
- ✅ PHP 8.2+ (minimum)
- ✅ MySQL/MariaDB
- ✅ Composer disponible via SSH
- ✅ Git disponible via SSH
- ✅ Node.js/NPM disponible (pour compilation des assets)

### Dépôt GitHub
- ✅ Repository GitHub créé
- ✅ Code source poussé sur GitHub
- ✅ Accès au repository (public ou privé avec clé SSH)

---

## 📦 Préparation du Dépôt GitHub

### Étape 1 : Vérifier le .gitignore

Assurez-vous que votre `.gitignore` contient :

```gitignore
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.fleet
/.idea
/.vscode
database/database.sqlite
```

⚠️ **Important :** Ne JAMAIS commiter :
- `.env` (informations sensibles)
- `/vendor` (sera installé via Composer)
- `/node_modules` (sera installé via NPM)
- `/public/build` (sera compilé sur le serveur)

### Étape 2 : Pousser le Code sur GitHub

```bash
# Si pas encore initialisé
git init
git add .
git commit -m "Initial commit - GestMat V2"

# Ajouter le repository distant
git remote add origin https://github.com/votre-username/gestmatv2.git

# Pousser le code
git branch -M main
git push -u origin main
```

### Étape 3 : Vérifier le Repository

Vérifiez sur GitHub que :
- ✅ Tous les fichiers sont présents
- ✅ Le fichier `.env` n'est PAS présent
- ✅ Le dossier `vendor/` n'est PAS présent
- ✅ Le dossier `node_modules/` n'est PAS présent
- ✅ Le fichier `.env.example` EST présent

---

## 🌐 Configuration O2Switch

### Étape 1 : Connexion cPanel

1. Connectez-vous à votre cPanel O2Switch
2. URL : `https://www.votre-domaine.com:2083`

### Étape 2 : Créer la Base de Données MySQL

**Via cPanel → MySQL Database Wizard :**

1. **Nom de la base** : `o2switch_gestmat`
2. **Créer un utilisateur** :
    - Nom : `o2switch_gestmat`
    - Mot de passe : `[GÉNÉRER UN MOT DE PASSE FORT]`
    - ⚠️ **Copiez et sauvegardez le mot de passe**
3. **Privilèges** : Sélectionner TOUS les privilèges
4. Notez les informations :
   ```
   DB_HOST: localhost
   DB_DATABASE: o2switch_gestmat
   DB_USERNAME: o2switch_gestmat
   DB_PASSWORD: [votre_mot_de_passe]
   ```

### Étape 3 : Configurer PHP (Minimum 8.2)

**Via cPanel → Select PHP Version :**

1. Sélectionner **PHP 8.2 ou supérieur**
2. Activer les extensions :
    - ✅ `mbstring`
    - ✅ `openssl`
    - ✅ `pdo`
    - ✅ `pdo_mysql`
    - ✅ `tokenizer`
    - ✅ `xml`
    - ✅ `ctype`
    - ✅ `json`
    - ✅ `bcmath`
    - ✅ `fileinfo`
    - ✅ `gd` (pour génération QR codes)
    - ✅ `zip`

### Étape 4 : Vérifier les Outils Disponibles

**Via SSH :**

```bash
# Se connecter
ssh votreuser@votre-domaine.com

# Vérifier Git
git --version
# Attendu : git version 2.x.x

# Vérifier Composer
composer --version
# Attendu : Composer version 2.x.x

# Vérifier PHP
php -v
# Attendu : PHP 8.2+ ou supérieur

# Vérifier Node.js
node --version
# Attendu : v18.x ou supérieur

# Vérifier NPM
npm --version
# Attendu : 9.x ou supérieur
```

---

## 📥 Cloner le Projet depuis GitHub

### Méthode A : Repository Public (Recommandé pour débutants)

**Via SSH O2Switch :**

```bash
# Se connecter
ssh votreuser@votre-domaine.com

# Naviguer vers le répertoire home
cd ~

# Cloner le repository
git clone https://github.com/votre-username/gestmatv2.git

# Entrer dans le dossier
cd gestmatv2
```

### Méthode B : Repository Privé (avec Token GitHub)

**1. Créer un Personal Access Token sur GitHub :**

- GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
- Generate new token (classic)
- Cocher : `repo` (accès complet aux repositories privés)
- Générer et copier le token

**2. Cloner avec le token :**

```bash
# Se connecter à O2Switch
ssh votreuser@votre-domaine.com

# Naviguer vers le répertoire home
cd ~

# Cloner avec token
git clone https://[VOTRE_TOKEN]@github.com/votre-username/gestmatv2.git

# Entrer dans le dossier
cd gestmatv2
```

### Méthode C : Repository Privé (avec Clé SSH)

**1. Générer une clé SSH sur O2Switch :**

```bash
# Se connecter
ssh votreuser@votre-domaine.com

# Générer la clé SSH
ssh-keygen -t ed25519 -C "votre-email@example.com"

# Afficher la clé publique
cat ~/.ssh/id_ed25519.pub
```

**2. Ajouter la clé à GitHub :**

- GitHub → Settings → SSH and GPG keys → New SSH key
- Coller la clé publique
- Sauvegarder

**3. Cloner avec SSH :**

```bash
cd ~
git clone git@github.com:votre-username/gestmatv2.git
cd gestmatv2
```

### Vérification du Clonage

```bash
# Vérifier la structure
ls -la

# Doit afficher :
# app/
# bootstrap/
# config/
# database/
# public/
# resources/
# routes/
# storage/
# vendor/ (pas encore présent)
# .env.example
# composer.json
# package.json
# artisan
```

---

## ⚙️ Configuration de l'Application

### Étape 1 : Créer le fichier .env

```bash
cd ~/gestmatv2

# Copier .env.example vers .env
cp .env.example .env

# Éditer le fichier
nano .env
```

### Étape 2 : Configurer les Variables d'Environnement

**Appuyez sur les touches pour éditer, puis `Ctrl+O` pour sauvegarder, `Enter`, `Ctrl+X` pour quitter.**

```env
# ========================================
# CONFIGURATION PRODUCTION
# ========================================

APP_NAME="GestMat V2"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://votre-domaine.com

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

# ========================================
# SÉCURITÉ
# ========================================

BCRYPT_ROUNDS=12

# ========================================
# LOGS
# ========================================

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

# ========================================
# BASE DE DONNÉES
# ========================================

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=o2switch_gestmat
DB_USERNAME=o2switch_gestmat
DB_PASSWORD=[VOTRE_MOT_DE_PASSE]

# ========================================
# SESSION & CACHE
# ========================================

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

# ========================================
# MAIL (À CONFIGURER)
# ========================================

MAIL_MAILER=smtp
MAIL_HOST=mail.votre-domaine.com
MAIL_PORT=587
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=[MOT_DE_PASSE_EMAIL]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@votre-domaine.com"
MAIL_FROM_NAME="${APP_NAME}"

# ========================================
# VITE
# ========================================

VITE_APP_NAME="${APP_NAME}"
```

⚠️ **Important :**
- `APP_ENV=production` (pas `local`)
- `APP_DEBUG=false` (jamais `true` en production)
- `DB_*` avec vos vraies informations de base de données
- `APP_URL` avec votre vrai domaine

### Étape 3 : Générer la Clé d'Application

```bash
php artisan key:generate
```

Cette commande va automatiquement remplir `APP_KEY` dans le `.env`.

### Étape 4 : Créer le Lien Symbolique pour Storage

```bash
php artisan storage:link
```

Cette commande crée un lien : `public/storage` → `storage/app/public`

---

## 🗄️ Configuration de la Base de Données

### Étape 1 : Exécuter les Migrations

```bash
cd ~/gestmatv2
php artisan migrate --force
```

⚠️ Le flag `--force` est nécessaire en environnement production.

**Output attendu :**
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table
Migrating: 2025_01_01_000001_create_services_table
Migrated:  2025_01_01_000001_create_services_table
...
[20 migrations completed]
```

---

## 📦 Installation des Dépendances

### Étape 1 : Installer les Dépendances Composer

```bash
cd ~/gestmatv2

# Installation des dépendances PHP (sans dev)
composer install --optimize-autoloader --no-dev
```

**Durée estimée :** 2-5 minutes

**Output attendu :**
```
Loading composer repositories with package information
Installing dependencies from lock file
...
Generating optimized autoload files
```

### Étape 2 : Configurer les Permissions

```bash
# Permissions storage et bootstrap/cache
chmod -R 775 storage bootstrap/cache

# S'assurer que l'utilisateur est propriétaire
chown -R $USER:$USER storage bootstrap/cache
```

---

## 🎨 Compilation des Assets

### Étape 1 : Installer les Dépendances Node

```bash
cd ~/gestmatv2

# Installer les dépendances NPM
npm install
```

**Durée estimée :** 3-10 minutes (selon la connexion)

### Étape 2 : Compiler les Assets pour Production

```bash
# Compiler pour la production
npm run build
```

**Output attendu :**
```
vite v5.x.x building for production...
✓ built in Xs
```

✅ Cette commande génère les fichiers optimisés dans `public/build/`

**Vérification :**

```bash
ls -la public/build/
# Doit afficher : manifest.json et fichiers CSS/JS compilés
```

---

## 🌱 Exécution des Seeders

### Comprendre les Seeders

Votre projet a **6 seeders** :

1. **DatabaseSeeder** (principal)
    - Crée 1 utilisateur admin
    - Appelle `MaterielTypeSeeder`
    - Appelle `AccessorySeeder`

2. **MaterielTypeSeeder** ⭐ OBLIGATOIRE
    - Crée 5 types de matériel de base

3. **AccessorySeeder** ⭐ OBLIGATOIRE
    - Crée 5 accessoires standards

4. **ServiceSeeder** (optionnel)
5. **EmployeeSeeder** (optionnel)
6. **MaterielSeeder** (optionnel)

### Option A : Déploiement Initial avec Données de Base (RECOMMANDÉ)

```bash
cd ~/gestmatv2
php artisan db:seed --force
```

✅ **Crée :**
- 1 utilisateur admin (`admin@local.host` / `password`)
- 5 types de matériel
- 5 accessoires

⚠️ **IMPORTANT :** Après le premier déploiement :
1. Connectez-vous avec `admin@local.host` / `password`
2. **CHANGEZ IMMÉDIATEMENT LE MOT DE PASSE** via l'interface Filament

### Option B : Seeders Sélectifs

```bash
# Types de matériel uniquement
php artisan db:seed --class=MaterielTypeSeeder --force

# Accessoires uniquement
php artisan db:seed --class=AccessorySeeder --force
```

### Option C : Base de Données Vierge

Si vous voulez une base vierge sans données de test :

```bash
# Créer l'admin manuellement
php artisan tinker
```

Puis dans Tinker :

```php
User::create([
    'name' => 'Administrateur',
    'email' => 'admin@votre-domaine.com',
    'password' => bcrypt('VotreMotDePasseSecurise123!')
]);
exit
```

---

## 🌍 Configuration du Domaine

### Option A : Domaine Principal

**Si vous utilisez le domaine principal (`votre-domaine.com`) :**

1. **cPanel → Domains → Primary Domain**
2. **Document Root** : `/home/votreuser/gestmatv2/public`
3. Sauvegarder

### Option B : Sous-domaine

**Créer un sous-domaine (`gestmat.votre-domaine.com`) :**

1. **cPanel → Subdomains**
2. **Subdomain** : `gestmat`
3. **Document Root** : `/home/votreuser/gestmatv2/public`
4. Créer

### Option C : Addon Domain

**Pour un autre domaine :**

1. **cPanel → Addon Domains**
2. **New Domain Name** : `gestmat-exemple.com`
3. **Document Root** : `/home/votreuser/gestmatv2/public`
4. Créer

⚠️ **CRITIQUE :** Le Document Root doit pointer vers `/public`, pas vers la racine du projet !

### Configuration .htaccess

Vérifiez que `public/.htaccess` existe et contient :

```apache
<IfModule mod_rewrite.c>
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
</IfModule>
```

### Forcer HTTPS (Recommandé)

**Ajouter au début de `public/.htaccess` :**

```bash
cd ~/gestmatv2/public
nano .htaccess
```

Ajouter en haut :

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## ⚡ Optimisations Production

### Étape 1 : Cacher la Configuration

```bash
cd ~/gestmatv2

# Optimiser configuration
php artisan config:cache

# Optimiser routes
php artisan route:cache

# Optimiser vues
php artisan view:cache

# Optimiser événements
php artisan event:cache
```

### Étape 2 : Optimiser l'Autoloader

```bash
composer dump-autoload --optimize
```

### Étape 3 : Vérifier les Permissions Finales

```bash
# Permissions finales
chmod -R 755 storage bootstrap/cache
chmod -R 755 public

# S'assurer que l'utilisateur web peut écrire
chown -R $USER:$USER storage bootstrap/cache
```

---

## ✅ Vérifications Post-Déploiement

### Checklist Complète

#### 1. Accès à l'Application

```
✅ https://votre-domaine.com → Page visible
✅ https://votre-domaine.com/admin → Page de connexion Filament
✅ Pas d'erreur 500
✅ HTTPS actif (cadenas vert)
```

#### 2. Connexion Admin

```
URL : https://votre-domaine.com/admin
Email : admin@local.host
Mot de passe : password
```

✅ Connexion réussie → Dashboard Filament visible

#### 3. Navigation

```
✅ Menu Matériels → Liste visible
✅ Menu Employés → Liste visible
✅ Menu Services → Liste visible
✅ Menu Attributions → Liste visible
✅ Menu Types de Matériel → 5 types présents
✅ Menu Accessoires → 5 accessoires présents
✅ Dashboard → Widgets affichés
```

#### 4. Fonctionnalités

```
✅ Créer un service
✅ Créer un employé
✅ Créer un matériel
✅ Créer une attribution
```

#### 5. Assets

```
✅ CSS chargés (interface stylisée)
✅ JavaScript fonctionnel
✅ Images/logos visibles
```

#### 6. Base de Données

```bash
# Vérifier via Tinker
php artisan tinker
```

```php
// Compter les utilisateurs
User::count(); // 1

// Compter les types de matériel
\App\Models\MaterielType::count(); // 5

// Compter les accessoires
\App\Models\Accessory::count(); // 5

exit
```

#### 7. Logs

```bash
# Vérifier qu'il n'y a pas d'erreurs
tail -50 storage/logs/laravel.log
```

---

## 🔄 Mises à Jour via GitHub

### Workflow de Mise à Jour

**Quand vous faites des modifications dans votre projet local :**

#### 1. Local : Pousser les Modifications

```bash
# Local - Sur votre machine de développement
git add .
git commit -m "Description des modifications"
git push origin main
```

#### 2. Serveur : Récupérer les Modifications

```bash
# SSH O2Switch
ssh votreuser@votre-domaine.com
cd ~/gestmatv2

# Récupérer les dernières modifications
git pull origin main

# Si des dépendances Composer ont changé
composer install --optimize-autoloader --no-dev

# Si des fichiers NPM ont changé
npm install
npm run build

# Si des migrations ont été ajoutées
php artisan migrate --force

# Vider les caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recréer les caches optimisés
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Script de Déploiement Automatisé

**Créer un script de déploiement :**

```bash
cd ~/gestmatv2
nano deploy.sh
```

**Contenu de `deploy.sh` :**

```bash
#!/bin/bash

echo "🚀 Déploiement de GestMat V2..."

# Récupérer les dernières modifications
echo "📥 Récupération du code depuis GitHub..."
git pull origin main

# Installer les dépendances
echo "📦 Installation des dépendances Composer..."
composer install --optimize-autoloader --no-dev

echo "📦 Installation des dépendances NPM..."
npm install

# Compiler les assets
echo "🎨 Compilation des assets..."
npm run build

# Exécuter les migrations
echo "🗄️ Exécution des migrations..."
php artisan migrate --force

# Vider et recréer les caches
echo "🧹 Nettoyage des caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "⚡ Optimisation pour production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
echo "🔐 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache

echo "✅ Déploiement terminé !"
```

**Rendre le script exécutable :**

```bash
chmod +x deploy.sh
```

**Utilisation :**

```bash
# Pour déployer les mises à jour
cd ~/gestmatv2
./deploy.sh
```

---

## 🚨 Dépannage

### Problème 1 : Erreur lors du git clone

**Erreur :** `Permission denied (publickey)`

**Solution pour repository privé :**
- Vérifier que la clé SSH est ajoutée sur GitHub
- Ou utiliser un Personal Access Token

### Problème 2 : Composer non trouvé

**Erreur :** `composer: command not found`

**Solution :**

```bash
# Trouver le chemin de Composer
which composer

# Utiliser le chemin complet
/usr/local/bin/composer install --optimize-autoloader --no-dev

# Ou créer un alias
alias composer='/usr/local/bin/composer'
```

### Problème 3 : NPM non disponible

**Erreur :** `npm: command not found`

**Solution :**
1. Compiler les assets en local avant de pousser sur GitHub
2. Commiter les fichiers dans `public/build/`
3. Modifier `.gitignore` pour inclure `public/build/`

```bash
# Local
npm run build
git add public/build/ -f
git commit -m "Add compiled assets"
git push
```

### Problème 4 : Erreur 500 après git pull

**Solution :**

```bash
# Vider tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Vérifier les permissions
chmod -R 775 storage bootstrap/cache

# Réinstaller les dépendances
composer install --optimize-autoloader --no-dev

# Recréer les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Problème 5 : Modifications locales empêchent git pull

**Erreur :** `error: Your local changes to the following files would be overwritten by merge`

**Solution :**

```bash
# Sauvegarder les modifications locales
git stash

# Récupérer les modifications GitHub
git pull origin main

# Réappliquer les modifications locales (si nécessaire)
git stash pop
```

### Problème 6 : .env non pris en compte

**Solution :**

```bash
# Vérifier que .env existe
ls -la .env

# Vider le cache de config
php artisan config:clear

# Re-générer le cache
php artisan config:cache
```

### Problème 7 : Assets CSS/JS ne chargent pas

**Solution :**

```bash
# Vérifier que public/build/ existe
ls -la public/build/

# Recompiler les assets
npm install
npm run build

# Vérifier APP_URL dans .env
nano .env
# APP_URL=https://votre-vrai-domaine.com

# Vider le cache
php artisan config:cache
```

### Problème 8 : Erreur de base de données

**Vérifications :**

```bash
# Tester la connexion
php artisan tinker
DB::connection()->getPdo();
exit

# Vérifier les credentials dans .env
cat .env | grep DB_
```

---

## 🔐 Sécurité Post-Déploiement

### Actions Immédiates

1. ✅ Changer le mot de passe admin (`admin@local.host` → mot de passe fort)
2. ✅ Créer d'autres utilisateurs si besoin
3. ✅ Supprimer l'utilisateur de test (optionnel)
4. ✅ Configurer les sauvegardes automatiques (cPanel)
5. ✅ Activer HTTPS/SSL (Let's Encrypt via cPanel)
6. ✅ Vérifier `.env` : `APP_DEBUG=false`
7. ✅ Ne jamais commiter le fichier `.env` sur GitHub

### Protéger le fichier .env

```bash
# S'assurer que .env n'est pas accessible via le web
chmod 600 .env
```

### Sauvegardes

**Base de données :**
- cPanel → phpMyAdmin → Export (hebdomadaire)

**Fichiers :**
- Votre code est sur GitHub (déjà sauvegardé)
- Sauvegarder `storage/app/` (uploads utilisateurs)

---

## 📚 Ressources

- **Laravel** : https://laravel.com/docs/12.x
- **Filament** : https://filamentphp.com/docs/4.x
- **O2Switch FAQ** : https://faq.o2switch.fr/
- **GitHub Docs** : https://docs.github.com/
- **README Projet** : README.md

---

## 📝 Récapitulatif Express (TL;DR)

```bash
# ========================================
# SUR GITHUB (Une seule fois)
# ========================================
git add .
git commit -m "Ready for production"
git push origin main

# ========================================
# SSH O2SWITCH
# ========================================
ssh votreuser@votre-domaine.com

# Cloner le projet
cd ~
git clone https://github.com/votre-username/gestmatv2.git
cd gestmatv2

# Configuration
cp .env.example .env
nano .env  # Configurer DB + APP_URL + APP_ENV=production + APP_DEBUG=false
php artisan key:generate
php artisan storage:link

# Installation
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Base de données
php artisan migrate --force
php artisan db:seed --force

# Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache

# ========================================
# cPanel
# ========================================
# → Créer la base de données MySQL
# → Configurer PHP 8.2+
# → Configurer Document Root vers /home/user/gestmatv2/public

# ========================================
# CONNEXION
# ========================================
# → https://votre-domaine.com/admin
# → Email: admin@local.host | Pass: password
# → CHANGER LE MOT DE PASSE IMMÉDIATEMENT !

# ========================================
# MISES À JOUR (À chaque modification)
# ========================================
cd ~/gestmatv2
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

**🎉 Félicitations ! Votre application GestMat V2 est déployée sur O2Switch via GitHub !**

---

**Version** : 2.0 (GitHub)
**Date** : 27 Novembre 2025
**Projet** : GestMat V2
**Méthode** : Déploiement via GitHub
