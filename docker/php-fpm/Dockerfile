FROM composer:2.7 as composer
FROM mlocati/php-extension-installer:2.2 as php-extension-installer


FROM php:8.3-fpm as fpm

# Прописываем UID и GID для пользователя, под которым будет запускаться приложение.
# Для production окружений считается безопасным выбирать ID выше 10000,
# чтобы не получить случайно id пользователя, который будет совпадать с пользователем хост системы
# и избежать эскалации привилегий.
ARG UID=10001
ARG GID=10001

WORKDIR /var/www/html/app

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/* # кеш надо чистить в том же слое, где ставятся пакеты, иначе нет смысла

# в предыдущем шаге можно почистить ненужное
# копировать из других образов тоже можно
COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions
# почитай про эту тулзу
RUN install-php-extensions pdo_mysql zip

COPY --from=composer /usr/bin/composer /usr/bin/composer

# прокидываем своего юзера, тогда не будет проблем с правами
RUN groupmod --gid=${GID} www-data \
    && usermod --uid=${UID} --gid=${GID} www-data

# от этого юзера запущен php-fpm процесс
USER www-data
