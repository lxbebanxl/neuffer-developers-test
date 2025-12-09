<?php
declare(strict_types=1);

namespace App\Operations;

use App\Interfaces\OperationInterface;
use App\Exceptions\DivisionByZeroException;

class Division implements OperationInterface
{
    public function calculate(float $a, float $b): float
    {
        if ($b == 0) {
            throw new DivisionByZeroException("Division by zero not possible");
        }
        return $a / $b;
    }
}
