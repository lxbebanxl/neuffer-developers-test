<?php
declare(strict_types=1);

namespace App\Interfaces;

interface DataWriterInterface
{
    /**
     * Writes data to a destination
     * 
     * @param string $destination path to destination
     * @param array<int, array<int, float>> $data data to be written
     * @throws InvalidArgumentException in case of errors
     */
    public function write(string $destination, array $data): void;
}
