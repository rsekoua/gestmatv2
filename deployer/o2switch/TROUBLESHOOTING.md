# 🔧 Guide de Dépannage - GestMat v2 sur O2switch

Guide complet pour résoudre les problèmes courants sur hébergement mutualisé O2switch.

---

## 🚨 Problèmes Courants

### 1. Erreur 500 - Internal Server Error

**Symptômes:** Page blanche avec erreur 500

**Causes possibles:**
- Permissions fichiers incorrectes
- Erreur dans .env
- Cache corrompu
- Erreur PHP

**Solutions:**

#### Solution 1: Vérifier les logs
```bash
# Via SSH
tail -50 ~/gestmatv2/storage/logs/laravel.log

# Via cPanel
# Gestionnaire de fichiers > gestmatv2/storage/logs/laravel.log
```

#### Solution 2: Vérifier les permissions
```bash
cd ~/gestmatv2
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

#### Solution 3: Reconstruire le cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Puis reconstruire
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Solution 4: Vérifier .htaccess
```bash
# Vérifier que .htaccess existe dans public/
ls -la public/.htaccess

# Copier depuis le template si absent
cp deployer/o2switch/.htaccess.public public/.htaccess
```

#### Solution 5: Vérifier les erreurs PHP
```bash
# Activer temporairement le debug
nano .env
# Changer: APP_DEBUG=true

# Recharger la page pour voir l'erreur
# NE PAS OUBLIER de remettre APP_DEBUG=false après!
```

---

### 2. Database Connection Error

**Symptômes:** "SQLSTATE[HY000] [1045] Access denied for user..."

**Solutions:**

#### Vérifier les credentials
```bash
# Éditer .env
nano .env

# Vérifier:
DB_CONNECTION=mysql
DB_HOST=localhost  # IMPORTANT: localhost, pas 127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_gestmatv2  # Avec le préfixe cPanel
DB_USERNAME=cpaneluser_gestmat_user
DB_PASSWORD=le_bon_mot_de_passe
```

#### Tester la connexion MySQL
```bash
# Via SSH
mysql -u cpaneluser_gestmat_user -p cpaneluser_gestmatv2

# Ou via cPanel > phpMyAdmin
```

#### Recréer la base de données
1. cPanel > Bases de données MySQL
2. Supprimer l'ancienne base (ATTENTION: backup d'abord!)
3. Recréer base + utilisateur
4. Donner TOUS les privilèges
5. Mettre à jour .env

---

### 3. Page blanche (sans erreur 500)

**Symptômes:** Page complètement blanche, pas de message d'erreur

**Solutions:**

#### Vérifier .env existe
```bash
ls -la ~/gestmatv2/.env

# Si absent:
cp deployer/o2switch/.env.o2switch .env
php artisan key:generate
```

#### Vérifier APP_KEY
```bash
grep APP_KEY .env

# Si vide ou "base64:":
php artisan key:generate
```

#### Vérifier le lien public_html
```bash
ls -la ~/public_html

# Devrait afficher:
# lrwxrwxrwx 1 user user XX date public_html -> /home/user/gestmatv2/public

# Si incorrect:
mv ~/public_html ~/public_html.backup
ln -s ~/gestmatv2/public ~/public_html
```

---

### 4. Assets non chargés (CSS/JS manquants)

**Symptômes:** Page affichée sans style, erreurs 404 pour CSS/JS

**Solutions:**

#### Vérifier le build des assets
```bash
# Sur votre machine locale:
npm run build

# Uploader le dossier public/build/ vers O2switch
```

#### Vérifier APP_URL dans .env
```bash
nano .env

# Doit correspondre exactement:
APP_URL=https://votre-domaine.com
# Pas de slash final!
```

#### Reconstruire le cache
```bash
php artisan config:cache
php artisan view:cache
```

---

### 5. Upload de fichiers échoue

**Symptômes:** Erreur lors de l'upload d'images/documents

**Solutions:**

#### Vérifier storage link
```bash
php artisan storage:link

