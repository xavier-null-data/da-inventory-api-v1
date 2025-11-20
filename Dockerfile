FROM php:8.3-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Install Laravel
RUN composer create-project --prefer-dist laravel/laravel:"11.*" laravel \
    && cp -r laravel/* laravel/.* . 2>/dev/null || true \
    && rm -rf laravel

# Copy application code
COPY . .

# Copy testing environment explicitly (because .dockerignore hides it)
COPY .env.testing /var/www/.env.testing


# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# ENTRYPOINT
ENTRYPOINT ["bash", "/var/www/scripts/entrypoint.sh"]

EXPOSE 8000
