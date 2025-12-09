<?php
declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

use App\Services\Calculator;
use App\Services\CsvReader;
use App\Services\CsvWriter;
use App\Logger\Logger;
use App\Validators\InputValidator;
use App\Enums\OperationType;

try {
    $shortOpts = 'a:f:h';
    $longOpts  = [
        'action:',
        'file:',
        'help',
    ];

    $options = getopt($shortOpts, $longOpts);

    if (isset($options['h']) || isset($options['help']) || empty($options)) {
        showHelp();
        exit(0);
    }
    
    $logger = Logger::getInstance();
    $inputValidator = new InputValidator();
    $reader = new CsvReader($logger);
    $writer = new CsvWriter();
    
    $validatedOptions = $inputValidator->validateAndNormalizeOptions($options);
    
    $calculator = new Calculator(
        $validatedOptions['action'],
        $reader,
        $writer,
        $logger
    );
    $calculator->execute($validatedOptions['file']);
    
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    Logger::getInstance()->error('Fatal error: ' . $e->getMessage());
    exit(1);
}

function showHelp(): void
{
    $operations = OperationType::valuesAsString();
    
    echo <<<HELP
Calculator CLI - Performs calculations on CSV data

Usage:
  php console.php -a <action> -f <file>
  php console.php --action=<action> --file=<file>

Options:
  -a, --action    Mathematical operation ({$operations})
  -f, --file      Path to CSV file
  -h, --help      Displays this help text

Examples:
  php console.php -a plus -f test.csv
  php console.php --action=division --file=data.csv
  php console.php -a multiply -f numbers.csv

HELP;
}