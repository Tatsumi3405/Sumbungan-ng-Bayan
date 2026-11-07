# syntax=docker/dockerfile:1
# Sumbungan ng Bayan — Laravel on Render (Docker runtime, Apache + PHP 8.3)
FROM php:8.3-apache

ARG LARAVEL_CONSTRAINT="^12.0"

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip intl bcmath opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_INTERACTION=1

# Apache: serve /public, allow .htaccess rewrites
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/apache-app.conf /etc/apache2/conf-available/zz-app.conf
RUN a2enconf zz-app
# Send every non-file URL to Laravel's front controller. Done here (not only in
# public/.htaccess) because GitHub's web uploader drops dot-files.
RUN printf '<Directory /var/www/html/public>\n    FallbackResource /index.php\n</Directory>\n' > /etc/apache2/conf-available/zz-laravel-routes.conf \
 && a2enconf zz-laravel-routes

# Project files. If the repo already contains a full Laravel app (artisan
# present) it is used as-is; otherwise a fresh Laravel skeleton is created
# first and this repo's files are laid on top of it.
WORKDIR /var/www/html
COPY . /tmp/src
RUN set -eux; \
    if [ ! -f /tmp/src/artisan ]; then \
        composer create-project "laravel/laravel:${LARAVEL_CONSTRAINT}" /var/www/html --no-scripts --prefer-dist; \
    fi; \
    cp -a /tmp/src/. /var/www/html/; \
    rm -rf /tmp/src /var/www/html/.env /var/www/html/docker/.keep; \
    rm -f bootstrap/cache/*.php; \
    mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache; \
    composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist; \
    php artisan package:discover --ansi; \
    chown -R www-data:www-data storage bootstrap/cache; \
    chmod -R ug+rwX storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i "s/\r$//" /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["entrypoint.sh"]
