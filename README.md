***SPA приложение социальная сеть "Chtune" на стэке Laravel и Vue + Tailwind CSS***

==================================================

## Требований к функциональности проекта:

- *Регистрация и аутентификация пользователей*

- *Профили пользователей*

- *Создание и редактирование постов*

- *Комментирование постов*

- *Взаимодействие пользователей*

- *Создание группы*

- *Профили группы*

- *Роли администратора и пользователей в группе*

- *Редактирование группы*

- *Глобальный поиск*

Для получения подробного списка функциональных требований, пожалуйста, обратитесь к [
Документация по функциональным требованиям](docs/functional-requirements.md).

## Запуск проекта:
````
docker compose up
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
