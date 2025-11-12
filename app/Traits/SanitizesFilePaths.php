<?php

namespace App\Traits;

trait SanitizesFilePaths
{
    /**
     * Sanitize file path to prevent directory traversal attacks
     * 
     * @param string $path
     * @return string
     */
    protected function sanitizePath(string $path): string
    {
        // Remove any directory traversal attempts
        $path = str_replace('..', '', $path);
        
        // Remove leading/trailing slashes and normalize
        $path = trim($path, '/\\');
        
        // Replace backslashes with forward slashes for consistency
        $path = str_replace('\\', '/', $path);
        
        // Remove any null bytes
        $path = str_replace("\0", '', $path);
        
        // Remove any absolute path indicators
        $path = ltrim($path, '/');
        
        return $path;
    }

    /**
     * Validate file size against maximum allowed size
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxSizeInMB Maximum size in megabytes
     * @return bool
     * @throws \Exception
     */
    protected function validateFileSize($file, int $maxSizeInMB = 10): bool
    {
        $maxSizeInBytes = $maxSizeInMB * 1024 * 1024; // Convert MB to bytes
        $fileSize = $file->getSize();
        
        if ($fileSize > $maxSizeInBytes) {
            throw new \Exception("File size exceeds maximum allowed size of {$maxSizeInMB}MB. File size: " . round($fileSize / 1024 / 1024, 2) . "MB");
        }
        
        return true;
    }

    /**
     * Get sanitized file path for storage
     * 
     * @param string $basePath Base path (e.g., "requests/{userId}/{requestCode}")
     * @param string $filename Filename
     * @return string
     */
    protected function getSanitizedStoragePath(string $basePath, string $filename): string
    {
        $basePath = $this->sanitizePath($basePath);
        $filename = $this->sanitizePath(basename($filename)); // Use basename to prevent path injection
        
        return $basePath . '/' . $filename;
    }
}

