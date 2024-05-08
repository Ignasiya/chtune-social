FROM debian:bullseye-slim as npm

# Прописываем UID и GID для пользователя, под которым будет запускаться приложение.
# Для production окружений считается безопасным выбирать ID выше 10000,
# чтобы не получить случайно id пользователя, который будет совпадать с пользователем хост системы
# и избежать эскалации привилегий.
ARG UID=10001
ARG GID=10001

# Обновление репозиториев и установка необходимых пакетов
RUN apt-get update && apt-get install -y curl gnupg
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash -
RUN apt-get install -y nodejs

WORKDIR /var/www/html/app

# прокидываем своего юзера, тогда не будет проблем с правами
RUN groupmod --gid=${GID} www-data \
    && usermod --uid=${UID} --gid=${GID} www-data

RUN chown -R ${UID}:${GID} /var/www

USER www-data