# Vérifier que le lien existe
ls -la ~/gestmatv2/public/storage
```

#### Vérifier permissions
```bash
chmod -R 755 ~/gestmatv2/storage/app/public
```

#### Vérifier limites PHP
```bash
# cPanel > Sélectionner une version de PHP > Options

# Vérifier/Ajuster:
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 512M
```

#### Vérifier .htaccess
```apache
# Dans public/.htaccess, ajouter si absent:
<IfModule mod_php8.c>
    php_value upload_max_filesize 50M
    php_value post_max_size 50M
</IfModule>
```

---

### 6. Queue Jobs ne s'exécutent pas

**Symptômes:** PDF non générés, imports/exports bloqués

**Solutions:**

#### Vérifier le cron job
```bash
# Via SSH
crontab -l

# Devrait afficher:
# */5 * * * * cd /home/user/gestmatv2 && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

#### Configurer cron dans cPanel
1. cPanel > Tâches Cron
2. Ajouter:
   - Minute: `*/5` (toutes les 5 minutes)
   - Commande: `cd /home/cpaneluser/gestmatv2 && /usr/bin/php artisan schedule:run >> /dev/null 2>&1`

#### Vérifier le chemin PHP
```bash
which php
# Copier le chemin exact dans la commande cron
```

#### Tester manuellement
```bash
php artisan schedule:run
php artisan queue:work --stop-when-empty
```

#### Vérifier la table jobs
```bash
mysql -u user -p database -e "SELECT * FROM jobs LIMIT 10;"

# Ou via phpMyAdmin
```

---

### 7. Emails non envoyés

**Symptômes:** Notifications non reçues

**Solutions:**

#### Vérifier configuration SMTP
```bash
nano .env

# Pour email cPanel:
MAIL_MAILER=smtp
MAIL_HOST=mail.votre-domaine.com
MAIL_PORT=587
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=mot_de_passe_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
```

#### Créer l'adresse email dans cPanel
1. cPanel > Comptes de messagerie
2. Créer: noreply@votre-domaine.com
3. Noter le mot de passe
4. Utiliser dans .env

#### Tester l'envoi
```bash
php artisan tinker

>>> Mail::raw('Test email from GestMat', function($msg) {
    $msg->to('votre@email.com')
        ->subject('Test O2switch');
});

# Vérifier réception
```

#### Vérifier les logs
```bash
tail -50 ~/gestmatv2/storage/logs/laravel.log | grep -i mail
```

---

### 8. Erreur "Class not found"

**Symptômes:** Erreur "Class 'App\Models\...' not found"

**Solutions:**

#### Reconstruire l'autoload
```bash
composer dump-autoload --optimize
```

#### Vérifier le cache
```bash
php artisan config:clear
php artisan cache:clear
```

#### Vérifier les namespaces
```php
// Dans le fichier concerné
namespace App\Models;  // Doit correspondre au chemin
```

---

### 9. Erreur "Too Many Redirects"

**Symptômes:** Boucle de redirection infinie

**Solutions:**

#### Vérifier .htaccess
```apache
# Dans public/.htaccess
# S'assurer d'avoir:
RewriteCond %{HTTPS} off
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

#### Vérifier APP_URL
```bash
nano .env

# Doit être exactement:
APP_URL=https://votre-domaine.com
# Pas de slash final, pas de www si pas utilisé
```

---

### 10. Performance lente

**Symptômes:** Pages qui chargent très lentement (>5s)

**Solutions:**

#### Vérifier le cache est actif
```bash
# Reconstruire tous les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### Vérifier OPcache
```bash
php -i | grep opcache

# Devrait afficher opcache.enable = On
```

#### Activer OPcache dans cPanel
1. cPanel > Sélectionner une version de PHP
2. Extensions > Activer `opcache`

#### Optimiser Composer
```bash
composer dump-autoload --optimize --classmap-authoritative
```

