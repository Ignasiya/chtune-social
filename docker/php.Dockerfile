FROM php:8.3-fpm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install zip unzip
RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install -o -f xdebug && docker-php-ext-enable xdebug

# npm install
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs