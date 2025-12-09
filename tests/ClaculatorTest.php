<?php
declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

use App\Operations\Plus;
use App\Operations\Minus;
use App\Operations\Multiply;
use App\Operations\Division;
use App\Validators\Validator;
use App\Validators\InputValidator;
use App\Enums\OperationType;
use App\Exceptions\DivisionByZeroException;
use App\Exceptions\ValidationException;
use App\Exceptions\InvalidArgumentException;

class CalculatorTest
{
    public function __construct(
        private int $testsPassed = 0,
        private int $testsFailed = 0
    ) {}
    
    public function runAll(): void
    {
        echo "=== Calculator Tests ===" . PHP_EOL;
        
        $this->testAddition();
        $this->testSubtraction();
        $this->testMultiplication();
        $this->testDivision();
        $this->testDivisionByZero();
        $this->testValidation();
        $this->testInputValidation();
        $this->testEnums();
        
        echo PHP_EOL . "Tests: {$this->testsPassed} successful, {$this->testsFailed} failed" . PHP_EOL;
    }
    
    private function testAddition(): void
    {
        $op = new Plus();
        $this->assertEquals(8.0, $op->calculate(5.0, 3.0), "Plus: 5 + 3 = 8");
        $this->assertEquals(-2.0, $op->calculate(-5.0, 3.0), "Plus: -5 + 3 = -2");
    }
    
    private function testSubtraction(): void
    {
        $op = new Minus();
        $this->assertEquals(2.0, $op->calculate(5.0, 3.0), "Minus: 5 - 3 = 2");
        $this->assertEquals(-8.0, $op->calculate(-5.0, 3.0), "Minus: -5 - 3 = -8");
    }
    
    private function testMultiplication(): void
    {
        $op = new Multiply();
        $this->assertEquals(15.0, $op->calculate(5.0, 3.0), "Multiply: 5 * 3 = 15");
        $this->assertEquals(-15.0, $op->calculate(-5.0, 3.0), "Multiply: -5 * 3 = -15");
    }
    
    private function testDivision(): void
    {
        $op = new Division();
        $this->assertEquals(2.5, $op->calculate(5.0, 2.0), "Division: 5 / 2 = 2.5");
        $this->assertEquals(-2.5, $op->calculate(-5.0, 2.0), "Division: -5 / 2 = -2.5");
    }
    
    private function testDivisionByZero(): void
    {
        $op = new Division();
        try {
            $op->calculate(5.0, 0.0);
            $this->fail("Division by zero should throw an exception");
        } catch (DivisionByZeroException $e) {
            $this->pass("Division by zero correctly throws an exception");
        }
    }
    
    private function testValidation(): void
    {
        $validator = new Validator();
        
        try {
            $validator->validateNumberRange(150.0, 20.0, new Plus());
            $this->fail("Numbers out of range should throw an exception.");
        } catch (ValidationException $e) {
            $this->pass("Invalid numbers throw a correct exception");
        }
    }
    
    private function testInputValidation(): void
    {
        $inputValidator = new InputValidator();
        
        // Test ShortOpts
        try {
            $result = $inputValidator->validateAndNormalizeOptions([
                'a' => 'plus',
                'f' => 'test.csv'
            ]);
            $this->assertEnumEquals(OperationType::PLUS, $result['action'], "ShortOpt action valid");
            $this->pass("ShortOpts are processed correctly");
        } catch (\Exception $e) {
            $this->fail("ShortOpts should work: " . $e->getMessage());
        }
        
        // Test LongOpts
        try {
            $result = $inputValidator->validateAndNormalizeOptions([
                'action' => 'multiply',
                'file' => 'test.csv'
            ]);
            $this->assertEnumEquals(OperationType::MULTIPLY, $result['action'], "LongOpt action valid");
            $this->pass("LongOpts are processed correctly");
        } catch (\Exception $e) {
            $this->fail("LongOpts should work: " . $e->getMessage());
        }
        
        // Test ungültige Action
        try {
            $inputValidator->validateAndNormalizeOptions([
                'action' => 'invalid_action',
                'file' => 'example_input.csv'
            ]);
            $this->fail("Invalid action should throw an exception");
        } catch (InvalidArgumentException $e) {
            $this->pass("Invalid action is correctly intercepted");
        }
        
        // Test Directory Traversal Prevention
        try {
            $inputValidator->validateAndNormalizeOptions([
                'action' => 'plus',
                'file' => '../../../etc/passwd'
            ]);
            $this->fail("Directory Traversal should be blocked");
        } catch (InvalidArgumentException $e) {
            $this->pass("Directory Traversal is correctly prevented");
        }
    }
    
    private function testEnums(): void
    {
        // Test Enum Values
        $values = OperationType::values();
        $this->assertEquals(4, count($values), "Enum has 4 values");
        
        // Test fromString (case-insensitive)
        $enum = OperationType::fromString('PLUS');
        $this->assertEnumEquals(OperationType::PLUS, $enum, "fromString in uppercase");
        
        $enum = OperationType::fromString('multiply');
        $this->assertEnumEquals(OperationType::MULTIPLY, $enum, "fromString in lowercase");
        
        // Test tryFromString
        $enum = OperationType::tryFromString('invalid');
        if ($enum === null) {
            $this->pass("tryFromString returns zero for invalid values");
        } else {
            $this->fail("tryFromString should return null");
        }
        
        // Test getOperationClass
        $class = OperationType::PLUS->getOperationClass();
        $this->assertStringEquals(Plus::class, $class, "getOperationClass for PLUS");
        
        // Test valuesAsString
        $str = OperationType::valuesAsString();
        $this->pass("valuesAsString: " . $str);
    }
    
    private function assertEquals(float|string $expected, float|string $actual, string $message): void
    {
        if (is_float($expected) && is_float($actual)) {
            if (abs($expected - $actual) < 0.0001) {
                $this->pass($message);
            } else {
                $this->fail($message . " (expected: {$expected}, received: {$actual})");
            }
        } else {
            if ($expected === $actual) {
                $this->pass($message);
            } else {
                $this->fail($message . " (expected: {$expected}, received: {$actual})");
            }
        }
    }
    
    private function assertStringEquals(string $expected, string $actual, string $message): void
    {
        if ($expected === $actual) {
            $this->pass($message);
        } else {
            $this->fail($message . " (expected: {$expected}, received: {$actual})");
        }
    }
    
    private function assertEnumEquals(OperationType $expected, OperationType $actual, string $message): void
    {
        if ($expected === $actual) {
            $this->pass($message);
        } else {
            $this->fail($message . " (expected: {$expected->value}, received: {$actual->value})");
        }
    }
    
    private function pass(string $message): void
    {
        echo "✓ {$message}" . PHP_EOL;
        $this->testsPassed++;
    }
    
    private function fail(string $message): void
    {
        echo "✗ {$message}" . PHP_EOL;
        $this->testsFailed++;
    }
}

// Tests ausführen
$test = new CalculatorTest();
$test->runAll();