#### Vérifier les requêtes lentes
```bash
# Activer query logging temporairement
nano .env

# Ajouter:
DB_LOG_QUERIES=true
LOG_LEVEL=debug

# Recharger une page lente
# Vérifier les logs:
tail -100 ~/gestmatv2/storage/logs/laravel.log

# Ne pas oublier de désactiver après!
```

---

## 🔍 Outils de Diagnostic

### Vérifier l'état général

```bash
# Information PHP
php -i | less

# Version PHP
php -v

# Extensions chargées
php -m

# Configuration Laravel
php artisan about

# Vérifier .env est chargé
php artisan tinker
>>> config('app.name')
>>> config('database.default')
```

### Tester les connexions

```bash
# Test MySQL
mysql -u username -p database_name -e "SELECT 1;"

# Test SMTP (via tinker)
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@example.com'));
```

### Vérifier les permissions

```bash
# Afficher permissions
ls -la storage/
ls -la bootstrap/cache/

# Devrait être 755 pour dossiers, 644 pour fichiers
find storage -type d -exec ls -ld {} \;
find storage -type f -exec ls -l {} \; | head
```

---

## 📞 Quand Contacter le Support O2switch

Contactez O2switch si:

- ❌ PHP 8.2+ non disponible
- ❌ Extensions PHP requises non activables
- ❌ Limites de ressources dépassées (CPU, RAM, I/O)
- ❌ Problèmes de connexion SSH
- ❌ Problèmes de certificat SSL
- ❌ Serveur mail ne fonctionne pas
- ❌ Problèmes de permissions persistants

**Contact O2switch:**
- Email: support@o2switch.fr
- Téléphone: 04 44 44 60 40
- Chat: Via le site O2switch
- Ticket: Interface client O2switch

---

## 📋 Checklist de Dépannage Générale

Avant de contacter le support, essayez dans cet ordre:

1. [ ] Vérifier les logs: `storage/logs/laravel.log`
2. [ ] Vérifier .env existe et est correct
3. [ ] Reconstruire le cache: `php artisan config:cache`
4. [ ] Vérifier permissions: `chmod -R 755 storage`
5. [ ] Vérifier lien public_html existe
6. [ ] Tester connexion MySQL
7. [ ] Vérifier APP_KEY est généré
8. [ ] Vérifier .htaccess dans public/
9. [ ] Tester en local si possible
10. [ ] Consulter documentation O2switch

---

## 🔧 Scripts Utiles

### Script de diagnostic complet

```bash
#!/bin/bash
# diagnostic.sh - Vérifier l'état de l'installation

echo "=== DIAGNOSTIC GESTMAT V2 O2SWITCH ==="
echo ""

echo "1. Version PHP:"
php -v | head -1

echo ""
echo "2. Extensions PHP critiques:"
php -m | grep -E "(pdo|mysql|mbstring|xml|curl|zip|gd)"

echo ""
echo "3. Fichier .env:"
if [ -f ".env" ]; then
    echo "✓ .env existe"
    grep -E "^(APP_KEY|DB_DATABASE|APP_URL)" .env | sed 's/=.*/=***/'
else
    echo "✗ .env manquant!"
fi

echo ""
echo "4. Permissions storage:"
ls -ld storage | awk '{print $1, $3, $4, $9}'

echo ""
echo "5. Lien public_html:"
ls -l ~/public_html 2>/dev/null || echo "Lien non trouvé"

echo ""
echo "6. Base de données:"
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connexion OK';" 2>&1 | tail -1

echo ""
echo "7. Cache Laravel:"
ls -la bootstrap/cache/*.php 2>/dev/null | wc -l | awk '{print $1 " fichiers de cache"}'

echo ""
echo "8. Cron job:"
crontab -l 2>/dev/null | grep artisan || echo "Pas de cron Laravel trouvé"

echo ""
echo "=== FIN DIAGNOSTIC ==="
```

Sauvegarder comme `diagnostic.sh` et exécuter:
```bash
chmod +x diagnostic.sh
./diagnostic.sh
```

---

**Document créé:** 2025-11-15
**Hébergeur:** O2switch
**Support:** deployer/o2switch/README_O2SWITCH.md
