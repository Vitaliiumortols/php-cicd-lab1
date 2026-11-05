# PHP CI/CD Lab

[![PHP CI Application](https://github.com/Vitaliiumortols/php-cicd-lab1/actions/workflows/php-ci.yml/badge.svg)](https://github.com/Vitaliiumortols/php-cicd-lab1/actions/workflows/php-ci.yml)

Навчальний PHP-проєкт для лабораторної роботи №1 з дисципліни «Веб-програмування».

Мета проєкту — опанувати Git і GitHub, налаштувати автоматичну
перевірку PHP-коду за допомогою GitHub Actions та організувати роботу
із задачами через GitHub Issues і Projects.

## Функціонал

- калькулятор з операціями додавання та віднімання;
- модульні тести на PHPUnit;
- автоматична перевірка форматування через Laravel Pint;
- запуск CI для Pull Request і змін у гілці `main`;
- демонстрація безпечного використання GitHub Actions Secrets.

## Лабораторна робота №3

Додано навчальну сторінку інтернет-магазину iPhone для варіанта 15:

- масиви з даними Product;
- типізовані функції та обробка винятків;
- генерація тестових цін за допомогою Faker;
- фільтрація цін анонімною функцією з `use`;
- сума натуральних чисел, останні три слова та перемішування слів.

Запуск сторінки:

```bash
composer install
php -S localhost:8000
```

Після цього потрібно відкрити `http://localhost:8000`.

## Запуск перевірок

Потрібні PHP 8.2 і Composer.

```bash
composer install
./vendor/bin/pint --test
./vendor/bin/phpunit tests/Unit
```
