FROM php:8.3-alpine AS build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --prefer-dist --optimize-autoloader && \
    rm -rf /root/.composer/cache

COPY . .

FROM php:8.3-cli-alpine

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY --from=build /var/www/html /var/www/html

RUN adduser -D backend
USER backend

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
