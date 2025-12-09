<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\DataReaderInterface;
use App\Config\Config;
use App\Logger\Logger;
use App\Exceptions\InvalidArgumentException;

class CsvReader implements DataReaderInterface
{
    public function __construct(
        private ?Logger $logger = null
    ) {
        $this->logger = $logger ?? Logger::getInstance();
    }
    
    /**
     * @return array<int, array<int, float>>
     */
    public function read(string $source): array
    {
        $this->validateFile($source);
        
        $data = [];
        $handle = fopen($source, 'r');
        
        if ($handle === false) {
            throw new InvalidArgumentException("File could not be open: {$source}");
        }
        
        $lineNumber = 0;
        
        while (($row = fgetcsv($handle, 0, Config::CSV_DELIMITER, Config::CSV_ENCLOSURE, Config::CSV_ESCAPE)) !== false) {
            $lineNumber++;
            
            // Check whether there are enough columns
            if (count($row) < Config::CSV_MIN_COLUMNS) {
                $this->logger->warning(
                    "Line {$lineNumber}: Too few columns (expected: " . Config::CSV_MIN_COLUMNS . ", found: " . count($row) . ") - skipped"
                );
                continue;
            }
            
            // Check whether both values are numeric
            if (!is_numeric($row[0]) || !is_numeric($row[1])) {
                $this->logger->warning(
                    "Line {$lineNumber}: Non-numeric values found ('{$row[0]}', '{$row[1]}') - skipped"
                );
                continue;
            }
            
            $num1 = (float)$row[0];
            $num2 = (float)$row[1];
            
            $data[] = [$num1, $num2];
        }
        
        fclose($handle);
        
        if (empty($data)) {
            throw new InvalidArgumentException("No valid data found in CSV file: {$source}");
        }
        
        $this->logger->info("CSV read: {$lineNumber} lines processed, " . count($data) . " valid records found");
        
        return $data;
    }
    
    private function validateFile(string $file): void
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException("File not found: {$file}");
        }
        
        if (!is_file($file)) {
            throw new InvalidArgumentException("Path is not a file: {$file}");
        }
        
        if (!is_readable($file)) {
            throw new InvalidArgumentException("File is unreadable: {$file}");
        }
    }
}
