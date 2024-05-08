FROM node:latest as npm

# Прописываем UID и GID для пользователя, под которым будет запускаться приложение.
# Для production окружений считается безопасным выбирать ID выше 10000,
# чтобы не получить случайно id пользователя, который будет совпадать с пользователем хост системы
# и избежать эскалации привилегий.
ARG UID=10001
ARG GID=10001

RUN userdel -r node

WORKDIR /var/www/html/app

# прокидываем своего юзера, тогда не будет проблем с правами
RUN groupmod --gid=${GID} www-data \
    && usermod --uid=${UID} --gid=${GID} www-data

USER www-data