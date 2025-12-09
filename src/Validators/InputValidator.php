<?php
declare(strict_types=1);

namespace App\Validators;

use App\Enums\OperationType;
use App\Config\Config;
use App\Exceptions\InvalidArgumentException;

class InputValidator
{
    /**
     * Validates and normalizes CLI options
     * Supports both ShortOpts (-a, -f) and LongOpts (--action, --file)
     * 
     * @param array<string, string|array> $options
     * @return array{action: OperationType, file: string}
     */
    public function validateAndNormalizeOptions(array $options): array
    {
        $actionFromOptions = $this->extractOption($options, 'action', 'a');
        $fileFromOptions = $this->extractOption($options, 'file', 'f');
        
        if ($actionFromOptions === null) {
            throw new InvalidArgumentException(
                'Missing action. Use -a <action> or --action=<action>'
            );
        }
        
        if ($fileFromOptions === null) {
            throw new InvalidArgumentException(
                'Missing file. Use -f <file> or --file=<file>'
            );
        }
        
        // Validates action
        $action = OperationType::tryfromString($actionFromOptions);
        
        if ($action === null) {
            throw new InvalidArgumentException(
                "Invalid action: '{$actionFromOptions}'. The following are permitted: " . OperationType::valuesAsString()
            );
        }
        
        // Validates file
        $file = $this->sanitizeFilePath($fileFromOptions);
        $this->validateBasicFilePath($file);
        
        return [
            'action' => $action,
            'file' => $file
        ];
    }
    
    /**
     * Extracts Option from ShortOpt or LongOpt
     */
    private function extractOption(array $options, string $longName, string $shortName): ?string
    {
        // LongOpt has priority
        if (isset($options[$longName])) {
            return is_array($options[$longName]) ? $options[$longName][0] : $options[$longName];
        }
        
        // Fallback to ShortOpt
        if (isset($options[$shortName])) {
            return is_array($options[$shortName]) ? $options[$shortName][0] : $options[$shortName];
        }
        
        return null;
    }
    
    /**
     * Sanitized Filepath
     */
    private function sanitizeFilePath(string $path): string
    {
        $pathTrimed = trim($path);
        
        // Removes null bytes (Security)
        $pathRemovedNullBytes = str_replace("\0", '', $pathTrimed);
        
        // Normalizes path separators
        $sanitized = str_replace('\\', '/', $pathRemovedNullBytes);
        
        if ($sanitized === '') {
            throw new InvalidArgumentException('Invalid file path: Path is empty');
        }
        
        return $sanitized;
    }
    
    /**
     * Validates file path for security and existence
     */
    private function validateBasicFilePath(string $path): void
    {
        // Prevent Directory Traversal
        if (str_contains($path, '..')) {
            throw new InvalidArgumentException(
                'Invalid file path: Directory Traversal (..) is not permitted'
            );
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($extension !== Config::ALLOWED_FILE_EXTENSION) {
            throw new InvalidArgumentException(
                "Invalid file extension: '{$extension}'. Only" . strtoupper(Config::ALLOWED_FILE_EXTENSION) . "files are permitted"
            );
        }
        
        $maxSize = Config::MAX_FILE_SIZE;
        if (filesize($path) > $maxSize) {
            throw new InvalidArgumentException(
                'File is too large. Maximum: 10 MB'
            );
        }
    }
}
