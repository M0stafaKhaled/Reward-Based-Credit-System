FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache bash git curl libpng-dev libjpeg-turbo-dev libfreetype-dev oniguruma-dev icu-dev zip unzip

# Install PHP extensions including Redis
RUN apk add --no-cache $PHPIZE_DEPS
RUN pecl install redis
RUN docker-php-ext-enable redis
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application code
COPY . .

# Copy .env.docker to .env for Docker environments
COPY .env.docker .env

# Generate application key
RUN php artisan key:generate

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear and cache config for better performance
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
