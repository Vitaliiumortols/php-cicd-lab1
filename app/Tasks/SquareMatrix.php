<?php

declare(strict_types=1);

namespace App\Tasks;

use InvalidArgumentException;

class SquareMatrix
{
    public function __construct(private readonly array $matrix)
    {
        if (count($matrix) !== 3) {
            throw new InvalidArgumentException('Матриця повинна мати розмір 3x3');
        }

        foreach ($matrix as $row) {
            if (count($row) !== 3) {
                throw new InvalidArgumentException('Матриця повинна мати розмір 3x3');
            }
        }
    }

    public function getMainDiagonalSum(): int|float
    {
        return $this->matrix[0][0]
            + $this->matrix[1][1]
            + $this->matrix[2][2];
    }

    public function getDeterminant(): int|float
    {
        $a = $this->matrix;

        return $a[0][0] * ($a[1][1] * $a[2][2] - $a[1][2] * $a[2][1])
            - $a[0][1] * ($a[1][0] * $a[2][2] - $a[1][2] * $a[2][0])
            + $a[0][2] * ($a[1][0] * $a[2][1] - $a[1][1] * $a[2][0]);
    }
}
