FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        zip \
        libzip-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libmcrypt-dev \
        libicu-dev \
        libxml2-dev \
        libsqlite3-dev \
        libsqlite3-0 \
        libbz2-dev \
        mariadb-client \
        && docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage
