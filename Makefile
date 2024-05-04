export APP_NAME=chtune-social
export DOCKER_COMPOSE_FILE=docker-compose.yml

init: init-env up composer-install npm-install prepare migrate

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
