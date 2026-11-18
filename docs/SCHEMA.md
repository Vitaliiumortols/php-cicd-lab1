# Схема бази даних iPhoneHub

| Таблиця | Призначення | Основні зв'язки |
|---|---|---|
| `users` | покупці та працівники | `hasOne(Cart)`, `hasMany(Order)` |
| `categories` | групи моделей iPhone | `hasMany(Product)` |
| `products` | конфігурації iPhone | `belongsTo(Category)`, `belongsToMany(Tag)` |
| `tags` | мітки каталогу | `belongsToMany(Product)` |
| `product_tag` | проміжна таблиця | `product_id`, `tag_id` |
| `carts` | кошики | `belongsTo(User)`, `hasMany(CartItem)` |
| `cart_items` | позиції кошика | `belongsTo(Cart)`, `belongsTo(Product)` |
| `orders` | замовлення | `belongsTo(User)`, `hasMany(OrderItem)`, `hasOne(Shipment)` |
| `order_items` | знімки проданих товарів | `belongsTo(Order)`, `belongsTo(Product)` |
| `shipments` | доставка | `belongsTo(Order)` |

Типи зв'язків:

- 1:1 — `orders` ↔ `shipments`;
- 1:M — `categories` → `products`, `users` → `orders`, `orders` → `order_items`;
- M:M — `products` ↔ `tags` через `product_tag`.
