FROM php:7.4-cli

# Preparing..
RUN apt-get update && apt-get install -y curl git

# Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app