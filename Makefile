dc := docker compose
php := $(dc) exec server
node := $(dc) exec node
mysql := $(dc) exec mysql

init: init-env down image-build up composer-install npm-install prepare wait-db migrate seed
refresh: clear init

up:
	$(dc) up -d --remove-orphans

down:
	$(dc) down

clear:
	$(dc) down -v --remove-orphans
	rm -rf src/node_modules/
	rm -rf src/vendor/
	rm -rf src/public/vendor/
	rm -rf src/public/hot
	rm -rf src/public/storage
	rm -rf src/bootstrap/cache/*
	rm -rf src/storage/app/public/*
	rm -rf src/storage/framework/cache/data/*
	rm -rf src/storage/framework/sessions/*
	rm -rf src/storage/framework/testing/*
	rm -rf src/storage/framework/views/*
	rm -rf src/storage/logs/*
	rm -rf .env src/.env

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

init-env: .env src/.env

.env:
	cp .env.example .env

src/.env:
	cp ./src/.env.example ./src/.env

image-build:
	$(dc) build --build-arg UID=$$(id -u) --build-arg GID=$$(id -g)

seed:
	$(php) php artisan db:seed --class=DatabaseSeeder
