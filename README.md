SPA приложение социальная сеть "Chtune" на стэке Laravel и Vue + Tailwind CSS

Запуск проекта:
````
docker compose up --build
````
Установка composer:
````
docker compose exec php composer install
````
Для миграции:
````
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate
docker compose exec php php artisan storage:link
````
npm, версия 20x:
````
docker compose exec php npm install
docker compose exec php npm run build
````
