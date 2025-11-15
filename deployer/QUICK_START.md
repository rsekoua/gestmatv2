# 🚀 Guide de Démarrage Rapide - Dossier Deployer

Ce guide vous aide à utiliser les fichiers du dossier `deployer/` pour déployer GestMat v2 en production.

---

## 📁 Structure du Dossier

```
deployer/
├── README.md                     # Documentation complète et recommandations
├── DEPLOYMENT_CHECKLIST.md       # Checklist étape par étape
├── QUICK_START.md               # Ce fichier
│
├── configs/                      # Fichiers de configuration
│   ├── .env.production          # Variables d'environnement production
│   ├── .env.staging             # Variables d'environnement staging
│   ├── nginx.conf               # Configuration Nginx
│   └── supervisor.conf          # Configuration Supervisor (queues)
│
├── scripts/                      # Scripts d'automatisation
│   ├── deploy.sh                # Script de déploiement
│   ├── rollback.sh              # Script de rollback
│   └── backup.sh                # Script de backup manuel
│
├── docker/                       # Configuration Docker
│   ├── Dockerfile               # Image Docker production
│   ├── docker-compose.yml       # Orchestration Docker
│   ├── php.ini                  # Configuration PHP
│   ├── opcache.ini              # Configuration OPcache
│   ├── supervisord.conf         # Supervisor pour Docker
│   └── default.conf             # Nginx pour Docker
│
├── workflows/                    # GitHub Actions CI/CD
│   ├── ci-cd.yml                # Pipeline complet
│   └── tests.yml                # Tests automatiques
│
└── docs/                         # Documentation supplémentaire
    └── OPTIMIZATIONS.md         # Optimisations de performance
```

---

## 🎯 Scénarios d'Utilisation

### Scénario 1: Déploiement Serveur Traditionnel (VPS/Dédié)

**Étapes:**

1. **Lire la documentation**
   ```bash
   cat deployer/README.md
   cat deployer/DEPLOYMENT_CHECKLIST.md
   ```

2. **Configurer l'environnement**
   ```bash
   # Copier et éditer le fichier .env
   cp deployer/configs/.env.production .env
   nano .env  # Remplir toutes les valeurs
   ```

3. **Configurer Nginx**
   ```bash
   sudo cp deployer/configs/nginx.conf /etc/nginx/sites-available/gestmat
   sudo ln -s /etc/nginx/sites-available/gestmat /etc/nginx/sites-enabled/
   # Éditer avec votre domaine
   sudo nano /etc/nginx/sites-available/gestmat
   sudo nginx -t
   sudo systemctl reload nginx
   ```

4. **Configurer Supervisor**
   ```bash
   sudo cp deployer/configs/supervisor.conf /etc/supervisor/conf.d/gestmat.conf
   # Éditer le chemin si nécessaire
   sudo nano /etc/supervisor/conf.d/gestmat.conf
   sudo supervisorctl reread
   sudo supervisorctl update
   ```

5. **Déployer avec le script**
   ```bash
   chmod +x deployer/scripts/*.sh
   ./deployer/scripts/deploy.sh production
   ```

6. **Suivre la checklist**
   - Ouvrir `deployer/DEPLOYMENT_CHECKLIST.md`
   - Cocher chaque étape au fur et à mesure

---

### Scénario 2: Déploiement Docker

**Étapes:**

1. **Créer un fichier .env**
   ```bash
   cp deployer/configs/.env.production .env
   nano .env  # Configurer
   ```

2. **Lancer avec Docker Compose**
   ```bash
   cd deployer/docker
   docker-compose up -d
   ```

3. **Vérifier les services**
   ```bash
   docker-compose ps
   docker-compose logs -f app
   ```

4. **Initialiser l'application**
   ```bash
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan storage:link
   docker-compose exec app php artisan optimize
   ```

5. **Accéder à l'application**
   - http://localhost:8080 (ou le port configuré)

---

### Scénario 3: Configuration CI/CD GitHub Actions

**Étapes:**

1. **Copier les workflows**
   ```bash
   mkdir -p .github/workflows
   cp deployer/workflows/ci-cd.yml .github/workflows/
   cp deployer/workflows/tests.yml .github/workflows/
   ```

2. **Configurer les secrets GitHub**
   - Aller dans: `Settings > Secrets and variables > Actions`
   - Ajouter:
     - `PRODUCTION_HOST`
     - `PRODUCTION_USER`
     - `PRODUCTION_SSH_KEY`
     - `STAGING_HOST` (optionnel)
     - `STAGING_USER` (optionnel)
     - `STAGING_SSH_KEY` (optionnel)
     - `SLACK_WEBHOOK` (optionnel)

3. **Pousser sur GitHub**
   ```bash
   git add .github/workflows/
   git commit -m "Add CI/CD workflows"
   git push origin main
   ```

4. **Vérifier l'exécution**
   - Aller dans: `Actions` sur GitHub
   - Vérifier que les tests passent

---

### Scénario 4: Appliquer les Optimisations

