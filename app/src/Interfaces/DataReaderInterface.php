<?php
declare(strict_types=1);

namespace App\Interfaces;

interface DataReaderInterface
{
    /**
     * Reads data from a source
     * 
     * @param string $source path to file
     * @return array<int, array<int, float>> array of pairs of numbers
     * @throws InvalidArgumentException in case of reading errors
     */
    public function read(string $source): array;
}
