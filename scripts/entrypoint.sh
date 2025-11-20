#!/bin/bash

set -e

echo ">>> Starting container initialization..."

###############################################
# 1) Prepare .env for normal environment
###############################################
if [ ! -f /var/www/.env ]; then
    echo ">>> .env not found. Creating from .env.example..."
    cp /var/www/.env.example /var/www/.env
fi

# Insert DB config dynamically
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/" /var/www/.env
sed -i "s/^DB_HOST=.*/DB_HOST=${DB_HOST}/" /var/www/.env
sed -i "s/^DB_PORT=.*/DB_PORT=${DB_PORT}/" /var/www/.env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" /var/www/.env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" /var/www/.env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" /var/www/.env


###############################################
# 3) Wait for PostgreSQL (normal environment)
###############################################
echo ">>> Waiting for PostgreSQL..."
until nc -z $DB_HOST $DB_PORT; do
    sleep 1
done
echo ">>> PostgreSQL is ready."

###############################################
# 4) Generate APP_KEY for normal and testing
###############################################
if ! grep -q "APP_KEY=base64" /var/www/.env; then
    echo ">>> Generating APP_KEY for .env..."
    php /var/www/artisan key:generate
fi

echo ">>> Forcing APP_KEY generation for testing environment..."
php /var/www/artisan key:generate --env=testing --force

###############################################
# 5) Run normal migrations + seeders
###############################################
echo ">>> Running Laravel migrations..."
php /var/www/artisan migrate:fresh --seed --force

###############################################
# 6) Start Laravel server
###############################################
echo ">>> Starting Laravel server on port 8000"
exec php -S 0.0.0.0:8000 -t public
