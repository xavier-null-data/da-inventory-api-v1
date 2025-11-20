Inventario API — Laravel 11 + PostgreSQL + Docker

Descripción general
Este proyecto provee una API REST para gestión de productos, inventarios, alertas de stock y transferencias entre tiendas. Está diseñado para ambientes locales y productivos usando Docker, con buenas prácticas de arquitectura, pruebas automatizadas y documentación en Swagger.

Tecnologías principales
- Laravel 11  
- PHP 8.3  
- PostgreSQL 15  
- Docker y docker-compose  
- Repository Pattern, Service Layer, Strategy Pattern  
- OpenAPI 3.0  
- Pruebas unitarias, integración y carga (k6)  

Levantamiento del proyecto
1. Clonar el repositorio  
   git clone <repo_url>  
   cd laravel-inventory-api  

2. Construir y levantar contenedores  
   docker-compose up --build -d  
   Esto ejecuta migraciones y seeders automáticamente dentro del contenedor.

3. Probar API  
   curl http://localhost:8080/api/products  

Documentación de la API
URL local de Swagger:  
http://localhost:8080/api/docs  

Archivo OpenAPI:  
openapi.yaml en la raíz del proyecto.

Pruebas
1. Pruebas unitarias  
   docker exec -it inventory_app php artisan test --testsuite=Unit  

2. Pruebas funcionales  
   docker exec -it inventory_app php artisan test --testsuite=Feature  

3. Pruebas generales  
   docker exec -it inventory_app php artisan test  

Pruebas de carga
Archivo: load-tests/k6-inventory-test.js  

Ejecución dentro de Docker:
docker run --rm -i   --network=laravel-inventory-api-v2_default   -v "$PWD/load-tests:/tests"   grafana/k6 run /tests/k6-inventory-test.js

Decisiones de arquitectura
- Laravel permite desarrollo rápido, estructura clara y ecosistema sólido.  
- Repository Pattern para desacoplar acceso a datos y facilitar testing.  
- Service Layer para mantener controladores delgados y lógica de negocio centralizada.  
- Strategy Pattern para manejar reglas variables sin modificar código existente.  

Scripts útiles
Backup de BD:  
docker exec inventory_db pg_dump -U postgres inventory > backup.sql  

Restauración:  
docker exec -i inventory_db psql -U postgres inventory < backup.sql  

Despliegue a AWS (deploy.sh)
- Requiere acceso SSH mediante .pem  
- Instalar Docker + Docker Compose  
- Clonar repo y ejecutar docker-compose up -d  
- Configurar variables en .env  
- Ejecutar migraciones  
