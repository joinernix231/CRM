FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
