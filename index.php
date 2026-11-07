<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\EntityFactory;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Tasks\DepartmentHead;
use App\Tasks\EnterpriseManager;
use App\Tasks\Group;
use App\Tasks\SquareMatrix;
use App\Tasks\Student;
use App\Tasks\WorkshopManager;

require_once __DIR__.'/vendor/autoload.php';

ob_start();
$database1 = Database::getInstance();
$databaseMessage = ob_get_clean();
$database2 = Database::getInstance();
$singletonResult = $database1 === $database2
    ? 'Singleton працює: використано один екземпляр'
    : 'Помилка Singleton';

$factory = new EntityFactory;
$product = $factory->createEntity('product', [
    'id' => 'p1',
    'createdAt' => '2026-11-07',
    'name' => 'iPhone 16',
    'price' => 42999.0,
]);
$user = $factory->createEntity('user', [
    'id' => 'u1',
    'createdAt' => '2026-11-07',
    'name' => 'Віталій',
    'email' => 'vitalii@example.com',
]);

ob_start();
$service = new EntityService(new ConsoleLogger);
$service->showDetails($product);
$serviceResult = ob_get_clean();

$group = new Group(42, 'КН-42');
$group->addStudent(new Student(1, 'Віталій', 'Томенко', '2005-01-10'));
$group->addStudent(new Student(2, 'Олена', 'Коваль', '2005-03-15'));
$foundStudent = $group->searchStudent('Томенко');
$group->removeStudent(2);

$workers = [
    new WorkshopManager('Керівник цеху', 20000),
    new DepartmentHead('Начальник відділу', 20000),
    new EnterpriseManager('Керівник підприємства', 20000),
];

$matrix = new SquareMatrix([
    [1, 2, 3],
    [0, 4, 5],
    [1, 0, 6],
]);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторна №4 — ООП в PHP</title>
    <style>
        body { max-width: 900px; margin: 32px auto; padding: 0 16px; font-family: Arial, sans-serif; line-height: 1.6; color: #222; background: #f5f6f8; }
        section { margin: 20px 0; padding: 20px; background: white; border: 1px solid #ddd; border-radius: 8px; }
        h1, h2 { line-height: 1.3; }
        h2 { margin-top: 0; font-size: 22px; }
        pre { overflow-x: auto; padding: 12px; background: #f3f3f3; }
        .result { font-weight: bold; }
    </style>
</head>
<body>
    <h1>ООП в PHP: архітектура та патерни</h1>
    <p>Лабораторна робота №4 · Варіант 18 · Інтернет-магазин iPhone</p>
    <p>Томенко Віталій Ігорович, КН-42</p>

    <section>
        <h2>1. Singleton, Factory та наслідування</h2>
        <p><?= htmlspecialchars($databaseMessage) ?></p>
        <p class="result"><?= htmlspecialchars($singletonResult) ?></p>
        <p><?= htmlspecialchars($product->getDetails()) ?></p>
        <p><?= htmlspecialchars($user->getDetails()) ?></p>
    </section>

    <section>
        <h2>2. Інтерфейс і трейт експорту</h2>
        <pre><?= htmlspecialchars($product->exportToJson()) ?></pre>
    </section>

    <section>
        <h2>3. Dependency Injection</h2>
        <p><?= nl2br(htmlspecialchars($serviceResult)) ?></p>
    </section>

    <section>
        <h2>4. Варіант 18 — Student і Group</h2>
        <p>Знайдено студента: <strong><?= htmlspecialchars($foundStudent?->getFullName() ?? 'не знайдено') ?></strong></p>
        <p>Кількість студентів після видалення: <strong><?= count($group->getStudents()) ?></strong></p>
    </section>

    <section>
        <h2>5. Варіант 18 — ієрархія працівників</h2>
        <ul>
            <?php foreach ($workers as $worker) { ?>
                <li><?= htmlspecialchars($worker->getName()) ?>: <strong><?= $worker->calculateSalary() ?> грн</strong></li>
            <?php } ?>
        </ul>
    </section>

    <section>
        <h2>6. Варіант 18 — матриця 3x3</h2>
        <p>Сума головної діагоналі: <strong><?= $matrix->getMainDiagonalSum() ?></strong></p>
        <p>Визначник матриці: <strong><?= $matrix->getDeterminant() ?></strong></p>
    </section>
</body>
</html>
