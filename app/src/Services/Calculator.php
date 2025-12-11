<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\OperationInterface;
use App\Interfaces\DataReaderInterface;
use App\Interfaces\DataWriterInterface;
use App\Enums\OperationType;
use App\Config\Config;
use App\Validators\Validator;
use App\Logger\Logger;
use App\Exceptions\ValidationException;
use App\Exceptions\DivisionByZeroException;

class Calculator
{
    public function __construct(
        private readonly OperationType $action,
        private readonly DataReaderInterface $reader,
        private readonly DataWriterInterface $writer,
        private readonly Logger $logger    
    ) {}
    
    public function execute(string $inputFile, string $outputFile = Config::OUTPUT_FILE): void
    {
        $actionName = $this->action->value;
        $this->logger->info("Started '{$actionName}' operation");
        
        try {
            $operation = $this->createOperation($this->action);
            $validator = new Validator();
            
            $data = $this->reader->read($inputFile);
            $results = $this->processData($data, $operation, $validator);
            $this->writer->write($outputFile, $results);
            
            $this->logger->info("Finished '{$actionName}' operation");
            echo "Processing complete. Results in {$outputFile}" . PHP_EOL;
            
        } catch (\Exception $e) {
            $this->logger->error("Error during action '{$this->action}': " . $e->getMessage());
            throw $e;
        }
    }
    
    private function createOperation(OperationType $action): OperationInterface
    {   
        $className = $action->getOperationClass();
        return new $className();
    }
    
    /**
     * @param array<int, array<int, float>> $data
     * @return array<int, array<int, float>>
     */
    private function processData(array $data, OperationInterface $operation, Validator $validator): array
    {
        $results = [];
        
        foreach ($data as $row) {
            [$num1, $num2] = $row;
            
            try {
                $validator->validateNumberRange($num1, $num2);
                $result = $operation->calculate($num1, $num2);
                
                if ($result > 0) {
                    $results[] = [$num1, $num2, $result];
                } else {
                    $this->logger->warning("The given numbers {$num1} and {$num2} are wrong");
                }
                
            } catch (DivisionByZeroException $e) {
                $this->logger->error(
                    "The given numbers {$num1} and {$num2} are wrong, division by zero not possible"
                );
            } catch (ValidationException $e) {
                $this->logger->warning($e->getMessage());
            }
        }
        
        return $results;
    }
}
