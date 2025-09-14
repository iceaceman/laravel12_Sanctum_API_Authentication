FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    freetype-dev \
    oniguruma-dev \
    build-base \
    curl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl bcmath opcache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html
# RUN chown -R www-data:www-data /var/www/html/storage bootstrap/cache
# RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]

