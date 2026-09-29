FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        pkg-config \
        libonig-dev \
        libsqlite3-dev \
        libzip-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

CMD ["php", "docker/start.php"]
