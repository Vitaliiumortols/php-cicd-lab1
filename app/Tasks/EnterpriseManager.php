<?php

declare(strict_types=1);

namespace App\Tasks;

class EnterpriseManager extends DepartmentHead
{
    public function calculateSalary(): float
    {
        return parent::calculateSalary() + 10000;
    }
}
