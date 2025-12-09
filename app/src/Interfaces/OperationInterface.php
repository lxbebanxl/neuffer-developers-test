<?php
declare(strict_types=1);

namespace App\Interfaces;

interface OperationInterface
{
    /**
     * Performs the calculation
     * 
     * @param float $a first number
     * @param float $b second number
     * @return float Result of the calculation
     * @throws \App\Exceptions\DivisionByZeroException on division by null
     */
    public function calculate(float $a, float $b): float;
}


