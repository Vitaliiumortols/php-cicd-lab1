# Перевірка вимог лабораторної роботи №7

| Вимога | Реалізація |
|---|---|
| Базовий Blade layout і Bootstrap 5 | `resources/views/layouts/app.blade.php` |
| Navbar з `route()` | `resources/views/layouts/app.blade.php` |
| Flash success/error | `resources/views/layouts/app.blade.php` |
| Blade-компоненти форм | `resources/views/components/form/` |
| Головна сторінка | `resources/views/home.blade.php` |
| Resource routes | `routes/web.php` |
| Index, show, create, edit, delete для всіх сутностей | `resources/views/{catalog,categories,tags,users,carts,orders,shipments}` |
| Eager Loading зв'язків | ресурсні контролери в `app/Http/Controllers/` |
| Зв'язок 1:M Product → Category | `Product::category()` і поле `category_id` у формі |
| Зв'язок M:M Product ↔ Tag | `Product::tags()`, `Tag::products()` і `sync()` |
| CSRF | директива `@csrf` у формах |
| Method Spoofing | `@method('PUT')` та `@method('DELETE')` |
| Валідація, `old()`, `@error` | контролери та Blade-компоненти форм |
| Пагінація | index-методи контролерів і `links()` у представленнях |
| Feature tests | `tests/Feature/` |
| CRUD-тести всіх ресурсів | `tests/Feature/ResourceCrudTest.php` |
| Перевірка CSRF і Method Spoofing у HTML | `CatalogViewTest::test_forms_render_csrf_tokens_and_method_spoofing_fields()` |
| Схема фактичної архітектури | `docs/diagrams/lab7-actual-architecture.drawio` |

GitHub-етапи: Issue №19 з міткою `enhancement`, гілка `feature/blade-views-forms`, Pull Request до `main` та GitHub Actions pipeline.
