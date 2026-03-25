FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js ./vite.config.js
RUN npm run build

FROM php:8.3-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    libpq \
    libzip \
    postgresql-dev \
    sqlite-dev \
    zip \
  && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_sqlite \
    zip

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache \
  && chmod +x scripts/start-render.sh

EXPOSE 10000

CMD ["sh", "scripts/start-render.sh"]
