<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\DataWriterInterface;
use App\Config\Config;
use App\Exceptions\InvalidArgumentException;

class CsvWriter implements DataWriterInterface
{
    /**
     * @param array<int, array<int, float>> $data
     */
    public function write(string $destination, array $data): void
    {
        $handle = fopen($destination, 'w');
        
        if ($handle === false) {
            throw new InvalidArgumentException("Output file could not be created: {$destination}");
        }
        
        foreach ($data as $row) {
            fputcsv($handle, $row, Config::CSV_DELIMITER, Config::CSV_ENCLOSURE, Config::CSV_ESCAPE);
        }
        
        fclose($handle);
    }
}
