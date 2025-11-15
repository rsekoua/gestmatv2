# ✅ Checklist de Déploiement - GestMat v2

**Date de déploiement prévue:** _______________
**Responsable du déploiement:** _______________
**Environnement:** [ ] Staging [ ] Production

---

## 📋 Pré-déploiement (1-2 jours avant)

### Infrastructure

- [ ] **Serveur provisionné et accessible**
  - [ ] Serveur de production configuré
  - [ ] Accès SSH configuré avec clés
  - [ ] Nom de domaine pointé vers le serveur
  - [ ] Certificat SSL configuré (Let's Encrypt ou autre)

- [ ] **Base de données PostgreSQL**
  - [ ] Instance PostgreSQL installée (v16+)
  - [ ] Utilisateur et base de données créés
  - [ ] Connexions sécurisées configurées (SSL)
  - [ ] Backup automatique configuré
  - [ ] Restauration testée

- [ ] **Redis**
  - [ ] Redis installé (v7+)
  - [ ] Mot de passe configuré
  - [ ] Persistence activée (AOF ou RDB)
  - [ ] Connexion testée

- [ ] **Services système**
  - [ ] Nginx installé et configuré
  - [ ] PHP 8.4-FPM installé avec extensions requises
  - [ ] Supervisor installé pour les queues
  - [ ] Firewall configuré (ports 80, 443, 22)

### Configuration Application

- [ ] **Fichiers de configuration**
  - [ ] `.env.production` créé et rempli
  - [ ] `APP_KEY` généré: `php artisan key:generate`
  - [ ] Toutes les variables d'environnement définies
  - [ ] Secrets sécurisés (pas de valeurs par défaut)

- [ ] **Vérification des variables critiques**
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL` correct
  - [ ] `DB_*` configuré pour PostgreSQL
  - [ ] `REDIS_*` configuré
  - [ ] `MAIL_*` configuré avec service réel
  - [ ] `CACHE_STORE=redis`
  - [ ] `SESSION_DRIVER=redis`
  - [ ] `QUEUE_CONNECTION=redis`

### Tests

- [ ] **Tests locaux**
  - [ ] Tous les tests Pest passent: `php artisan test`
  - [ ] Laravel Pint appliqué: `vendor/bin/pint`
  - [ ] Audit de sécurité: `composer audit`
  - [ ] Build frontend réussi: `npm run build`

- [ ] **Tests staging**
  - [ ] Application déployée sur staging
  - [ ] Toutes les fonctionnalités testées
  - [ ] Performance validée
  - [ ] Imports/exports testés
  - [ ] Génération PDF testée
  - [ ] Emails testés

---

## 🚀 Déploiement (Jour J)

### Préparation

- [ ] **Backup complet**
  - [ ] Backup de la base de données actuelle (si migration)
  - [ ] Backup des fichiers de configuration
  - [ ] Backup des fichiers uploadés
  - [ ] Vérification que les backups sont récupérables

- [ ] **Communication**
  - [ ] Utilisateurs notifiés de la fenêtre de maintenance
  - [ ] Support/Équipe technique alertés
  - [ ] Rollback plan préparé

### Installation

- [ ] **Cloner le repository**
  ```bash
  git clone git@github.com:votre-org/gestmatv2.git /var/www/gestmatv2
  cd /var/www/gestmatv2
  git checkout main
  ```

- [ ] **Installation des dépendances**
  ```bash
  composer install --optimize-autoloader --no-dev
  npm ci --production
  npm run build
  ```

- [ ] **Configuration**
  ```bash
  cp deployer/configs/.env.production .env
  # Éditer .env avec les vraies valeurs
  php artisan key:generate
  ```

- [ ] **Base de données**
  ```bash
  php artisan migrate --force
  # Si nécessaire: php artisan db:seed --class=ProductionSeeder
  ```

- [ ] **Optimisation**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  php artisan filament:cache-components
  php artisan storage:link
  ```

- [ ] **Permissions**
  ```bash
  chown -R www-data:www-data /var/www/gestmatv2
  chmod -R 755 /var/www/gestmatv2
  chmod -R 775 /var/www/gestmatv2/storage
  chmod -R 775 /var/www/gestmatv2/bootstrap/cache
  ```

### Configuration Services

- [ ] **Nginx**
  ```bash
  sudo cp deployer/configs/nginx.conf /etc/nginx/sites-available/gestmat
  sudo ln -s /etc/nginx/sites-available/gestmat /etc/nginx/sites-enabled/
  sudo nginx -t
  sudo systemctl reload nginx
  ```

- [ ] **Supervisor (Queues)**
  ```bash
  sudo cp deployer/configs/supervisor.conf /etc/supervisor/conf.d/gestmat.conf
  sudo supervisorctl reread
  sudo supervisorctl update
  sudo supervisorctl start gestmat:*
  ```

- [ ] **SSL/HTTPS**
  ```bash
  sudo certbot --nginx -d gestmat.votre-domaine.com
  # Tester le renouvellement: sudo certbot renew --dry-run
  ```

---

## ✅ Vérification Post-déploiement

### Tests Fonctionnels

- [ ] **Accès application**
  - [ ] Site accessible via HTTPS
  - [ ] Redirection HTTP → HTTPS fonctionne
  - [ ] Certificat SSL valide
  - [ ] Pas d'erreurs dans les logs Nginx

- [ ] **Fonctionnalités critiques**
  - [ ] Login administrateur fonctionne
  - [ ] Dashboard s'affiche correctement
  - [ ] Liste des matériels accessible
  - [ ] Création d'une attribution fonctionne
  - [ ] Génération PDF fonctionne
  - [ ] Import/Export fonctionnent
  - [ ] Logs d'activité enregistrés

- [ ] **Performance**
  - [ ] Temps de chargement < 2s
  - [ ] Pas de requêtes N+1
  - [ ] Cache Redis actif
  - [ ] OPcache actif

### Monitoring

- [ ] **Health checks**
  ```bash
  curl https://gestmat.votre-domaine.com/health
  # Devrait retourner 200 OK
  ```

- [ ] **Services actifs**
  ```bash
  sudo systemctl status nginx
  sudo systemctl status php8.4-fpm
  sudo systemctl status postgresql
  sudo systemctl status redis
  sudo supervisorctl status gestmat:*
  ```

- [ ] **Logs**
  ```bash
  tail -f /var/log/nginx/gestmat-access.log
  tail -f /var/log/nginx/gestmat-error.log
  tail -f /var/www/gestmatv2/storage/logs/laravel.log
  tail -f /var/log/supervisor/gestmat-queue.log
  ```

- [ ] **Queue workers**
  ```bash
  sudo supervisorctl status gestmat:*
  # Tous doivent être en RUNNING
  ```

### Sécurité

- [ ] **Permissions fichiers**
  ```bash
  # Vérifier que .env n'est pas accessible publiquement
  curl https://gestmat.votre-domaine.com/.env
  # Devrait retourner 404 ou 403
  ```

- [ ] **Headers de sécurité**
  ```bash
  curl -I https://gestmat.votre-domaine.com
  # Vérifier présence de:
  # - X-Frame-Options
  # - X-Content-Type-Options
  # - Strict-Transport-Security
  ```

- [ ] **Rate limiting**
  - [ ] Tester limite de login (5 tentatives max)
  - [ ] Vérifier throttling API

---

## 📊 Configuration Monitoring

- [ ] **Monitoring application**
  - [ ] Laravel Pulse installé et configuré (ou APM)
  - [ ] Error tracking configuré (Sentry/Flare)
  - [ ] Uptime monitoring configuré

- [ ] **Alertes**
  - [ ] Alertes serveur configurées (CPU, RAM, Disk)
  - [ ] Alertes application (erreurs 5xx)
  - [ ] Alertes base de données (connexions, slow queries)

- [ ] **Backups automatiques**
  ```bash
  # Vérifier cron de backup
  crontab -l
  # Devrait contenir:
  # 0 2 * * * cd /var/www/gestmatv2 && php artisan backup:run --only-db
  ```

---

## 🔄 Configuration CI/CD

- [ ] **GitHub Actions**
  - [ ] Workflows copiés dans `.github/workflows/`
  - [ ] Secrets GitHub configurés:
    - [ ] `PRODUCTION_HOST`
    - [ ] `PRODUCTION_USER`
    - [ ] `PRODUCTION_SSH_KEY`
    - [ ] `SLACK_WEBHOOK` (optionnel)

- [ ] **Test automatique**
  - [ ] Push sur `main` déclenche déploiement production
  - [ ] Pull requests déclenchent tests automatiques

---

## 📝 Documentation

- [ ] **Documentation mise à jour**
  - [ ] URL de production documentée
  - [ ] Credentials admin sauvegardés (gestionnaire de mots de passe)
  - [ ] Procédures de rollback documentées
  - [ ] Contact support technique défini

- [ ] **Formation utilisateurs**
  - [ ] Guide utilisateur créé
  - [ ] Session de formation planifiée
  - [ ] Support post-déploiement planifié

---

## 🎯 Post-déploiement (Première semaine)

### Jour 1

- [ ] **Surveillance intensive**
  - [ ] Vérifier logs toutes les heures
  - [ ] Surveiller performance
  - [ ] Répondre rapidement aux tickets

### Jours 2-3

- [ ] **Ajustements**
  - [ ] Optimiser requêtes lentes identifiées
  - [ ] Ajuster configuration cache si nécessaire
  - [ ] Corriger bugs mineurs

### Jours 4-7

- [ ] **Optimisation**
  - [ ] Analyser patterns d'utilisation
  - [ ] Optimiser si goulots d'étranglement
  - [ ] Planifier prochaines améliorations

### Revue hebdomadaire

- [ ] **Métriques à vérifier**
  - [ ] Temps de réponse moyen
  - [ ] Taux d'erreur
  - [ ] Utilisation ressources (CPU, RAM, DB)
  - [ ] Taille backups
  - [ ] Satisfaction utilisateurs

---

## 🔥 Plan de Rollback (En cas de problème critique)

Si déploiement échoue ou problème majeur:

1. **Activer maintenance mode**
   ```bash
   php artisan down
   ```

2. **Exécuter rollback**
   ```bash
   cd /var/www/gestmatv2
   ./deployer/scripts/rollback.sh [commit-hash]
   ```

3. **Restaurer base de données** (si nécessaire)
   ```bash
   # Restaurer depuis backup
   pg_restore -U gestmat -d gestmatv2_production backup.sql
   ```

4. **Vérifier fonctionnement**
   ```bash
   curl https://gestmat.votre-domaine.com/health
   ```

5. **Désactiver maintenance**
   ```bash
   php artisan up
   ```

6. **Notifier équipe et utilisateurs**

---

## 📞 Contacts d'Urgence

| Rôle | Nom | Contact |
|------|-----|---------|
| Lead Developer | ____________ | ____________ |
| DevOps | ____________ | ____________ |
| DBA | ____________ | ____________ |
| Support | ____________ | ____________ |
| Hébergeur Support | ____________ | ____________ |

---

## ✍️ Signatures

**Déploiement vérifié par:**

| Nom | Rôle | Date | Signature |
|-----|------|------|-----------|
| ____________ | Lead Dev | ______ | __________ |
| ____________ | DevOps | ______ | __________ |
| ____________ | QA | ______ | __________ |

---

**Notes additionnelles:**

_______________________________________________________________________
_______________________________________________________________________
_______________________________________________________________________

---

**Déploiement complété le:** _______________
**Temps total:** ___________ heures
**Incidents:** [ ] Aucun [ ] Mineurs [ ] Majeurs (détails en annexe)
