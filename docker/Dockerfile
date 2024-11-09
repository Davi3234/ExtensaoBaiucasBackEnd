FROM php:8.3-alpine AS build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --prefer-dist --optimize-autoloader && \
    rm -rf /root/.composer/cache

COPY . .

FROM php:8.3.12-apache AS dev

RUN apt-get update && \
    apt-get install -y --no-install-recommends libpq-dev git && \
    docker-php-ext-install pdo pdo_pgsql pgsql && \
    rm -rf /var/cache/apk/*

RUN a2enmod rewrite && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

COPY ./docker/xdebug.ini /usr/local/etc/php/conf.d/
COPY ./docker/httpd.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html/
COPY --from=build /var/www/html /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
