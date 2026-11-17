<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends AbstractEntity implements ExportableInterface
{
    use HasFactory, JsonExportTrait;

    protected string $legacyName = '';

    protected float $legacyPrice = 0.0;

    public function __construct(
        array|string $attributes = [],
        ?string $createdAt = null,
        ?string $name = null,
        ?float $price = null
    ) {
        if (is_array($attributes)) {
            parent::__construct($attributes);

            return;
        }

        parent::__construct($attributes, $createdAt);
        $this->legacyName = $name ?? '';
        $this->legacyPrice = $price ?? 0.0;
    }

    protected $fillable = [
        'category_id',
        'model_name',
        'color',
        'storage_gb',
        'sku',
        'price_kop',
        'stock_qty',
        'reserved_qty',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'storage_gb' => 'integer',
            'price_kop' => 'integer',
            'stock_qty' => 'integer',
            'reserved_qty' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function availableQty(): int
    {
        return $this->stock_qty - $this->reserved_qty;
    }

    public function getDetails(): string
    {
        if ($this->legacyName !== '') {
            return "Товар: {$this->legacyName}, ціна: {$this->legacyPrice} грн";
        }

        return "Товар: {$this->model_name}, ціна: ".($this->price_kop / 100).' грн';
    }
}
