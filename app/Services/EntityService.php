<?php

declare(strict_types=1);

namespace App\Services {
    use App\Interfaces\LoggerInterface;
    use App\Models\AbstractEntity;

    class EntityService
    {
        public function __construct(private LoggerInterface $logger) {}

        public function showDetails(AbstractEntity $entity): void
        {
            $this->logger->log('Обробка сутності');
            echo "\n".$entity->getDetails();
        }
    }
}

namespace {
    // Функції з лабораторної №3 залишені для сумісності з попередніми тестами.
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
}
