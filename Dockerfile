FROM php:8.2-apache

# PHP extensions for PostgreSQL
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Apache: serve from /var/www/html (default)
WORKDIR /var/www/html

# Copy app
COPY public/ /var/www/html/
COPY src/ /var/www/html/src/

# Small hardening / convenience
RUN a2enmod headers

# Healthcheck is done in compose (curl), keep container simple
EXPOSE 80
