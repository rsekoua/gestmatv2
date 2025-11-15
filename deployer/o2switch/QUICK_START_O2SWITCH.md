# 🚀 Démarrage Rapide - GestMat v2 sur O2switch

**Temps estimé:** 1-2 heures
**Niveau:** Intermédiaire
**Prérequis:** Compte O2switch actif avec accès cPanel

---

## 📋 Checklist Pré-déploiement (15 min)

### Dans cPanel O2switch

- [ ] **Connexion:** https://cpanel.o2switch.fr
- [ ] **Base de données créée** via "Bases de données MySQL"
- [ ] **Utilisateur MySQL créé** avec tous les privilèges
- [ ] **PHP 8.3** sélectionné (ou 8.2 minimum)
- [ ] **Extensions PHP** activées (voir liste ci-dessous)
- [ ] **Domaine configuré** et accessible
- [ ] **SSL activé** (Let's Encrypt AutoSSL)
- [ ] **Email créé** pour les notifications (ex: noreply@votre-domaine.com)

### Extensions PHP requises

Dans cPanel > "Sélectionner une version de PHP" > Activer:
- mbstring, pdo, pdo_mysql, zip, gd, curl, xml, bcmath, fileinfo, tokenizer, json, openssl

---

## 🎯 Déploiement en 6 Étapes

### Étape 1: Préparation (5 min)

**Créer la base de données:**

1. cPanel > **Bases de données MySQL** > **Assistant bases de données MySQL**
2. Nom: `gestmatv2`
3. Utilisateur: `gestmat_user` avec mot de passe fort (générer)
4. Privilèges: **TOUS** ✓
5. **Noter les infos:**
   ```
   DB_DATABASE: votrenom_gestmatv2
   DB_USERNAME: votrenom_gestmat_user
   DB_PASSWORD: [le mot de passe généré]
   ```

---

### Étape 2: Upload du Projet (10 min)

**Option A: Via SSH (Recommandé)**

```bash
# Connexion SSH
ssh votrenom@votredomaine.com

# Cloner le projet
cd ~
git clone https://github.com/votre-org/gestmatv2.git gestmatv2
cd gestmatv2
```

**Option B: Via Gestionnaire de Fichiers cPanel**

1. Télécharger le ZIP du projet depuis GitHub
2. cPanel > **Gestionnaire de fichiers**
3. **Télécharger** le fichier ZIP
4. **Extraire** dans le répertoire home

---

### Étape 3: Configuration (15 min)

**Installer Composer et dépendances:**

```bash
cd ~/gestmatv2

# Installer Composer (si nécessaire)
curl -sS https://getcomposer.org/installer | php

# Installer dépendances
php composer.phar install --optimize-autoloader --no-dev
```

**Configurer .env:**

```bash
# Copier le fichier d'exemple O2switch
cp deployer/o2switch/.env.o2switch .env

# Éditer avec nano ou cPanel
nano .env
```

**Remplir les valeurs:**
```env
APP_URL=https://votre-domaine.com
DB_DATABASE=votrenom_gestmatv2
DB_USERNAME=votrenom_gestmat_user
DB_PASSWORD=mot_de_passe_mysql
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=mot_de_passe_email
```

**Générer la clé:**

```bash
php artisan key:generate
```

---

### Étape 4: Lien vers public_html (10 min)

**Créer le lien symbolique:**

```bash
# Backup de l'ancien public_html
mv ~/public_html ~/public_html.backup

# Créer le lien vers le dossier public de Laravel
ln -s ~/gestmatv2/public ~/public_html
```

**Créer .htaccess de protection:**

Dans `~/gestmatv2/.htaccess`:
```apache
Order deny,allow
Deny from all
```

---

### Étape 5: Initialisation (10 min)

**Migrations et optimisations:**

```bash
cd ~/gestmatv2

# Exécuter les migrations
php artisan migrate --force

# Créer le lien storage
php artisan storage:link

# Permissions
chmod -R 755 storage bootstrap/cache

# Cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

# Optimisation
php artisan optimize
```

---

### Étape 6: Configuration Cron (5 min)

**Ajouter le scheduler Laravel:**

1. cPanel > **Tâches Cron**
2. **Ajouter une nouvelle tâche**
3. Paramètres:
   - **Fréquence:** Toutes les 5 minutes
   - **Commande:**
     ```bash
     cd /home/votrenom/gestmatv2 && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
     ```

**Important:** Remplacer `votrenom` par votre nom d'utilisateur cPanel.

---

## ✅ Vérification Post-déploiement

### Tests Rapides

1. **Accès HTTPS:**
   ```
   https://votre-domaine.com
   ```
   → Devrait afficher l'application

2. **Login Admin:**
   - Créer un utilisateur: `php artisan make:filament-user`
   - Se connecter via `/admin`

3. **Test Upload:**
   - Créer un matériel avec une image
   - Vérifier que l'image s'affiche

4. **Test Email:**
   ```bash
   php artisan tinker
   >>> Mail::raw('Test email', function($msg) { $msg->to('votre@email.com'); });
   ```

5. **Vérifier Queue:**
   ```bash
   php artisan queue:work --stop-when-empty
   ```

---

## 🔧 Configuration Email O2switch

### Option 1: Email cPanel (Recommandé)

1. cPanel > **Comptes de messagerie**
2. Créer: `noreply@votre-domaine.com`
3. Noter le mot de passe
4. Configuration dans `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mail.votre-domaine.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@votre-domaine.com
   MAIL_PASSWORD=mot_de_passe_email
   MAIL_ENCRYPTION=tls
   ```

### Option 2: SMTP O2switch Direct

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.o2switch.net
MAIL_PORT=587
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=mot_de_passe_email
MAIL_ENCRYPTION=tls
```

---

## 🎨 Build des Assets (Important!)

**Sur votre machine locale AVANT le déploiement:**

```bash
# Installer dépendances Node
npm ci

# Build production
npm run build

# Upload SEULEMENT le dossier public/build/ vers O2switch
```

**Via cPanel:**
- Upload le dossier `public/build/` compilé
- Ne PAS installer Node.js sur le serveur mutualisé

---

## 📊 Performance sur O2switch

### Optimisations Activées

✅ **OPcache** - Activé par défaut par O2switch
✅ **PHP-FPM** - Activé par défaut
✅ **Cache fichier** - Laravel cache optimisé
✅ **Gzip compression** - Activé via .htaccess

### Performance Attendue

| Métrique | Valeur |
|----------|--------|
| Première visite | 1-3s |
| Visites suivantes | 0.5-1.5s |
| Dashboard Filament | 1-2s |
| Génération PDF | 2-4s |

---

## 🚨 Dépannage Rapide

### Erreur 500

```bash
# Vérifier logs
tail ~/gestmatv2/storage/logs/laravel.log

# Reconstruire cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
```

### Permissions

```bash
chmod -R 755 ~/gestmatv2/storage
chmod -R 755 ~/gestmatv2/bootstrap/cache
```

### Database Connection Error

- Vérifier DB_HOST=localhost (pas 127.0.0.1)
- Vérifier le nom avec préfixe cPanel
- Tester dans cPanel > phpMyAdmin

### Queue Jobs ne s'exécutent pas

```bash
# Vérifier cron
crontab -l

# Tester manuellement
php artisan schedule:run
php artisan queue:work --stop-when-empty
```

---

## 📞 Besoin d'Aide?

### Documentation Complète

```bash
cat deployer/o2switch/README_O2SWITCH.md
```

### Support O2switch

- **Email:** support@o2switch.fr
- **Téléphone:** 04 44 44 60 40
- **FAQ:** https://faq.o2switch.fr
- **Chat:** Disponible sur le site

### Logs

```bash
# Application
tail -f ~/gestmatv2/storage/logs/laravel.log

# Apache (via cPanel)
# cPanel > Métriques > Erreurs
```

---

## 🔄 Mise à Jour Future

```bash
# Connexion SSH
ssh votrenom@votredomaine.com
cd ~/gestmatv2

# Maintenance ON
php artisan down

# Update code
git pull origin main

# Update dependencies
php composer.phar install --optimize-autoloader --no-dev

# Migrations
php artisan migrate --force

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

# Maintenance OFF
php artisan up
```

---

## 📋 Checklist Finale

- [ ] Site accessible en HTTPS
- [ ] SSL valide (cadenas vert)
- [ ] Login admin fonctionne
- [ ] Dashboard charge < 3s
- [ ] Création matériel OK
- [ ] Upload images OK
- [ ] PDF generation OK
- [ ] Emails envoyés OK
- [ ] Cron job actif
- [ ] Logs propres
- [ ] Backup cPanel configuré

---

**Félicitations! GestMat v2 est déployé sur O2switch! 🎉**

**Temps total:** ~1-2 heures
**Document créé:** 2025-11-15
**Support:** deployer/o2switch/README_O2SWITCH.md
