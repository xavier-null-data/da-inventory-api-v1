#!/usr/bin/env bash
# Backup simple de PostgreSQL usando pg_dump

set -e

DB_HOST=${DB_HOST:-localhost}
DB_PORT=${DB_PORT:-5433}
DB_NAME=${DB_NAME:-inventory_db}
DB_USER=${DB_USER:-inventory_user}
BACKUP_DIR=${BACKUP_DIR:-./backups}

mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
FILE_NAME="$BACKUP_DIR/inventory_db_$TIMESTAMP.sql"

echo "Creando backup en $FILE_NAME"

PGPASSWORD=${DB_PASSWORD:-inventory_pass} pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" "$DB_NAME" > "$FILE_NAME"

echo "Backup completado"
