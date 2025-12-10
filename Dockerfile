# Base PHP Apache image (PHP 8.1)
FROM php:8.1-apache

# Install build deps for PHP extensions (GD, ZIP, PostgreSQL, etc.) and Node.js
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libpng-dev \
       libjpeg62-turbo-dev \
       libfreetype6-dev \
       libwebp-dev \
       libzip-dev \
       libonig-dev \
       zlib1g-dev \
       pkg-config \
       zip \
       git \
       libpq-dev \
       curl \
       ca-certificates \
       openssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j"$(nproc)" gd mysqli mbstring zip pdo pdo_pgsql pgsql sockets \
    && update-ca-certificates \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules and allow .htaccess rewrites
RUN a2enmod rewrite headers expires \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && sed -ri 's/^\s*AllowOverride\s+None/    AllowOverride All/g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy application into container
COPY . /var/www/html

# JavaScript assets are built on the host prior to image build
# to work seamlessly with bind mounts in docker-compose.

# Ensure proper permissions for writable directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 application/cache application/logs || true

# Expose Apache port
EXPOSE 80

# Default command
CMD ["apache2-foreground"]