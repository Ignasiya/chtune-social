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
Аутенфикации Breeze:
````
docker compose exec php composer require laravel/breeze --dev
docker compose exec php php artisan breeze:install // Vue with Inertia
docker compose exec php composer require spatie/laravel-sluggable
````
Установка npm, версия 20x:
````
docker compose exec php npm install
docker compose exec php npm run build
docker compose exec php npm run dev
````
