#!/bin/bash
set -e

# - validar el proyecto antes de empaquetarlo
# - sube el artefacto a S3
# - ejecuta el despliegue remoto en EC2
# - ejecuta migraciones genera caches y valida healthcheck
# - cambia symlink current si todo ok


for arg in "$@"
do
    case $arg in
        --env=*)
        ENV="${arg#*=}"
        shift
        ;;
        --tag=*)
        TAG="${arg#*=}"
        shift
        ;;
    esac
done

# verificar ambiente a desplegar
if [[ -z "$ENV" ]]; then
  echo "ERROR: Debes indicar --env=dev|staging|prod"
  exit 1
fi

if [[ -z "$TAG" ]]; then
  echo "ERROR: Debes indicar --tag=VERSION (por ejemplo v1.0.0)"
  exit 1
fi

echo "Iniciando despliegue del tag $TAG en entorno $ENV"


# configuracion para aws
AWS_REGION="us-east-1"
APP_NAME="inventory-api"
S3_BUCKET="$APP_NAME-deployments"
ARTIFACT="$APP_NAME-$TAG.zip"
REMOTE_DIR="/var/www/$APP_NAME"

echo "Usando región AWS: $AWS_REGION"
echo "Bucket de despliegos: $S3_BUCKET"


echo "Ejecutando validaciones antes de empaquetar..."

composer install --no-interaction --prefer-dist
composer dump-autoload
php artisan config:clear

# validacion de calidad de codigo (eslint, mejoras practicas, etc)
echo "Ejecutando PHPStan para validar calidad del código..."
vendor/bin/phpstan analyse --memory-limit=1G

echo "Validaciones locales completadas"


# empaquetado de la app
echo "Empaquetando la aplicación..."

rm -f $ARTIFACT

zip -rq $ARTIFACT . \
    -x "./tests/*" \
    -x "./storage/logs/*" \
    -x "./.git/*" \
    -x "vendor/bin/phpunit"

echo "Artefacto generado: $ARTIFACT"


# upload a s3
echo "Subiendo el artefacto a S3..."

aws s3 cp "$ARTIFACT" "s3://$S3_BUCKET/$ENV/$TAG/$ARTIFACT" \
    --region "$AWS_REGION"

echo "Artefacto subido a S3 correctamente"


# despliegue en ec3
echo "Iniciando despliegue remoto en EC2..."

# los hosts se leen desde parameter Store (lista separada por espacios)
HOSTS=$(aws ssm get-parameter \
      --name "/$APP_NAME/$ENV/hosts" \
      --query "Parameter.Value" \
      --output text)

for host in $HOSTS
do
  echo "Desplegando en servidor: $host"

  ssh -o StrictHostKeyChecking=no ec2-user@$host << EOF
    set -e

    echo "Descargando artefacto desde S3..."
    aws s3 cp s3://$APP_NAME-deployments/$ENV/$TAG/$ARTIFACT /tmp/$ARTIFACT

    echo "Descomprimiendo release..."
    rm -rf $REMOTE_DIR/releases/$TAG
    mkdir -p $REMOTE_DIR/releases/$TAG
    unzip -q /tmp/$ARTIFACT -d $REMOTE_DIR/releases/$TAG

    echo "Instalando dependencias..."
    cd $REMOTE_DIR/releases/$TAG
    composer install --no-dev --optimize-autoloader --no-interaction

    echo "Aplicando migraciones..."
    php artisan migrate --force

    echo "Generando cachés..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Validando healthcheck..."
    curl -f http://localhost/up || (echo "Healthcheck falló" && exit 1)

    echo "Activando nueva versión..."
    ln -sfn $REMOTE_DIR/releases/$TAG $REMOTE_DIR/current
EOF

  if [[ $? != 0 ]]; then
    echo "Error durante el despliegue en $host. El proceso se detiene."
    exit 1
  fi
done

echo "Despliegue ok"
