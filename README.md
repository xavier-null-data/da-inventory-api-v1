# Inventario API — Laravel 11 + PostgreSQL + Docker

API REST para gestionar inventarios, productos, alertas de stock y transferencias entre tiendas.  
El proyecto está diseñado para ser fácil de levantar con Docker, seguir buenas prácticas de arquitectura, ser mantenible y escalable, y contar con documentación clara para ambientes locales y producción.

---

## Tecnologías principales

- Laravel 11
- PHP 8.3
- PostgreSQL 15
- Docker / Docker Compose
- Swagger / OpenAPI 3.0
- K6 (pruebas de carga)

---

## Levantamiento del proyecto con Docker

```bash
git clone <repo_url>
cd laravel-inventory-api-v2
docker-compose up --build -d
```

Esto levanta los servicios:

- `inventory_app` → Laravel + PHP 8.3  
- `inventory_db` → PostgreSQL 15  
- Network y volúmenes persistentes  

![Contenedores](https://raw.githubusercontent.com/xavier-null-data/da-inventory-api-v1/master/docs/contenedores.png)
---

## Inicialización automática mediante `entrypoint.sh`

Este proyecto incluye un script:

```
/docker/entrypoint.sh
```

El cual se ejecuta automáticamente al iniciar el contenedor de Laravel.

Este script realiza:

1. Espera a que PostgreSQL esté listo  
2. Ejecuta migraciones (`php artisan migrate`)  
3. Ejecuta seeders (`php artisan db:seed`)  
4. Carga datos iniciales de productos, inventarios y tiendas  

Por lo tanto, **al levantar Docker por primera vez ya tendrás la base de datos creada y poblada**.

---

## Resetear la base de datos (migrate:fresh + seed + force)

Si necesitas reconstruir la base de datos desde cero (QA/dev):

```bash
docker exec -it inventory_app php artisan migrate:fresh --seed --force
```

`--force` permite ejecutar comandos destructivos dentro de contenedores sin prompt interactivo.  
Esto recrea tablas, seeders y datos base.

---

## Documentación de API (Swagger / OpenAPI)

Archivo:

```
openapi.yaml
```

Puedes revisar la documentación en http://localhost:8080/docs una vez que la app este desplegada

![Swagger](https://raw.githubusercontent.com/xavier-null-data/da-inventory-api-v1/master/docs/swagger-preview.png)

---

## Colección de Postman

Archivo:

```
postman_collection.json
```

Incluye todos los endpoints del API listos para pruebas.

---

## Testing

Ejecutar toda la suite:

```bash
docker exec -it inventory_app php artisan test
```

Tests unitarios:

```bash
docker exec -it inventory_app php artisan test --testsuite=Unit
```

Tests de integración:

```bash
docker exec -it inventory_app php artisan test --testsuite=Feature
```

---

## Pruebas de carga con K6

```bash
docker run --rm -i \
  --network=laravel-inventory-api-v2_default \
  -v "$PWD/load-tests:/tests" \
  grafana/k6 run /tests/k6-inventory-test.js
```

---

# Decisiones arquitectónicas

Este proyecto utiliza **Repository Pattern**, **Service Layer** y **Strategy Pattern** para garantizar:

- escalabilidad  
- mantenibilidad  
- modularidad  
- desacoplamiento  
- reglas de negocio claras  

---

## Repository Pattern

Se utiliza para:

- separar la lógica de acceso a datos del ORM  
- centralizar consultas complejas  
- facilitar pruebas unitarias mediante mocks  
- permitir cambiar la fuente de datos sin reescribir código  

Evita que los controladores o servicios dependan directamente de Eloquent.

---

## Service Layer

Gestiona:

- reglas de negocio  
- validaciones avanzadas  
- transacciones  
- coordinación entre repositorios  
- lógica de stock, alertas y transferencias  

Esto permite controladores limpios y fáciles de mantener.

---

## Strategy Pattern

Se usa cuando existen comportamientos intercambiables:

- reglas distintas por tipo de tienda  
- diferentes estrategias de alertas  
- flujos alternativos de inventario  

Permite reemplazar comportamientos sin modificar la capa de servicio.

---

# Diagrama de arquitectura
![Diagrama Arq](https://raw.githubusercontent.com/xavier-null-data/da-inventory-api-v1/master/docs/arquitectura.png)
---

# Backups y restauración de base de datos

### Backup

```bash
docker exec -it inventory_db pg_dump -U postgres inventory_db > backup.sql
```

### Restauración

```bash
cat backup.sql | docker exec -i inventory_db psql -U postgres inventory_db
```

---

# Despliegue en AWS

---

## Prerrequisitos

### 1. Llave SSH (.pem)

### 2. Instancia EC2

Debe tener:

- Docker  
- Docker Compose  
- Git  
- Acceso a puertos 22, 80 y/o 443  
- Rol IAM o AWS CLI configurado  

### 3. Permisos para despliegue

El rol o usuario debe tener permisos para:

- S3 (put/get/list)  
- EC2 (SSH)  
- CloudWatch opcional (logs)  

---

## Variables para despliegue

El script incluye estas variables configurables:

```bash
AWS_REGION="us-east-1"
APP_NAME="inventory-api"
S3_BUCKET="$APP_NAME-deployments"
TAG=$(date +%Y%m%d%H%M)
ARTIFACT="$APP_NAME-$TAG.zip"
REMOTE_DIR="/var/www/$APP_NAME"
```

### Explicación

| Variable | Descripción |
|---------|-------------|
| AWS_REGION | Región AWS donde se encuentra tu infraestructura |
| APP_NAME | Nombre del proyecto |
| S3_BUCKET | Bucket para almacenar artefactos de despliegue |
| TAG | Versión basada en timestamp |
| ARTIFACT | ZIP final que se sube a S3 |
| REMOTE_DIR | Directorio destino en EC2 para el proyecto |

---


# Script de despliegue automático

Ubicación:

```
scripts/deploy.sh
```

Ejecución:

```bash
sh scripts/deploy.sh
```

El script realiza:

1. Empaquetado del código  
2. Subida del ZIP a S3  
3. Conexión remota vía SSH  
4. Descarga en EC2  
5. Extracción del artefacto  
6. Levantamiento de contenedores  

