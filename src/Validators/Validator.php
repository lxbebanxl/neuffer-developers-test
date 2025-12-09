<?php
declare(strict_types=1);

namespace App\Validators;

use App\Config\Config;
use App\Exceptions\ValidationException;
use App\Exceptions\InvalidArgumentException;

class Validator
{
    public function validateFile(string $file): void
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException("File not found: {$file}");
        }
        
        if (!is_readable($file)) {
            throw new InvalidArgumentException("File not readable: {$file}");
        }
    }
    
    public function validateNumberRange(float $num1, float $num2): void
    {
        if ($num1 < Config::MIN_NUMBER || $num1 > Config::MAX_NUMBER || 
            $num2 < Config::MIN_NUMBER || $num2 > Config::MAX_NUMBER) {
            throw new ValidationException("The given numbers {$num1} and {$num2} are wrong");
        }
    }
}
