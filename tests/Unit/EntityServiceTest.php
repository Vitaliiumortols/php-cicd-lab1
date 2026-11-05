<?php

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../app/Services/EntityService.php';

class EntityServiceTest extends TestCase
{
    public function test_available_quantity(): void
    {
        $this->assertSame(7, availableQty(10, 3));
    }

    public function test_discount(): void
    {
        $this->assertSame(90.0, applyDiscount(100.0, 10.0));
    }

    public function test_invalid_discount(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Неприпустиме значення знижки!');

        applyDiscount(100.0, 150.0);
    }
}
