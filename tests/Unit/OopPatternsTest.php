<?php

namespace Tests\Unit;

use App\Core\Database;
use App\Core\EntityFactory;
use App\Models\Product;
use App\Tasks\DepartmentHead;
use App\Tasks\EnterpriseManager;
use App\Tasks\Group;
use App\Tasks\SquareMatrix;
use App\Tasks\Student;
use App\Tasks\WorkshopManager;
use PHPUnit\Framework\TestCase;

class OopPatternsTest extends TestCase
{
    public function test_singleton_returns_same_instance(): void
    {
        ob_start();
        $first = Database::getInstance();
        $second = Database::getInstance();
        ob_end_clean();

        $this->assertSame($first, $second);
    }

    public function test_factory_creates_product_and_exports_json(): void
    {
        $factory = new EntityFactory;
        $product = $factory->createEntity('product', [
            'id' => 'p1',
            'createdAt' => '2026-11-07',
            'name' => 'iPhone 16',
            'price' => 42999.0,
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertStringContainsString('iPhone 16', $product->exportToJson());
    }

    public function test_group_can_add_search_and_remove_student(): void
    {
        $group = new Group(42, 'КН-42');
        $group->addStudent(new Student(1, 'Віталій', 'Томенко', '2005-01-10'));

        $this->assertSame('Віталій Томенко', $group->searchStudent('Томенко')?->getFullName());

        $group->removeStudent(1);
        $this->assertCount(0, $group->getStudents());
    }

    public function test_worker_hierarchy_calculates_salary(): void
    {
        $this->assertSame(23000.0, (new WorkshopManager('A', 20000))->calculateSalary());
        $this->assertSame(28000.0, (new DepartmentHead('B', 20000))->calculateSalary());
        $this->assertSame(38000.0, (new EnterpriseManager('C', 20000))->calculateSalary());
    }

    public function test_square_matrix_calculations(): void
    {
        $matrix = new SquareMatrix([
            [1, 2, 3],
            [0, 4, 5],
            [1, 0, 6],
        ]);

        $this->assertSame(11, $matrix->getMainDiagonalSum());
        $this->assertSame(22, $matrix->getDeterminant());
    }
}
