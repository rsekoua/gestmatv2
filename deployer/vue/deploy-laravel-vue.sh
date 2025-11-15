#!/bin/bash

# ==================================================
# SCRIPT DE DÉPLOIEMENT - Laravel + Vue.js + Inertia
# ==================================================
# Pour O2switch hébergement mutualisé
# Usage: bash deployer/vue/deploy-laravel-vue.sh
# ==================================================

set -e

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}"
echo "╔════════════════════════════════════════════════╗"
echo "║  GestMat v2 - Déploiement Laravel + Vue.js    ║"
echo "║  O2switch Hébergement Mutualisé                ║"
echo "╚════════════════════════════════════════════════╝"
echo -e "${NC}"

# ==================================================
# VÉRIFICATIONS
# ==================================================

echo -e "${BLUE}[1/10]${NC} Vérification environnement..."

if [ ! -f ".env" ]; then
    echo -e "${RED}✗ Fichier .env manquant${NC}"
    echo "Copiez deployer/vue/.env.laravel-vue.o2switch vers .env"
    exit 1
fi

if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP non trouvé${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)
echo -e "${GREEN}✓${NC} PHP $PHP_VERSION détecté"

# ==================================================
# MISE EN MAINTENANCE
# ==================================================

echo -e "${BLUE}[2/10]${NC} Activation mode maintenance..."
php artisan down || echo "Déjà en maintenance"

# ==================================================
# GIT PULL
# ==================================================

echo -e "${BLUE}[3/10]${NC} Récupération dernières modifications..."
git pull origin main || {
    echo -e "${RED}✗ Git pull échoué${NC}"
    php artisan up
    exit 1
}

# ==================================================
# COMPOSER
# ==================================================

echo -e "${BLUE}[4/10]${NC} Installation dépendances Composer..."
if [ -f "composer.phar" ]; then
    php composer.phar install --no-dev --optimize-autoloader --no-interaction
else
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# ==================================================
# VÉRIFICATION BUILD FRONTEND
# ==================================================

echo -e "${BLUE}[5/10]${NC} Vérification assets frontend..."

if [ ! -d "public/build" ]; then
    echo -e "${RED}✗ Dossier public/build/ manquant${NC}"
    echo ""
    echo -e "${YELLOW}IMPORTANT:${NC} Les assets Vue.js doivent être compilés EN LOCAL"
    echo ""
    echo "Sur votre machine locale, exécutez:"
    echo "  npm run build"
    echo ""
    echo "Puis uploadez le dossier public/build/ vers O2switch"
    echo "Ou commitez-le dans Git (pas recommandé pour gros projets)"
    echo ""
    php artisan up
    exit 1
fi

MANIFEST_FILE="public/build/manifest.json"
if [ ! -f "$MANIFEST_FILE" ]; then
    echo -e "${YELLOW}⚠${NC} manifest.json manquant (assets peut-être non compilés)"
fi

echo -e "${GREEN}✓${NC} Assets frontend trouvés"

# ==================================================
# MIGRATIONS
# ==================================================

echo -e "${BLUE}[6/10]${NC} Migrations base de données..."
php artisan migrate --force || {
    echo -e "${RED}✗ Migrations échouées${NC}"
    php artisan up
    exit 1
}

# ==================================================
# CACHE LARAVEL
# ==================================================

echo -e "${BLUE}[7/10]${NC} Optimisation cache Laravel..."

# Clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo -e "${GREEN}✓${NC} Cache optimisé"

# ==================================================
# STORAGE LINK
# ==================================================

echo -e "${BLUE}[8/10]${NC} Lien symbolique storage..."
php artisan storage:link 2>/dev/null || echo "Lien storage déjà existant"

# ==================================================
# PERMISSIONS
# ==================================================

echo -e "${BLUE}[9/10]${NC} Configuration permissions..."
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}✓${NC} Permissions configurées"

# ==================================================
# FIN MAINTENANCE
# ==================================================

echo -e "${BLUE}[10/10]${NC} Désactivation mode maintenance..."
php artisan up

# ==================================================
# RÉSUMÉ
# ==================================================

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║      DÉPLOIEMENT TERMINÉ AVEC SUCCÈS!         ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}Informations:${NC}"
echo "  - Version PHP: $PHP_VERSION"
echo "  - Environnement: $(grep APP_ENV .env | cut -d '=' -f2)"
echo "  - Database: $(grep DB_DATABASE .env | cut -d '=' -f2)"
echo ""
echo -e "${BLUE}Prochaines étapes:${NC}"
echo "  1. Tester l'application: $(grep APP_URL .env | cut -d '=' -f2)"
echo "  2. Vérifier les logs: tail -f storage/logs/laravel.log"
echo "  3. Vérifier cron job actif pour les queues"
echo ""
echo -e "${YELLOW}Note:${NC} Les assets Vue.js (public/build/) doivent être"
echo "compilés EN LOCAL avant chaque déploiement avec: npm run build"
echo ""

exit 0
