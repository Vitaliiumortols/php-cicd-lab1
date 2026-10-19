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

## Запуск перевірок

Потрібні PHP 8.2 і Composer.

```bash
composer install
./vendor/bin/pint --test
./vendor/bin/phpunit tests/Unit
```

## Проєктування системи

Матеріали лабораторної роботи №2 для інтернет-магазину настільних
ігор **Boardly** розміщено у файлі
[`docs/architecture.md`](docs/architecture.md). Документ містить аналіз
предметної галузі, три форми Use Case та UML-діаграми варіантів
використання і класів разом із вихідним кодом PlantUML.
