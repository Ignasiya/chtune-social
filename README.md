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
````
npm, версия 20x:
````
docker compose exec node npm install
docker compose exec node npm run build
````
