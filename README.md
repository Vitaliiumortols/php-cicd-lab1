# Лабораторна робота №6 — iPhoneHub

Варіант 18. Виконав: Томенко Віталій Ігорович, КН-42.

Тема: робота з базою даних інтернет-магазину iPhone у Laravel за допомогою Eloquent ORM.

## Що реалізовано

- SQLite та міграції для всіх сутностей з лабораторної №2;
- зв'язки 1:1, 1:M і M:M;
- Eloquent-моделі, `$fillable`, `$casts` і методи зв'язків;
- фабрики Faker і `DatabaseSeeder`;
- Eager Loading через `with()` для запобігання N+1;
- створення та оновлення товарів із `sync()` тегів;
- маршрути каталогу, категорій, тегів, користувачів, кошиків, замовлень і доставок;
- Feature-тести з `RefreshDatabase`;
- GitHub Actions для міграцій, Pint і PHPUnit.

## Перший запуск

```bash
cd /Users/vitalijtomenko/univer4/php/lab6
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Відкрити у браузері:

- `http://127.0.0.1:8000/` — інформація про лабораторну;
- `http://127.0.0.1:8000/catalog` — каталог разом із категоріями та тегами;
- `http://127.0.0.1:8000/categories` — категорії;
- `http://127.0.0.1:8000/orders` — замовлення разом із позиціями та доставкою.

## Перевірка

```bash
php artisan migrate:status
./vendor/bin/pint --test
php artisan test
```

Команди для Tinker наведені у [docs/TINKER_COMMANDS.md](docs/TINKER_COMMANDS.md), опис структури — у [docs/SCHEMA.md](docs/SCHEMA.md).
