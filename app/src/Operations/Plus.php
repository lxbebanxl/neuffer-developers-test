<?php
declare(strict_types=1);

namespace App\Operations;

use App\Interfaces\OperationInterface;

class Plus implements OperationInterface
{
    public function calculate(float $a, float $b): float
    {
        return $a + $b;
    }
}