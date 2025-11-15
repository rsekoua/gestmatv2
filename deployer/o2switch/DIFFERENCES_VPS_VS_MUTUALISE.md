# 📊 Différences VPS vs Hébergement Mutualisé O2switch

Ce document explique les différences entre les configurations pour serveur VPS et hébergement mutualisé O2switch.

---

## 🔄 Tableau Comparatif

| Composant | VPS/Serveur Dédié | O2switch Mutualisé |
|-----------|-------------------|-------------------|
| **Accès** | Root/sudo complet | cPanel seulement |
| **Serveur Web** | Nginx configurable | Apache (géré par O2switch) |
| **Base de données** | PostgreSQL/MySQL au choix | MySQL seulement |
| **Cache** | Redis | Cache fichier |
| **Queue** | Redis Queue + Supervisor | Database Queue + Cron |
| **Sessions** | Redis | Database ou Fichier |
| **Cron Jobs** | Illimités | Limités (~5-10) |
| **Process Manager** | Supervisor | Cron scheduler |
| **SSL** | Certbot manuel | AutoSSL automatique |
| **PHP Version** | Installation personnalisée | Versions multiples via cPanel |
| **Node.js** | Serveur possible | Build en local uniquement |
| **Docker** | Oui | Non |
| **Logs** | Accès complet | Via cPanel/SSH limité |

---

## 📁 Fichiers de Configuration

### VPS/Dédié

```
deployer/
├── configs/
│   ├── .env.production         # PostgreSQL + Redis
│   ├── nginx.conf              # Configuration Nginx
│   └── supervisor.conf         # Queue workers permanents
├── scripts/
│   ├── deploy.sh               # Déploiement automatisé
│   └── rollback.sh             # Rollback Git
└── docker/
    └── docker-compose.yml      # Containerisation
```

### O2switch Mutualisé

```
deployer/o2switch/
├── .env.o2switch               # MySQL + Cache fichier
├── .htaccess.public            # Configuration Apache
├── install.sh                  # Installation guidée
├── README_O2SWITCH.md          # Documentation complète
├── QUICK_START_O2SWITCH.md     # Démarrage rapide
├── TROUBLESHOOTING.md          # Dépannage
└── DIFFERENCES_VPS_VS_MUTUALISE.md  # Ce fichier
```

---

## ⚙️ Différences de Configuration .env

### VPS (.env.production)

```env
# Base de données
DB_CONNECTION=pgsql              # PostgreSQL
DB_HOST=postgres-host.com
DB_PORT=5432

# Cache et Queue
CACHE_STORE=redis                # Redis
SESSION_DRIVER=redis             # Redis
QUEUE_CONNECTION=redis           # Redis Queue

# Mail
MAIL_MAILER=smtp                 # Serveur SMTP externe
MAIL_HOST=smtp.sendgrid.net
```

### O2switch (.env.o2switch)

```env
# Base de données
DB_CONNECTION=mysql              # MySQL seulement
DB_HOST=localhost                # Toujours localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_gestmatv2 # Préfixe cPanel

# Cache et Queue
CACHE_STORE=file                 # Cache fichier
SESSION_DRIVER=database          # Database
QUEUE_CONNECTION=database        # Database Queue + Cron

# Mail
MAIL_MAILER=smtp                 # SMTP O2switch
MAIL_HOST=mail.votre-domaine.com # Mail cPanel
MAIL_USERNAME=email@domain.com   # Email créé dans cPanel
```

---

## 🔧 Installation et Déploiement

### VPS

```bash
# Déploiement complet automatisé
./deployer/scripts/deploy.sh production

# Avec:
- git pull
- composer install
- npm run build
- migrations
- cache rebuild
- supervisor restart
- nginx reload
```

### O2switch

```bash
# Installation guidée interactive
bash deployer/o2switch/install.sh

# Ou manuel:
1. Upload fichiers via cPanel/SSH
2. Installer dépendances Composer
3. Configurer .env
4. Créer lien public_html
5. Exécuter migrations
6. Configurer cron job
```

---

## ⚡ Gestion des Queues

### VPS - Queue Workers Permanents

**Configuration Supervisor:**
```ini
[program:gestmat-queue]
command=php artisan queue:work redis
numprocs=2
autostart=true
autorestart=true
```

**Résultat:**
- ✅ Jobs traités en temps réel (< 1s)
- ✅ Plusieurs workers en parallèle
- ✅ Auto-restart en cas d'erreur
- ✅ Logs dédiés

### O2switch - Queue via Cron

**Configuration Cron:**
```bash
*/5 * * * * cd ~/gestmatv2 && php artisan schedule:run
```

**Dans Laravel (routes/console.php):**
```php
Schedule::command('queue:work --stop-when-empty')
    ->everyFiveMinutes();
```

**Résultat:**
- ⏱️ Jobs traités toutes les 5 minutes max
- ⚠️ Un seul worker à la fois
- ⚠️ Peut être interrompu
- ℹ️ Adapté pour usage modéré

---

## 📈 Performance

### VPS

| Métrique | Valeur Typique |
|----------|---------------|
| Temps réponse dashboard | 200-500ms |
| Requêtes DB/page (avec Redis) | 2-5 |
| Concurrent users | 100+ |
| Queue job latency | < 1s |
| Uptime | 99.9%+ |

### O2switch Mutualisé

