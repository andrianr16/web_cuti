FROM php:8.3-fpm-alpine

# Install ekstensi sistem yang dibutuhkan Laravel & DomPDF/Excel
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev

# Install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Salin source code
COPY . .

# Install dependencies PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Beri hak akses folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Salin konfigurasi Nginx & Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]