# Guide de déploiement - Production o2switch

## 1. Préparer la base de données MySQL

Sur o2switch cPanel :
- Créer une base de données MySQL
- Créer un utilisateur MySQL
- Noter : nom_bdd, utilisateur_bdd, mot_de_passe_bdd, hôte (généralement `localhost`)

## 2. Configuration GitHub → Serveur

### Structure des fichiers sur o2switch
```
/home/votre_compte/
├── inventaire.dap-ci.org/          # Racine du dépôt GitHub
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/                     # ← Important
│   ├── ...
└── public_html/inventaire/         # Lien symbolique vers public/
```

### Dans SSH (ou via cPanel Terminal) :
```bash
cd /home/votre_compte
git clone https://github.com/votre-username/votre-repo.git inventaire.dap-ci.org
cd inventaire.dap-ci.org
```

### Pointer le sous-domaine vers le dossier public
Dans cPanel → Domaines → Modifier `inventaire.dap-ci.org` :
- Racine du document : `/home/votre_compte/inventaire.dap-ci.org/public`

## 3. Configuration .env

```bash
cd /home/votre_compte/inventaire.dap-ci.org
cp .env.example .env
```

Éditer `.env` avec les valeurs de production :
```env
APP_NAME="Gestionnaire Matériel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://inventaire.dap-ci.org

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votre_nom_bdd
DB_USERNAME=votre_utilisateur_bdd
DB_PASSWORD=votre_mot_de_passe_bdd

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Configuration email o2switch
MAIL_MAILER=smtp
MAIL_HOST=ssl0.ovh.net
MAIL_PORT=587
MAIL_USERNAME=noreply@dap-ci.org
MAIL_PASSWORD=votre_mot_de_passe_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dap-ci.org
MAIL_FROM_NAME="${APP_NAME}"
```

## 4. Installation et configuration

```bash
# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Générer la clé d'application
php artisan key:generate

# Créer la base de données
php artisan migrate --force

# Créer les rôles et permissions
php artisan db:seed --class=RolePermissionSeeder

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize

# Compiler les assets
npm install
npm run build
```

## 5. Créer l'utilisateur admin Filament

**Note importante sur les rôles :**
- En **local** : tous les utilisateurs ont accès au panel (pour faciliter le développement)
- En **production** : l'utilisateur doit avoir un email vérifié ET un rôle assigné

```bash
php artisan tinker
```

Dans Tinker :
```php
$user = App\Models\User::create([
    'name' => 'Administrateur Principal',
    'email' => 'admin@dap-ci.org',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
]);

// Assigner le rôle super_admin
$user->assignRole('super_admin');

// Vérifier
echo "Utilisateur créé : {$user->email} avec le rôle " . $user->roles->pluck('name')->join(', ');
```

### Rôles disponibles :
- **super_admin** : Accès total + gestion des utilisateurs et rôles
- **admin** : Gestion complète sauf gestion des rôles
- **gestionnaire** : Gestion des matériels, employés, services, attributions
- **utilisateur** : Consultation uniquement (assigné automatiquement aux nouveaux inscrits)

## 6. Permissions des fichiers

```bash
chmod -R 755 storage bootstrap/cache
chown -R votre_compte:votre_compte storage bootstrap/cache
```

## 7. Fichier .htaccess dans /public

Vérifier que `/public/.htaccess` contient :
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 8. Mises à jour futures via GitHub

```bash
cd /home/votre_compte/inventaire.dap-ci.org
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## 9. Accès au panel Filament

URL : `https://inventaire.dap-ci.org/admin`

### Connexion administrateur :
- **Email** : `admin@dap-ci.org`
- **Mot de passe** : `Admin@2025!`
- **Rôle** : Super Admin

### Inscription des nouveaux utilisateurs :
1. Les utilisateurs peuvent s'inscrire via `/admin/register`
2. Ils reçoivent un email de vérification
3. Après vérification, ils obtiennent automatiquement le rôle "utilisateur" (consultation uniquement)
4. Le super admin peut modifier leurs rôles via "Administration > Utilisateurs"

### Gestion des rôles :
Accessible uniquement aux **super_admin** et **admin** via le menu "Administration > Utilisateurs"

## Notes importantes

- **Sauvegardes** : Configurer des sauvegardes automatiques de la base de données sur cPanel
- **SSL** : o2switch fournit SSL gratuit - l'activer dans cPanel
- **Logs** : Consulter les logs dans `storage/logs/laravel.log`

### ⚙️ Configuration de la Queue (Important pour les imports)

Pour que les importations de fichiers fonctionnent en production, configurez un cron job :

**Dans cPanel > Cron Jobs**, ajoutez :
```bash
* * * * * cd /home/votre_compte/inventaire.dap-ci.org && php artisan schedule:run >> /dev/null 2>&1
```

**Ce cron job permet de :**
- Traiter automatiquement les imports de fichiers
- Exécuter les tâches planifiées
- Gérer la queue en arrière-plan

### 📊 Suivi des importations

Une fois le cron configuré, vous pouvez suivre vos imports :
1. **En temps réel** : Via les notifications dans l'interface Filament (icône 🔔)
2. **Historique complet** : Menu "Administration > Historique des Imports"
3. **Progression** : Mise à jour automatique toutes les 5 secondes


# Système de suivi des importations configuré !

## Vous avez maintenant 3 façons de suivre vos importations :

1. 🔔 Notifications en temps réel (Interface Filament)

- Cliquez sur l'icône de notification en haut à droite
- Voir la progression en direct : 45/100 lignes (45%)
- Télécharger le rapport d'erreurs si besoin
- ✅ Aucune configuration nécessaire

2. 📊 Page "Historique des Imports"

- Nouvelle page créée dans le menu "Administration"
- Affiche tous les imports avec :
    - Type d'import (Matériels, Employés, Services)
    - Progression en temps réel
    - Nombre de lignes réussies/échouées
    - Date de début et de fin
- Mise à jour automatique toutes les 5 secondes
- Accessible aux Super Admin, Admin et Gestionnaires

3. ⚙️ En ligne de commande (Développement)

En local, démarrez le worker pour voir les logs :
php artisan queue:work --verbose

🚀 Configuration Production (o2switch)

Ajoutez ce cron job dans cPanel :
* * * * * cd /home/votre_compte/inventaire.dap-ci.org && php artisan schedule:run >> /dev/null 2>&1

Ce que fait ce cron :
- ✅ Traite automatiquement les imports en arrière-plan
- ✅ Exécute la queue toutes les minutes
- ✅ S'arrête automatiquement quand il n'y a plus de jobs

📝 Fichiers modifiés/créés :

1. ✅ routes/console.php - Configuration du scheduler
2. ✅ app/Filament/Pages/ViewImports.php - Page d'historique
3. ✅ resources/views/filament/pages/view-imports.blade.php - Vue
4. ✅ DEPLOIEMENT.md - Instructions mises à jour

Votre système d'import est maintenant complet avec suivi en temps réel ! 🎉
