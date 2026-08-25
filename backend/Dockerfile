FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --no-scripts

COPY . .
RUN composer dump-autoload --optimize

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]