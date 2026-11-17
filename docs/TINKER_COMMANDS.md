# Команди для перевірки Eloquent у Tinker

Запуск:

```bash
php artisan tinker
```

## `new` і `save()`

```php
$category = new App\Models\Category(['name' => 'Аксесуари', 'slug' => 'accessories']);
$category->id;
$category->save();
$category->id;
```

До `save()` значення `id` дорівнює `null`, після збереження SQLite присвоює номер.

## Зв'язок 1:M

```php
$product = $category->products()->create([
    'model_name' => 'iPhone 16',
    'color' => 'Black',
    'storage_gb' => 128,
    'sku' => 'IPH-16-128-BLK',
    'price_kop' => 3999900,
    'stock_qty' => 10,
    'reserved_qty' => 0,
    'active' => true,
]);
$category->load('products')->products;
$product->load('category')->category;
```

## Зв'язок M:M

```php
$sale = App\Models\Tag::create(['name' => 'Розпродаж', 'slug' => 'sale']);
$top = App\Models\Tag::create(['name' => 'Вибір покупців', 'slug' => 'buyers-choice']);
$product->tags()->attach($sale->id);
$product->tags()->create(['name' => 'Ексклюзив', 'slug' => 'exclusive']);
$product->tags()->sync([$sale->id, $top->id]);
$product->load('tags')->tags;
```

Вихід із консолі: `exit`.
