<?php

declare(strict_types=1);

$models = ['iPhone 15', 'iPhone 13', 'iPhone 16', 'iPhone 12', 'iPhone 14'];
sort($models);

// Назви ключів відповідають атрибутам Product з лабораторної №2.
$product = [
    'id' => 1,
    'categoryId' => 1,
    'modelName' => 'iPhone 15',
    'color' => 'Black',
    'storageGb' => 128,
    'sku' => 'IP15-128-BLK',
    'priceKop' => 2999900,
    'stockQty' => 10,
    'reservedQty' => 3,
    'active' => true,
];
?>

<h3>Моделі за алфавітом</h3>
<ul>
    <?php foreach ($models as $model) { ?>
        <li><?= htmlspecialchars($model) ?></li>
    <?php } ?>
</ul>

<h3>Дані одного товару — Product</h3>
<ul>
    <?php foreach ($product as $key => $value) { ?>
        <li>
            <strong><?= htmlspecialchars($key) ?>:</strong>
            <?= is_bool($value) ? ($value ? 'true' : 'false') : htmlspecialchars((string) $value) ?>
        </li>
    <?php } ?>
</ul>
<p>priceKop — ціна в копійках, stockQty — залишок, reservedQty — зарезервована кількість.</p>
