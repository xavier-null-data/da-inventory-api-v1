# Backups de base de datos

Este directorio contiene un script simple para generar backups de la base de datos PostgreSQL.

## Uso rápido

```bash
chmod +x scripts/backups/manual_pg_dump.sh
DB_HOST=localhost DB_PORT=5433 DB_NAME=inventory_db DB_USER=inventory_user DB_PASSWORD=inventory_pass ./scripts/backups/manual_pg_dump.sh
```

## Programar con cron

Ejemplo para correr todos los días a las 2am:

```cron
0 2 * * * /ruta/al/proyecto/scripts/backups/manual_pg_dump.sh >> /var/log/inventory_backups.log 2>&1
```
