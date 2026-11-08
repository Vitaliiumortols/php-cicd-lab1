<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\AbstractEntity;
use App\Models\OopUser;
use App\Models\Product;
use InvalidArgumentException;

class EntityFactory
{
    public function createEntity(string $type, array $data): AbstractEntity
    {
        return match ($type) {
            'product' => new Product(
                $data['id'],
                $data['createdAt'],
                $data['name'],
                $data['price']
            ),
            'user' => new OopUser(
                $data['id'],
                $data['createdAt'],
                $data['name'],
                $data['email']
            ),
            default => throw new InvalidArgumentException('Невідомий тип сутності'),
        };
    }
}
