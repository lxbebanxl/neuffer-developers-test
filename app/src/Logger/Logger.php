<?php
declare(strict_types=1);

namespace App\Logger;

use App\Config\Config;

class Logger
{
    private static ?Logger $instance = null;
    
    private function __construct(
        private readonly string $logFile = Config::LOG_FILE
    ) {}
    
    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }
    
    public function warning(string $message): void
    {
        $this->log('WARNING', $message);
    }
    
    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }
    
    private function log(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}