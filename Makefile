init: init-env up composer-install npm-install prepare wait-db migrate

up:
	docker compose up -d

down:
	docker compose down

restart: down up

composer-install:
	docker compose exec server composer install

app:
	docker-compose exec server bash

db:
	docker-compose exec mysql bash

wait-db:
	while ! (docker compose exec mysql bash -c 'mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE -e "SHOW TABLES;"'); do \
		sleep 10; \
	done; \

prepare:
	docker compose exec server php artisan key:generate
	docker compose exec server php artisan storage:link

migrate:
	docker compose exec server php artisan migrate

npm-install:
	docker compose exec server npm install

npm-run:
	docker compose exec server npm run build

npm-dev:
	docker compose exec server npm run dev

init-env:
	cp .env.example .env
	cp ./src/.env.example ./src/.env
