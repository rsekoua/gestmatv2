#!/bin/bash

# ==================================================
# GESTMAT V2 - AUTOMATED DEPLOYMENT SCRIPT
# ==================================================
# Usage: ./deploy.sh [environment]
# Environments: production, staging
# Example: ./deploy.sh production
# ==================================================

set -e  # Exit on error
set -u  # Exit on undefined variable

# ==================================================
# CONFIGURATION
# ==================================================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="/var/www/gestmatv2"
ENVIRONMENT="${1:-production}"
BRANCH="${2:-main}"
BACKUP_DIR="/var/backups/gestmatv2"
LOG_FILE="/var/log/gestmatv2-deploy.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

# ==================================================
# PRE-DEPLOYMENT CHECKS
# ==================================================

log "Starting deployment for environment: $ENVIRONMENT"
log "Target branch: $BRANCH"

# Check if running as correct user
if [ "$EUID" -eq 0 ]; then
    error "Do not run this script as root. Run as www-data or deployment user."
fi

# Check if project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    error "Project directory does not exist: $PROJECT_DIR"
fi

# Check if environment is valid
if [[ ! "$ENVIRONMENT" =~ ^(production|staging)$ ]]; then
    error "Invalid environment. Use 'production' or 'staging'"
fi

# ==================================================
# STEP 1: ENABLE MAINTENANCE MODE
# ==================================================

log "Enabling maintenance mode..."
cd "$PROJECT_DIR" || error "Failed to change to project directory"

php artisan down --render="errors::503" --retry=60 || warning "Maintenance mode failed (maybe already down)"

# ==================================================
# STEP 2: BACKUP DATABASE
# ==================================================

log "Creating database backup..."
mkdir -p "$BACKUP_DIR"
BACKUP_FILE="$BACKUP_DIR/backup-$(date +'%Y%m%d-%H%M%S').sql"

php artisan backup:run --only-db || error "Database backup failed"
success "Database backed up successfully"

# ==================================================
# STEP 3: PULL LATEST CODE
# ==================================================

log "Fetching latest code from Git..."
git fetch origin || error "Git fetch failed"

# Store current commit for rollback
PREVIOUS_COMMIT=$(git rev-parse HEAD)
log "Current commit: $PREVIOUS_COMMIT"

# Checkout target branch
git checkout "$BRANCH" || error "Failed to checkout branch: $BRANCH"
git pull origin "$BRANCH" || error "Git pull failed"

NEW_COMMIT=$(git rev-parse HEAD)
log "New commit: $NEW_COMMIT"

if [ "$PREVIOUS_COMMIT" == "$NEW_COMMIT" ]; then
    warning "No new commits to deploy"
fi

# ==================================================
# STEP 4: INSTALL DEPENDENCIES
# ==================================================

log "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction || error "Composer install failed"

log "Installing NPM dependencies..."
npm ci --production || error "NPM install failed"

# ==================================================
# STEP 5: BUILD FRONTEND ASSETS
# ==================================================

log "Building frontend assets..."
npm run build || error "Asset build failed"

# ==================================================
# STEP 6: RUN DATABASE MIGRATIONS
# ==================================================

log "Running database migrations..."
php artisan migrate --force || error "Migration failed"

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
# STEP 8: OPTIMIZE APPLICATION
# ==================================================

log "Optimizing application..."
php artisan optimize

# ==================================================
# STEP 9: STORAGE LINK
# ==================================================

log "Linking storage..."
php artisan storage:link || warning "Storage link already exists"

# ==================================================
# STEP 10: SET PERMISSIONS
# ==================================================

log "Setting file permissions..."
find "$PROJECT_DIR/storage" -type f -exec chmod 664 {} \;
find "$PROJECT_DIR/storage" -type d -exec chmod 775 {} \;
find "$PROJECT_DIR/bootstrap/cache" -type f -exec chmod 664 {} \;
find "$PROJECT_DIR/bootstrap/cache" -type d -exec chmod 775 {} \;

# ==================================================
# STEP 11: RESTART SERVICES
# ==================================================

log "Restarting queue workers..."
php artisan queue:restart

log "Restarting Supervisor processes..."
sudo supervisorctl restart gestmat:* || warning "Supervisor restart failed"

log "Restarting PHP-FPM..."
sudo systemctl restart php8.4-fpm || warning "PHP-FPM restart failed"

# Optional: Restart Nginx
# log "Restarting Nginx..."
# sudo systemctl reload nginx

# ==================================================
# STEP 12: HEALTH CHECK
# ==================================================

log "Running health checks..."

# Check if app is responding
APP_URL=$(php artisan tinker --execute="echo config('app.url');")
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/health" || echo "000")

if [ "$HTTP_CODE" != "200" ]; then
    error "Health check failed. HTTP code: $HTTP_CODE"
fi

success "Health check passed"

# ==================================================
# STEP 13: DISABLE MAINTENANCE MODE
# ==================================================

log "Disabling maintenance mode..."
php artisan up

# ==================================================
# STEP 14: POST-DEPLOYMENT TASKS
# ==================================================

log "Running post-deployment cleanup..."

# Clean old backups (keep last 10)
log "Cleaning old backups..."
cd "$BACKUP_DIR" || warning "Backup directory not found"
ls -t | tail -n +11 | xargs -r rm -- || warning "Backup cleanup failed"

# ==================================================
# DEPLOYMENT SUMMARY
# ==================================================

success "========================================="
success "DEPLOYMENT COMPLETED SUCCESSFULLY!"
success "========================================="
log "Environment: $ENVIRONMENT"
log "Branch: $BRANCH"
log "Previous commit: $PREVIOUS_COMMIT"
log "New commit: $NEW_COMMIT"
log "Deployment time: $(date +'%Y-%m-%d %H:%M:%S')"
success "========================================="

# ==================================================
# NOTIFICATIONS (Optional)
# ==================================================

# Send Slack notification
# curl -X POST -H 'Content-type: application/json' \
#   --data '{"text":"GestMat v2 deployment completed successfully!"}' \
#   YOUR_SLACK_WEBHOOK_URL

# Send email notification
# echo "GestMat v2 deployment completed successfully at $(date)" | \
#   mail -s "GestMat Deployment Success" admin@votre-domaine.com

exit 0
