<?php
declare(strict_types=1);

namespace App\Config;

class Config
{
    // File validation
    public const  MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
    public const  ALLOWED_FILE_EXTENSION = 'csv';
    
    // Number validation
    public const  MIN_NUMBER = -100;
    public const  MAX_NUMBER = 100;
    
    // file names
    public const  LOG_FILE = 'log.txt';
    public const  OUTPUT_FILE = 'result.csv';
    
    // CSV config
    public const  CSV_DELIMITER = ';';
    public const  CSV_ENCLOSURE = '"';
    public const  CSV_ESCAPE = '\\';
    public const  CSV_MIN_COLUMNS = 2;
}