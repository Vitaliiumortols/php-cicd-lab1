<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractEntity extends Model
{
    protected string $legacyId = '';

    protected string $createdAt = '';

    public function __construct(array|string $attributes = [], ?string $createdAt = null)
    {
        if (is_array($attributes)) {
            parent::__construct($attributes);

            return;
        }

        parent::__construct();
        $this->legacyId = $attributes;
        $this->createdAt = $createdAt ?? '';
    }

    abstract public function getDetails(): string;
}
