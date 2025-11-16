<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentGenerationService
{
    /**
     * System fields that don't affect document content
     */
    private const SYSTEM_FIELDS = ['_token', 'docx_type', 'store_for_submit', 'request_id', 'save_draft', 'force_regenerate'];
    
    /**
     * Lock file timeout in seconds (5 minutes)
     */
    private const LOCK_TIMEOUT = 300;
    
    /**
     * Normalize data for consistent hashing
     * Removes system fields and sorts keys
     */
    public function normalizeDataForHash(array $data): array
    {
        $normalized = $data;
        
        // Remove system fields
        foreach (self::SYSTEM_FIELDS as $field) {
            unset($normalized[$field]);
        }
        
        // Sort keys for consistent hash regardless of field order
        ksort($normalized);
        
        return $normalized;
    }
    
    /**
     * Calculate stable hash from request data (before fallback merge)
     */
    public function calculateDataHash(array $requestData, string $docxType): string
    {
        $normalizedData = $this->normalizeDataForHash($requestData);
        
        $hashSource = json_encode([
            'type' => $docxType,
            'data' => $normalizedData
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        return substr(hash('sha256', $hashSource), 0, 16);
    }
    
    /**
     * Validate required fields before generation
     */
    public function validateRequiredFields(array $data, string $docxType): array
    {
        $errors = [];
        
        // Define required fields per document type
        $requiredFields = [
            'incentive' => ['name', 'rank', 'college'],
            'recommendation' => ['name', 'rec_collegeheader'],
        ];
        
        $fields = $requiredFields[$docxType] ?? [];
        
        foreach ($fields as $field) {
            if (empty($data[$field]) || trim((string)$data[$field]) === '') {
                $errors[] = "Required field '{$field}' is missing or empty";
            }
        }
        
        return $errors;
    }
    
    /**
     * Check if existing file is still valid (hash matches)
     */
    public function isFileValid(string $filePath, string $expectedHash): bool
    {
        if (!Storage::disk('local')->exists($filePath)) {
            return false;
        }
        
        // For now, we'll regenerate if file exists and force_regenerate is set
        // In future, we could store hash in metadata or filename
        return true;
    }
    
    /**
     * Safely replace file by generating to temp location first
     */
    public function safeFileReplace(callable $generator, string $targetPath, string $backupPath = null): string
    {
        // Generate to temporary location
        $tempPath = $targetPath . '.tmp.' . uniqid();
        $tempDir = dirname($tempPath);
        
        // Ensure directory exists
        if (!Storage::disk('local')->exists($tempDir)) {
            Storage::disk('local')->makeDirectory($tempDir, 0777, true);
        }
        
        try {
            // Generate file to temp location
            $generatedPath = $generator($tempPath);
            
            // Verify file exists and is not empty
            $absolutePath = Storage::disk('local')->path($generatedPath);
            if (!file_exists($absolutePath) || filesize($absolutePath) === 0) {
                throw new \RuntimeException('Generated file is empty or does not exist');
            }
            
            // Create backup of old file if it exists
            if ($backupPath && Storage::disk('local')->exists($targetPath)) {
                $backupDir = dirname($backupPath);
                if (!Storage::disk('local')->exists($backupDir)) {
                    Storage::disk('local')->makeDirectory($backupDir, 0777, true);
                }
                Storage::disk('local')->copy($targetPath, $backupPath);
                Log::info('Created backup of old file', [
                    'original' => $targetPath,
                    'backup' => $backupPath
                ]);
            }
            
            // Move temp file to target location (atomic operation)
            if (Storage::disk('local')->exists($targetPath)) {
                Storage::disk('local')->delete($targetPath);
            }
            Storage::disk('local')->move($generatedPath, $targetPath);
            
            Log::info('File safely replaced', [
                'target' => $targetPath,
                'backup' => $backupPath
            ]);
            
            return $targetPath;
            
        } catch (\Exception $e) {
            // Clean up temp file on error
            if (Storage::disk('local')->exists($tempPath)) {
                Storage::disk('local')->delete($tempPath);
            }
            
            // Restore backup if generation failed
            if ($backupPath && Storage::disk('local')->exists($backupPath)) {
                Storage::disk('local')->copy($backupPath, $targetPath);
                Log::info('Restored backup after generation failure', [
                    'target' => $targetPath,
                    'backup' => $backupPath
                ]);
            }
            
            throw $e;
        }
    }
    
    /**
     * Create or check lock file for concurrent generation prevention
     * Note: $lockFile should be absolute path, not relative
     */
    public function acquireLock(string $lockFile): bool
    {
        // If relative path, convert to absolute
        $lockAbsolute = str_starts_with($lockFile, '/') || str_starts_with($lockFile, '\\') 
            ? $lockFile 
            : Storage::disk('local')->path($lockFile);
        $lockDir = dirname($lockAbsolute);
        
        // Ensure directory exists
        if (!is_dir($lockDir)) {
            mkdir($lockDir, 0777, true);
        }
        
        // Check if lock exists and is stale
        if (file_exists($lockAbsolute)) {
            $lockAge = time() - filemtime($lockAbsolute);
            
            // If lock is stale (older than timeout), remove it
            if ($lockAge > self::LOCK_TIMEOUT) {
                @unlink($lockAbsolute);
                Log::warning('Removed stale lock file', [
                    'lock_file' => $lockAbsolute,
                    'age_seconds' => $lockAge
                ]);
            } else {
                // Lock is still valid, another process is generating
                return false;
            }
        }
        
        // Create lock file with current PID
        try {
            file_put_contents($lockAbsolute, getmypid());
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create lock file', [
                'lock_file' => $lockAbsolute,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Release lock file
     * Note: $lockFile should be absolute path, not relative
     */
    public function releaseLock(string $lockFile): void
    {
        $lockAbsolute = str_starts_with($lockFile, '/') || str_starts_with($lockFile, '\\') 
            ? $lockFile 
            : Storage::disk('local')->path($lockFile);
        if (file_exists($lockAbsolute)) {
            @unlink($lockAbsolute);
        }
    }
    
    /**
     * Wait for lock to be released (for concurrent requests)
     * Note: $lockFile should be absolute path, not relative
     */
    public function waitForLock(string $lockFile, int $maxWait = 5, float $waitInterval = 0.1): bool
    {
        $lockAbsolute = str_starts_with($lockFile, '/') || str_starts_with($lockFile, '\\') 
            ? $lockFile 
            : Storage::disk('local')->path($lockFile);
        $waited = 0;
        
        while (file_exists($lockAbsolute) && $waited < $maxWait) {
            usleep((int)($waitInterval * 1000000)); // Convert to microseconds
            $waited += $waitInterval;
        }
        
        return !file_exists($lockAbsolute);
    }
    
    /**
     * Set template values with validation and logging
     */
    public function setTemplateValues(TemplateProcessor $processor, array $values, bool $logMissing = false): array
    {
        $missingPlaceholders = [];
        $setCount = 0;
        
        foreach ($values as $key => $value) {
            try {
                $processor->setValue($key, (string)($value ?? ''));
                $setCount++;
            } catch (\Exception $e) {
                // Placeholder doesn't exist in template
                $missingPlaceholders[] = $key;
                
                if ($logMissing) {
                    Log::debug('Template placeholder not found', [
                        'placeholder' => $key,
                        'value' => $value
                    ]);
                }
            }
        }
        
        // Log summary if there are missing placeholders
        if (!empty($missingPlaceholders) && $logMissing) {
            Log::info('Some template placeholders were not found', [
                'missing_count' => count($missingPlaceholders),
                'missing_placeholders' => $missingPlaceholders,
                'total_placeholders' => count($values),
                'set_successfully' => $setCount
            ]);
        }
        
        return $missingPlaceholders;
    }
    
    /**
     * Check if existing file should be regenerated
     */
    public function shouldRegenerate(string $filePath, string $currentHash, bool $forceRegenerate, bool $hasFormData): bool
    {
        // Always regenerate if forced
        if ($forceRegenerate) {
            return true;
        }
        
        // If no form data provided, don't regenerate
        if (!$hasFormData) {
            return false;
        }
        
        // If file doesn't exist, must generate
        if (!Storage::disk('local')->exists($filePath)) {
            return true;
        }
        
        // For now, regenerate if form data is provided
        // In future, could compare stored hash with current hash
        return true;
    }
    
    /**
     * Get cache path for preview files
     */
    public function getPreviewCachePath(string $docxType, string $hash): string
    {
        return "temp/docx_cache/{$docxType}_{$hash}";
    }
    
    /**
     * Get expected filename for document type
     */
    public function getDocumentFilename(string $docxType, bool $isPdf = false): string
    {
        $ext = $isPdf ? '.pdf' : '.docx';
        
        $filenames = [
            'incentive' => 'Incentive_Application_Form' . $ext,
            'recommendation' => 'Recommendation_Letter_Form' . $ext,
            'terminal' => 'Terminal_Report_Form' . $ext,
        ];
        
        return $filenames[$docxType] ?? 'document' . $ext;
    }
}

