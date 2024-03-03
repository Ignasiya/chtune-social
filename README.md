SPA приложение на Laravel и Vue

Запуск проекта:
````
docker compose up --build
````
Установка composer:
````
docker compose run php composer install
````
Для миграции:
````
docker compose exec php php artisan migrate
````
Аутенфикации Breeze(для себя):
````
docker compose exec php composer require laravel/breeze --dev
docker compose exec php php artisan breeze:install // Vue with Inertia
````
Установка npm, версия 20x:
````
docker compose exec php npm install
docker compose exec php npm run build
````
