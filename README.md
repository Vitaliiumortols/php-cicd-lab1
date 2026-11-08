# PHP CI/CD Lab

[![PHP CI Application](https://github.com/Vitaliiumortols/php-cicd-lab1/actions/workflows/php-ci.yml/badge.svg)](https://github.com/Vitaliiumortols/php-cicd-lab1/actions/workflows/php-ci.yml)

Навчальний репозиторій з лабораторних робіт із вебпрограмування.

## Лабораторна робота №5

Предметна область — інтернет-магазин iPhone. У проєкті використано Laravel 12 та PHP 8.2.

Реалізовано:

- маршрутизацію Laravel;
- контролери `ProductController` і `CatalogController`;
- перевірку вхідних даних товару;
- middleware `CheckStoreClient`;
- JSON-відповіді зі статусами 200, 201, 403, 404 і 422;
- функціональні тести;
- автоматичну перевірку Pint і PHPUnit у GitHub Actions.

## Основні маршрути

| Метод | Маршрут | Призначення |
|---|---|---|
| GET | `/` | Стартова сторінка Laravel |
| GET | `/health` | Перевірка роботи застосунку |
| GET | `/system-info` | Інформація про систему |
| GET | `/products` | Список товарів |
| GET | `/products/{id}` | Один товар |
| POST | `/products` | Створення товару |

Для POST-запиту потрібно передати заголовок `X-Store-Client`. Якщо заголовка немає, middleware повертає статус 403.

## Запуск

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Після запуску застосунок доступний за адресою `http://127.0.0.1:8000`.

## Перевірка

```bash
./vendor/bin/pint --test
php artisan test
php artisan route:list --except-vendor
```

Автор: Томенко Віталій Ігорович, КН-42.
