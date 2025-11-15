#!/bin/bash

# ==================================================
# GESTMAT V2 - ROLLBACK SCRIPT
# ==================================================
# Usage: ./rollback.sh [commit-hash]
# Example: ./rollback.sh abc123def
# If no commit provided, rolls back to previous commit
# ==================================================

set -e  # Exit on error
set -u  # Exit on undefined variable

# ==================================================
# CONFIGURATION
# ==================================================
PROJECT_DIR="/var/www/gestmatv2"
TARGET_COMMIT="${1:-HEAD~1}"
BACKUP_DIR="/var/backups/gestmatv2"
LOG_FILE="/var/log/gestmatv2-rollback.log"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ==================================================
# FUNCTIONS
# ==================================================

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

confirm() {
    read -p "$(echo -e ${YELLOW}$1${NC}) [y/N]: " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log "Rollback cancelled by user"
        exit 0
    fi
}

# ==================================================
# PRE-ROLLBACK CHECKS
# ==================================================

log "Starting rollback process..."
log "Target commit: $TARGET_COMMIT"

# Check if running as correct user
if [ "$EUID" -eq 0 ]; then
    error "Do not run this script as root"
fi

cd "$PROJECT_DIR" || error "Failed to change to project directory"

# Get current commit
CURRENT_COMMIT=$(git rev-parse HEAD)
log "Current commit: $CURRENT_COMMIT"

# Verify target commit exists
if ! git cat-file -e "$TARGET_COMMIT" 2>/dev/null; then
    error "Target commit does not exist: $TARGET_COMMIT"
fi

# Show what will change
log "Changes that will be rolled back:"
git log --oneline "$TARGET_COMMIT..HEAD"

# Confirm rollback
confirm "Are you sure you want to rollback to $TARGET_COMMIT?"

# ==================================================
# STEP 1: ENABLE MAINTENANCE MODE
# ==================================================

log "Enabling maintenance mode..."
php artisan down --render="errors::503" || warning "Maintenance mode failed"

# ==================================================
# STEP 2: BACKUP CURRENT STATE
# ==================================================

log "Creating backup of current state..."
mkdir -p "$BACKUP_DIR"
BACKUP_TIMESTAMP=$(date +'%Y%m%d-%H%M%S')

# Backup database
php artisan backup:run --only-db || warning "Database backup failed"

# Backup current commit reference
echo "$CURRENT_COMMIT" > "$BACKUP_DIR/commit-before-rollback-$BACKUP_TIMESTAMP.txt"

# ==================================================
# STEP 3: ROLLBACK CODE
# ==================================================

log "Rolling back code to $TARGET_COMMIT..."
git reset --hard "$TARGET_COMMIT" || error "Git rollback failed"

NEW_COMMIT=$(git rev-parse HEAD)
success "Code rolled back to: $NEW_COMMIT"

# ==================================================
# STEP 4: REINSTALL DEPENDENCIES
# ==================================================

log "Reinstalling Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction || error "Composer install failed"

log "Reinstalling NPM dependencies..."
npm ci --production || error "NPM install failed"

# ==================================================
# STEP 5: REBUILD ASSETS
# ==================================================

log "Rebuilding frontend assets..."
npm run build || error "Asset build failed"

# ==================================================
# STEP 6: ROLLBACK MIGRATIONS (OPTIONAL)
# ==================================================

warning "Database migrations are NOT automatically rolled back"
log "If you need to rollback migrations, run manually:"
log "  php artisan migrate:rollback --step=N"

confirm "Do you want to rollback the last migration batch?"
if [[ $REPLY =~ ^[Yy]$ ]]; then
    log "Rolling back migrations..."
    php artisan migrate:rollback || error "Migration rollback failed"
fi

# ==================================================
# STEP 7: CLEAR AND REBUILD CACHE
# ==================================================

log "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

log "Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components

# ==================================================
# STEP 8: RESTART SERVICES
# ==================================================

log "Restarting services..."
php artisan queue:restart
sudo supervisorctl restart gestmat:* || warning "Supervisor restart failed"
sudo systemctl restart php8.4-fpm || warning "PHP-FPM restart failed"

# ==================================================
# STEP 9: HEALTH CHECK
# ==================================================

log "Running health checks..."
sleep 2

APP_URL=$(php artisan tinker --execute="echo config('app.url');")
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/health" || echo "000")

if [ "$HTTP_CODE" != "200" ]; then
    error "Health check failed. HTTP code: $HTTP_CODE"
fi

success "Health check passed"

# ==================================================
# STEP 10: DISABLE MAINTENANCE MODE
# ==================================================

log "Disabling maintenance mode..."
php artisan up

# ==================================================
# ROLLBACK SUMMARY
# ==================================================

success "========================================="
success "ROLLBACK COMPLETED SUCCESSFULLY!"
success "========================================="
log "Previous commit: $CURRENT_COMMIT"
log "Current commit: $NEW_COMMIT"
log "Rollback time: $(date +'%Y-%m-%d %H:%M:%S')"
log "Backup reference: commit-before-rollback-$BACKUP_TIMESTAMP.txt"
success "========================================="

warning "To undo this rollback and return to $CURRENT_COMMIT, run:"
warning "  git reset --hard $CURRENT_COMMIT"
warning "  ./deploy.sh production"

exit 0
