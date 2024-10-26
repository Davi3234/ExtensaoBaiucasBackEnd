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

COPY config/configs.sh /usr/local/bin/configs.sh

RUN chmod +x /usr/local/bin/configs.sh

USER backend

EXPOSE 80

ENTRYPOINT ["/bin/sh", "/usr/local/bin/configs.sh"]