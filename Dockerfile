FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    zip unzip git curl \
    libpng-dev libonig-dev libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD php artisan serve --host=0.0.0.0 --port=8000
