<?php

declare(strict_types=1);
use Faker\Factory;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/app/Services/EntityService.php';
require_once __DIR__.'/app/Services/DataProcessor.php';

$faker = Factory::create();
$prices = [];

// Навчальні ціни у гривнях, а не реальні пропозиції магазину.
for ($i = 0; $i < 10; $i++) {
    $prices[] = $faker->numberBetween(20000, 70000);
}

$minPrice = 40000;
$filteredPrices = array_filter($prices, function (int $price) use ($minPrice): bool {
    return $price > $minPrice;
});

// Вхідні дані індивідуальних завдань задаємо у коді.
$numbers = [3, 5, 7, 10, 15];
$text = 'У нашому магазині можна купити новий iPhone';
$sum = sumNaturalNumbers($numbers);
$lastWords = lastThreeWords($text);
$mixedWords = shuffleWords($text);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторна №3 — магазин iPhone</title>
    <style>
        body { max-width: 900px; margin: 32px auto; padding: 0 16px; font-family: Arial, sans-serif; line-height: 1.6; color: #222; background: #f5f6f8; }
        section { margin: 20px 0; padding: 20px; background: white; border: 1px solid #ddd; border-radius: 8px; }
        h1, h2, h3 { line-height: 1.3; }
        h2 { margin-top: 0; font-size: 22px; }
        h3 { font-size: 18px; }
        .error { color: #a02020; }
        .result { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Основи PHP: структури даних, функції</h1>
    <p>Лабораторна робота №3 · Варіант 15 · Інтернет-магазин iPhone</p>
    <p>Томенко Віталій Ігорович, КН-42</p>

    <section>
        <h2>1. Індексний та асоціативний масиви</h2>
        <?php require __DIR__.'/app/Models/EntityArray.php'; ?>
    </section>

    <section>
        <h2>2. Функція з типізованими аргументами</h2>
        <p>На складі: <?= $product['stockQty'] ?> шт. У резерві: <?= $product['reservedQty'] ?> шт.</p>
        <p class="result">Доступно для продажу: <?= availableQty($product['stockQty'], $product['reservedQty']) ?> шт.</p>
    </section>

    <section>
        <h2>3. Faker та замикання з use</h2>
        <p>Згенеровано 10 випадкових цін у гривнях:</p>
        <p><?= implode(', ', $prices) ?></p>
        <p>Ціни, більші за <?= $minPrice ?> грн:</p>
        <p class="result"><?= $filteredPrices ? implode(', ', $filteredPrices) : 'Немає цін за цим критерієм.' ?></p>
        <p>Після оновлення сторінки ціни генеруються заново.</p>
    </section>

    <section>
        <h2>4. Обробка винятку</h2>
        <?php $price = $product['priceKop'] / 100; ?>
        <p>Початкова ціна: <?= number_format($price, 2, ',', ' ') ?> грн.</p>
        <p>Ціна зі знижкою 10%: <?= number_format(applyDiscount($price, 10.0), 2, ',', ' ') ?> грн.</p>
        <p>Спроба застосувати некоректну знижку 150%:</p>
        <?php
        try {
            applyDiscount($price, 150.0);
        } catch (Exception $e) {
            echo '<p class="error">'.htmlspecialchars($e->getMessage()).'</p>';
        } finally {
            echo '<p>Спробу розрахунку завершено.</p>';
        }
?>
    </section>

    <section>
        <h2>5. Індивідуальні завдання — варіант 15</h2>
        <h3>Таблиця 3.2, №6: сума натуральних чисел</h3>
        <p>Числа: <?= implode(', ', $numbers) ?></p>
        <p class="result">Сума: <?= $sum ?></p>

        <h3>Таблиця 3.3, №3: останні три слова</h3>
        <p>Початковий рядок: <?= htmlspecialchars($text) ?></p>
        <p class="result">Результат: <?= htmlspecialchars($lastWords) ?></p>

        <h3>Таблиця 3.4, №3: перемішування слів</h3>
        <p>Початковий рядок: <?= htmlspecialchars($text) ?></p>
        <p class="result">Результат: <?= htmlspecialchars($mixedWords) ?></p>
    </section>
</body>
</html>
