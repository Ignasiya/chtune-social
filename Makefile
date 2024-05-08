dc := docker compose
php := $(dc) exec server
node := $(dc) exec node
mysql := $(dc) exec mysql

init: down init-env image-build up composer-install npm-install prepare wait-db migrate

up:
	$(dc) up -d

down:
	$(dc) down

restart: down up

composer-install:
	$(php) composer install

app:
	$(php) bash

db:
	$(mysql) bash

wait-db:
	while ! (docker compose exec mysql bash -c 'mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE -e "SHOW TABLES;"'); do \
		sleep 10; \
	done; \

prepare:
	$(php) php artisan key:generate
	$(php) php artisan storage:link

migrate:
	$(php) php artisan migrate

npm-install:
	$(node) npm install

npm-run:
	$(node) npm run build

npm-dev:
	$(node) npm run dev

init-env:
	cp .env.example .env
	cp ./src/.env.example ./src/.env

image-build:
	$(dc) build --build-arg UID=$$(id -u) --build-arg GID=$$(id -g)
