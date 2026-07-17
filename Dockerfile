ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libcurl4-openssl-dev \
        libzip-dev \
    && docker-php-ext-install curl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN git config --global --add safe.directory /app \
    && git config --global --add safe.directory '*'

WORKDIR /app
