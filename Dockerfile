FROM php:7.1-cli

# Preparing..
RUN apt-get update && apt-get install -y curl git

# Setup extensions

# xdebug
RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug

# ZIP
RUN apt-get install -y \
        zlib1g-dev \
    && docker-php-ext-install zip

# Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app