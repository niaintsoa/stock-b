FROM php:8.4-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev

# Nettoyage
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP requises par Laravel et PostgreSQL
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd intl zip

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

USER www-data
