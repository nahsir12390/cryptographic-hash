FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl gd zip \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

ENV COMPOSER_PROCESS_TIMEOUT=900
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/data/database.sqlite
ENV CACHE_STORE=file
ENV SESSION_DRIVER=file
ENV QUEUE_CONNECTION=sync

RUN mkdir -p /var/data \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && touch /var/data/database.sqlite \
    && composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-dev \
    && npm ci \
    && npm run build \
    && php artisan view:cache

EXPOSE 8000

CMD ["sh", "./scripts/render-start.sh"]
