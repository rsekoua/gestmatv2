#!/bin/bash

# ==================================================
# GESTMAT V2 - SCRIPT D'INSTALLATION O2SWITCH
# ==================================================
# Usage: bash deployer/o2switch/install.sh
# ==================================================

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ==================================================
# FONCTIONS
# ==================================================

log() {
    echo -e "${BLUE}[$(date +'%H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✓${NC} $1"
}

error() {
    echo -e "${RED}✗${NC} $1"
    exit 1
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# ==================================================
# BANNIÈRE
# ==================================================

clear
echo -e "${BLUE}"
echo "╔════════════════════════════════════════════════╗"
echo "║   GestMat v2 - Installation O2switch           ║"
echo "║   Hébergement Mutualisé                        ║"
echo "╚════════════════════════════════════════════════╝"
echo -e "${NC}"

# ==================================================
# VÉRIFICATIONS PRÉ-INSTALLATION
# ==================================================

log "Vérification de l'environnement..."

# Vérifier PHP
if ! command -v php &> /dev/null; then
    error "PHP n'est pas installé ou non accessible"
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)
log "Version PHP détectée: $PHP_VERSION"

if (( $(echo "$PHP_VERSION < 8.2" | bc -l) )); then
    error "PHP 8.2 ou supérieur requis. Version actuelle: $PHP_VERSION"
fi

success "PHP $PHP_VERSION OK"

# Vérifier Composer
if [ ! -f "composer.phar" ] && ! command -v composer &> /dev/null; then
    warning "Composer non trouvé. Installation..."
    curl -sS https://getcomposer.org/installer | php
    COMPOSER="php composer.phar"
    success "Composer installé"
else
    if [ -f "composer.phar" ]; then
        COMPOSER="php composer.phar"
    else
        COMPOSER="composer"
    fi
    success "Composer trouvé"
fi

# ==================================================
# CONFIGURATION
# ==================================================

log "Configuration de l'application..."

# Copier .env si pas existant
if [ ! -f ".env" ]; then
    if [ -f "deployer/o2switch/.env.o2switch" ]; then
        cp deployer/o2switch/.env.o2switch .env
        success ".env créé depuis le template O2switch"
    else
        error "Template .env.o2switch non trouvé"
    fi
else
    warning ".env existe déjà, pas de modification"
fi

# Demander les informations de base de données
echo ""
log "Configuration de la base de données"
echo -e "${YELLOW}Entrez les informations de votre base MySQL (créée dans cPanel):${NC}"

read -p "Nom de la base de données (ex: cpaneluser_gestmatv2): " DB_NAME
read -p "Utilisateur MySQL (ex: cpaneluser_gestmat): " DB_USER
read -sp "Mot de passe MySQL: " DB_PASS
echo ""

# Mettre à jour .env
if [ -n "$DB_NAME" ]; then
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
    success "Base de données configurée"
fi

# Demander l'URL de l'application
read -p "URL de l'application (ex: https://votre-domaine.com): " APP_URL
if [ -n "$APP_URL" ]; then
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
    success "URL configurée"
fi

# ==================================================
# INSTALLATION DES DÉPENDANCES
# ==================================================

log "Installation des dépendances Composer..."
$COMPOSER install --optimize-autoloader --no-dev || error "Installation Composer échouée"
success "Dépendances installées"

# ==================================================
# GÉNÉRATION DE LA CLÉ
# ==================================================

log "Génération de la clé d'application..."
php artisan key:generate || error "Génération de clé échouée"
success "Clé générée"

# ==================================================
# MIGRATIONS
# ==================================================

log "Exécution des migrations de base de données..."
echo -e "${YELLOW}Voulez-vous exécuter les migrations maintenant? (y/n)${NC}"
read -p "> " RUN_MIGRATIONS

if [[ "$RUN_MIGRATIONS" =~ ^[Yy]$ ]]; then
    php artisan migrate --force || error "Migrations échouées"
    success "Migrations exécutées"
else
    warning "Migrations non exécutées. Lancez manuellement: php artisan migrate --force"
fi

# ==================================================
# LIEN STORAGE
# ==================================================

log "Création du lien symbolique storage..."
php artisan storage:link || warning "Lien storage déjà existant ou échec"

# ==================================================
# PERMISSIONS
# ==================================================

log "Configuration des permissions..."
chmod -R 755 storage bootstrap/cache
success "Permissions configurées"

# ==================================================
# OPTIMISATION
# ==================================================

log "Optimisation de l'application..."

php artisan config:cache
success "Configuration mise en cache"

php artisan route:cache
success "Routes mises en cache"

php artisan view:cache
success "Vues mises en cache"

php artisan event:cache || warning "Cache events échoué (optionnel)"

# Filament
if php artisan list | grep -q "filament:cache-components"; then
    php artisan filament:cache-components
    success "Composants Filament mis en cache"
fi

php artisan optimize
success "Optimisation générale effectuée"

# ==================================================
# CONFIGURATION PUBLIC_HTML
# ==================================================

echo ""
log "Configuration du répertoire public..."

USER_HOME=$(pwd)
PUBLIC_HTML="$HOME/public_html"

if [ -d "$PUBLIC_HTML" ]; then
    echo -e "${YELLOW}Voulez-vous créer un lien symbolique de public/ vers public_html? (y/n)${NC}"
    echo -e "${YELLOW}ATTENTION: Cela supprimera l'actuel public_html (backup sera créé)${NC}"
    read -p "> " CREATE_SYMLINK

    if [[ "$CREATE_SYMLINK" =~ ^[Yy]$ ]]; then
        # Backup
        if [ -L "$PUBLIC_HTML" ]; then
            rm "$PUBLIC_HTML"
            log "Ancien lien symbolique supprimé"
        elif [ -d "$PUBLIC_HTML" ]; then
            mv "$PUBLIC_HTML" "${PUBLIC_HTML}.backup.$(date +%Y%m%d_%H%M%S)"
            log "public_html sauvegardé"
        fi

        # Créer lien
        ln -s "$(pwd)/public" "$PUBLIC_HTML"
        success "Lien symbolique créé: public_html -> $(pwd)/public"

        # Copier .htaccess
        if [ -f "deployer/o2switch/.htaccess.public" ]; then
            cp deployer/o2switch/.htaccess.public public/.htaccess
            success ".htaccess copié et configuré"
        fi
    else
        warning "Lien symbolique non créé. Configuration manuelle requise."
        echo -e "${YELLOW}Commande manuelle:${NC}"
        echo "  mv ~/public_html ~/public_html.backup"
        echo "  ln -s $(pwd)/public ~/public_html"
    fi
else
    warning "public_html non trouvé à l'emplacement standard"
fi

# ==================================================
# RÉSUMÉ
# ==================================================

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║       INSTALLATION TERMINÉE AVEC SUCCÈS!      ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════╝${NC}"
echo ""

log "Prochaines étapes:"
echo ""
echo "1. Vérifier le fichier .env:"
echo "   nano .env"
echo ""
echo "2. Configurer le cron job dans cPanel:"
echo "   Fréquence: */5 * * * *"
echo "   Commande: cd $(pwd) && /usr/bin/php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "3. Créer un utilisateur admin Filament:"
echo "   php artisan make:filament-user"
echo ""
echo "4. Configurer SSL dans cPanel (si pas déjà fait):"
echo "   cPanel > SSL/TLS > AutoSSL (Let's Encrypt)"
echo ""
echo "5. Tester l'application:"
echo "   $APP_URL"
echo ""

echo -e "${BLUE}Documentation complète: deployer/o2switch/README_O2SWITCH.md${NC}"
echo ""

exit 0
