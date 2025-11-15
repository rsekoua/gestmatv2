#!/bin/bash

# ==================================================
# GESTMAT V2 - MANUAL BACKUP SCRIPT
# ==================================================
# Usage: ./backup.sh [type]
# Types: full, db, files
# Example: ./backup.sh full
# ==================================================

set -e

# ==================================================
# CONFIGURATION
# ==================================================
PROJECT_DIR="/var/www/gestmatv2"
BACKUP_DIR="/var/backups/gestmatv2"
BACKUP_TYPE="${1:-full}"
TIMESTAMP=$(date +'%Y%m%d-%H%M%S')

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

# ==================================================
# FUNCTIONS
# ==================================================

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# ==================================================
# CREATE BACKUP DIRECTORY
# ==================================================

mkdir -p "$BACKUP_DIR"
cd "$PROJECT_DIR" || exit 1

log "Starting backup: $BACKUP_TYPE"

# ==================================================
# DATABASE BACKUP
# ==================================================

if [[ "$BACKUP_TYPE" == "db" || "$BACKUP_TYPE" == "full" ]]; then
    log "Backing up database..."
    php artisan backup:run --only-db
    success "Database backup completed"
fi

# ==================================================
# FILES BACKUP
# ==================================================

if [[ "$BACKUP_TYPE" == "files" || "$BACKUP_TYPE" == "full" ]]; then
    log "Backing up files..."

    BACKUP_FILE="$BACKUP_DIR/files-$TIMESTAMP.tar.gz"

    tar -czf "$BACKUP_FILE" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        storage/ \
        public/storage \
        .env

    success "Files backup completed: $BACKUP_FILE"
fi

# ==================================================
# CLEANUP OLD BACKUPS
# ==================================================

log "Cleaning old backups (keeping last 30)..."
cd "$BACKUP_DIR" || exit 1
ls -t | tail -n +31 | xargs -r rm --

success "Backup process completed!"
log "Backup location: $BACKUP_DIR"

exit 0
