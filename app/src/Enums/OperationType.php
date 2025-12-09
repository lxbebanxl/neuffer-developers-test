<?php
declare(strict_types=1);

namespace App\Enums;

enum OperationType: string
{
    case PLUS = 'plus';
    case MINUS = 'minus';
    case MULTIPLY = 'multiply';
    case DIVISION = 'division';
    
    /**
     * Returns the full class name of the operation
     */
    public function getOperationClass(): string
    {
        return match($this) {
            self::PLUS => \App\Operations\Plus::class,
            self::MINUS => \App\Operations\Minus::class,
            self::MULTIPLY => \App\Operations\Multiply::class,
            self::DIVISION => \App\Operations\Division::class,
        };
    }
    
    /**
     * Creates an enum from a string (case-insensitive)
     */
    public static function fromString(string $value): self
    {
        return self::from(strtolower($value));
    }
    
    /**
     * Attempts to create Enum from string, returns null on error
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtolower($value));
    }
    
    /**
     * Returns all valid values as an array
     * 
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    /**
     * Returns all valid values as a comma-separated string
     */
    public static function valuesAsString(): string
    {
        return implode(', ', self::values());
    }
}