**Étapes:**

1. **Lire le guide d'optimisation**
   ```bash
   cat deployer/docs/OPTIMIZATIONS.md
   ```

2. **Créer la migration des index**
   ```bash
   php artisan make:migration add_performance_indexes_to_tables
   # Copier le contenu depuis OPTIMIZATIONS.md
   nano database/migrations/XXXX_add_performance_indexes_to_tables.php
   ```

3. **Créer le CacheService**
   ```bash
   php artisan make:class Services/CacheService
   # Copier le contenu depuis OPTIMIZATIONS.md
   ```

4. **Mettre à jour les Observers**
   ```bash
   nano app/Observers/MaterielObserver.php
   # Ajouter l'invalidation du cache
   ```

5. **Appliquer les changements**
   ```bash
   php artisan migrate
   php artisan optimize:clear
   ```

---

## 🔧 Commandes Utiles

### Déploiement

```bash
# Déployer en production
./deployer/scripts/deploy.sh production

# Déployer en staging
./deployer/scripts/deploy.sh staging

# Rollback au commit précédent
./deployer/scripts/rollback.sh

# Rollback à un commit spécifique
./deployer/scripts/rollback.sh abc123def

# Backup manuel
./deployer/scripts/backup.sh full
```

### Docker

```bash
# Démarrer tous les services
cd deployer/docker && docker-compose up -d

# Démarrer avec les outils de gestion (pgAdmin, Redis Commander)
docker-compose --profile tools up -d

# Voir les logs
docker-compose logs -f app
docker-compose logs -f queue-worker

# Exécuter des commandes artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Scaler les queue workers
docker-compose up -d --scale queue-worker=5

# Arrêter tout
docker-compose down
```

### Monitoring

```bash
# Vérifier les services
sudo systemctl status nginx
sudo systemctl status php8.4-fpm
sudo systemctl status postgresql
sudo systemctl status redis
sudo supervisorctl status gestmat:*

# Logs
tail -f /var/log/nginx/gestmat-access.log
tail -f /var/log/nginx/gestmat-error.log
tail -f storage/logs/laravel.log
tail -f /var/log/supervisor/gestmat-queue.log

# Health check
curl https://gestmat.votre-domaine.com/health
```

---

## 📋 Checklist Rapide Pré-déploiement

Avant de déployer, assurez-vous d'avoir:

- [ ] Lu `deployer/README.md`
- [ ] Configuré `.env.production` avec les vraies valeurs
- [ ] Testé sur staging
- [ ] Généré `APP_KEY` unique
- [ ] Configuré la base de données PostgreSQL
- [ ] Configuré Redis
- [ ] Configuré le serveur mail
- [ ] Pointé le domaine vers le serveur
- [ ] Configuré SSL/HTTPS
- [ ] Créé les backups automatiques
- [ ] Configuré le monitoring
- [ ] Formé les utilisateurs

---

## 🆘 Besoin d'Aide?

### Documentation Complète
```bash
cat deployer/README.md
```

### Checklist Détaillée
```bash
cat deployer/DEPLOYMENT_CHECKLIST.md
```

### Optimisations Performance
```bash
cat deployer/docs/OPTIMIZATIONS.md
```

### Problèmes Courants

**1. "Permission denied" lors du déploiement**
```bash
chmod +x deployer/scripts/*.sh
```

**2. "Database connection failed"**
- Vérifier les credentials dans `.env`
- Tester la connexion: `psql -h HOST -U USER -d DATABASE`

**3. "Queue workers not running"**
```bash
sudo supervisorctl restart gestmat:*
sudo supervisorctl tail -f gestmat-queue
```

**4. "502 Bad Gateway"**
```bash
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
```

**5. "Assets not loading"**
```bash
npm run build
php artisan optimize:clear
```

---

## 🎯 Prochaines Étapes

Après avoir déployé avec succès:

1. **Monitorer pendant 24-48h**
   - Vérifier les logs régulièrement
   - Surveiller les performances
   - Corriger les bugs rapidement

2. **Appliquer les optimisations**
   - Suivre `deployer/docs/OPTIMIZATIONS.md`
   - Implémenter les optimisations critiques
   - Mesurer les améliorations

3. **Configurer CI/CD**
   - Mettre en place GitHub Actions
   - Automatiser les déploiements futurs
   - Configurer les tests automatiques

4. **Former les utilisateurs**
   - Créer un guide utilisateur
   - Organiser des sessions de formation
   - Mettre en place un support

5. **Maintenance régulière**
   - Backups quotidiens
   - Mises à jour de sécurité
   - Monitoring continu
   - Optimisations progressives

---

## 📞 Support

Pour toute question ou problème:

1. Consulter d'abord la documentation dans `deployer/`
2. Vérifier les logs d'erreur
3. Consulter la documentation Laravel/Filament
4. Contacter l'équipe technique

---

**Bonne chance avec votre déploiement! 🚀**

**Créé par:** Claude AI
**Date:** 2025-11-15
**Version:** 1.0
