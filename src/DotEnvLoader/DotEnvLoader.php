<?php

namespace Aimocs\Iis\Flat\DotEnvLoader;

class DotEnvLoader
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        if (!file_exists($filePath)) {
            throw new RuntimeException("The .env file does not exist: {$filePath}");
        }
    }

    public function load(): void
    {
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignore comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse key-value pairs
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);

                // Trim spaces and remove optional quotes
                $key = trim($key);
                $value = trim($value);
                $value = $this->stripQuotes($value);

                // Set environment variable if not already defined
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    putenv("{$key}={$value}");
                }
            }
        }
    }

    private function stripQuotes(string $value): string
    {
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            return substr($value, 1, -1);
        }
        return $value;
    }
}



