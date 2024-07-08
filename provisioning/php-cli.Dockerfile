FROM php:8.2-cli

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev

RUN docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