| Métrique | Valeur Typique |
|----------|---------------|
| Temps réponse dashboard | 1-3s |
| Requêtes DB/page (sans Redis) | 15-30 |
| Concurrent users | 10-50 |
| Queue job latency | 0-5 min |
| Uptime | 99.9%+ |

---

## 💰 Coût Mensuel Estimé

### VPS (DigitalOcean/Similaire)

| Service | Coût |
|---------|------|
| Serveur (2CPU, 4GB RAM) | $24 |
| PostgreSQL Managed | $15 |
| Redis Managed | $15 |
| Storage (Spaces) | $5 |
| Backup | $5 |
| **Total** | **~$64/mois** |

### O2switch Mutualisé

| Service | Coût |
|---------|------|
| Hébergement illimité | €6.99 HT/mois (~€8.39 TTC) |
| MySQL inclus | ✓ |
| Email illimité | ✓ |
| SSL gratuit | ✓ |
| Backup quotidien | ✓ |
| **Total** | **~€8.39/mois** |

**Économie:** ~€56/mois (87% moins cher)

---

## 🎯 Cas d'Usage Recommandés

### Quand choisir VPS:

✅ **Volume élevé:**
- Plus de 100 utilisateurs simultanés
- Plus de 10,000 requêtes/jour
- Opérations en temps réel requises

✅ **Besoins spécifiques:**
- PostgreSQL obligatoire
- Redis requis pour performance
- Jobs temps réel critiques
- Contrôle total du serveur nécessaire

✅ **Budget disponible:**
- Budget IT > €50/mois
- Ressources pour maintenance serveur

### Quand choisir O2switch Mutualisé:

✅ **Volume modéré:**
- Moins de 50 utilisateurs simultanés
- Moins de 5,000 requêtes/jour
- Jobs asynchrones acceptables (latence 0-5min)

✅ **Contraintes:**
- Budget limité (< €20/mois)
- Pas de compétences DevOps
- Simplicité prioritaire

✅ **Contexte:**
- PME/association
- Prototype/MVP
- Application interne
- Projet en démarrage

---

## 🔄 Migration VPS → O2switch

Si vous avez déjà une installation VPS et voulez migrer vers O2switch:

### Étape 1: Export données

```bash
# Sur VPS - Export PostgreSQL
pg_dump -U user database > dump.sql

# Ou MySQL
mysqldump -u user -p database > dump.sql
```

### Étape 2: Conversion PostgreSQL → MySQL (si nécessaire)

```bash
# Utiliser pg2mysql ou phpMyAdmin
# Convertir les types de données incompatibles
```

### Étape 3: Import sur O2switch

```bash
# Via phpMyAdmin ou SSH
mysql -u cpaneluser_user -p cpaneluser_database < dump.sql
```

### Étape 4: Adapter configuration

```bash
# Copier le .env O2switch
cp deployer/o2switch/.env.o2switch .env

# Ajuster toutes les valeurs

# Tester
php artisan migrate:status
```

### Étape 5: Upload fichiers

```bash
# Via SSH ou cPanel Gestionnaire de fichiers
# Uploader storage/app/public/ avec tous les uploads
```

---

## 🔄 Migration O2switch → VPS

Si votre application grandit et nécessite un VPS:

### Étape 1: Export MySQL

```bash
# Sur O2switch
mysqldump -u user -p database > dump.sql
```

### Étape 2: Configurer VPS

```bash
# Installer PostgreSQL/MySQL
# Configurer Redis
# Installer Nginx/Apache
```

### Étape 3: Adapter configuration

```bash
# Utiliser .env.production
cp deployer/configs/.env.production .env

# Configurer avec vraies valeurs
```

### Étape 4: Import données

```bash
# Sur VPS
mysql -u user -p database < dump.sql
# Ou pour PostgreSQL: psql database < dump.sql
```

### Étape 5: Configuration serveur

```bash
# Nginx
cp deployer/configs/nginx.conf /etc/nginx/sites-available/

# Supervisor
cp deployer/configs/supervisor.conf /etc/supervisor/conf.d/

# Deploy
./deployer/scripts/deploy.sh production
```

---

## 📋 Checklist de Choix

Utilisez cette checklist pour décider:

### Répondez Oui/Non:

- [ ] Budget > €50/mois disponible → VPS
- [ ] Plus de 100 users simultanés → VPS
- [ ] Jobs temps réel requis (< 5s) → VPS
- [ ] PostgreSQL obligatoire → VPS
- [ ] Besoin contrôle total serveur → VPS
- [ ] Compétences DevOps en interne → VPS

**Si 3+ réponses OUI → VPS**
**Si majorité NON → O2switch mutualisé**

---

## 🎓 Résumé

### O2switch Mutualisé: Idéal pour

- 👥 Petites équipes (< 50 users)
- 💰 Budget limité
- 🚀 Démarrage rapide
- 🔧 Simplicité de gestion
- 📱 Applications internes/PME

### VPS: Idéal pour

- 👥 Grandes équipes (> 100 users)
- 💰 Budget confortable
- ⚡ Performance maximale
- 🔧 Contrôle total
- 🏢 Applications critiques/SaaS

---

**Pour GestMat v2:** L'hébergement mutualisé O2switch est parfaitement adapté pour:
- Usage PME/association
- < 50 employés à gérer
- < 1000 matériels en base
- Budget contrôlé
- Simplicité prioritaire

---

**Document créé:** 2025-11-15
**Auteur:** Claude AI - GestMat v2
