<?php

declare(strict_types=1);

// Операція availableQty з UML: передаємо залишок і резерв як аргументи.
function availableQty(int $stockQty, int $reservedQty): int
{
    return $stockQty - $reservedQty;
}

function applyDiscount(float $price, float $discount): float
{
    if ($discount < 0 || $discount > 100) {
        throw new Exception('Неприпустиме значення знижки!');
    }

    return $price - $price * $discount / 100;
}
