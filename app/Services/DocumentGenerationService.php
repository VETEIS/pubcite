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
     * Sanitize string for XML compatibility (PHPWord TemplateProcessor requirement)
     * Removes invalid XML characters and properly handles XML entities
     */
    private function sanitizeForXml(string $value): string
    {
        if (empty($value)) {
            return '';
        }
        
        // Ensure valid UTF-8 encoding
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
        
        // Remove control characters except tab (0x09), newline (0x0A), and carriage return (0x0D)
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Filter invalid XML characters character-by-character to handle Unicode properly
        // XML 1.0 allows: #x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF]
        $result = '';
        $length = mb_strlen($value, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($value, $i, 1, 'UTF-8');
            $code = mb_ord($char, 'UTF-8');
            
            // Skip if code point is invalid
            if ($code === false) {
                continue;
            }
            
            // Skip surrogate pairs (0xD800-0xDFFF) - these are invalid in XML
            if ($code >= 0xD800 && $code <= 0xDFFF) {
                continue;
            }
            
            // Keep valid XML characters
            // Valid ranges: 0x9 (tab), 0xA (newline), 0xD (carriage return), 
            // 0x20-0xD7FF, 0xE000-0xFFFD, 0x10000-0x10FFFF
            if ($code === 0x9 || $code === 0xA || $code === 0xD || 
                ($code >= 0x20 && $code <= 0xD7FF) || 
                ($code >= 0xE000 && $code <= 0xFFFD) ||
                ($code >= 0x10000 && $code <= 0x10FFFF)) {
                $result .= $char;
            }
        }
        
        // Final validation: ensure the result is valid UTF-8
        if (!mb_check_encoding($result, 'UTF-8')) {
            $result = mb_convert_encoding($result, 'UTF-8', 'UTF-8');
        }
        
        return trim($result);
    }
    
    /**
     * Set template values with validation and logging
     */
    public function setTemplateValues(TemplateProcessor $processor, array $values, bool $logMissing = false): array
    {
        $missingPlaceholders = [];
        
        foreach ($values as $key => $value) {
            try {
                // Convert to string and sanitize for XML compatibility (removes invalid XML chars)
                $sanitizedValue = $this->sanitizeForXml((string)($value ?? ''));
                
                // CRITICAL FIX: PHPWord's TemplateProcessor does a simple string replacement
                // and does NOT escape XML entities. We must escape them ourselves.
                // Decode any existing entities first to avoid double-escaping, then escape properly
                $sanitizedValue = html_entity_decode($sanitizedValue, ENT_QUOTES | ENT_XML1, 'UTF-8');
                // Escape XML entities: & -> &amp;, < -> &lt;, > -> &gt;, " -> &quot;, ' -> &apos;
                $sanitizedValue = htmlspecialchars($sanitizedValue, ENT_XML1 | ENT_QUOTES, 'UTF-8', false);
                
                // Now pass the properly escaped value to PHPWord
                $processor->setValue($key, $sanitizedValue);
            } catch (\Exception $e) {
                // Check if it's an invalid character error
                $errorMessage = $e->getMessage();
                if (str_contains($errorMessage, 'invalid character') || 
                    str_contains($errorMessage, 'String contains') ||
                    str_contains($errorMessage, 'XML')) {
                    Log::error('Invalid character in template value - attempting aggressive sanitization', [
                        'placeholder' => $key,
                        'original_value' => $value,
                        'sanitized_length' => strlen($sanitizedValue ?? ''),
                        'error' => $errorMessage
                    ]);
                    
                    // Try aggressive sanitization: remove all non-printable characters
                    $aggressiveSanitized = preg_replace('/[^\x20-\x7E\x0A\x0D\x09]/', '', (string)($value ?? ''));
                    $aggressiveSanitized = mb_convert_encoding($aggressiveSanitized, 'UTF-8', 'UTF-8');
                    
                    try {
                        $processor->setValue($key, $aggressiveSanitized);
                        Log::warning('Template value set with aggressive sanitization', [
                            'placeholder' => $key,
                            'original_length' => strlen((string)($value ?? '')),
                            'sanitized_length' => strlen($aggressiveSanitized)
                        ]);
                    } catch (\Exception $e2) {
                        // Last resort: use empty string
                        try {
                            $processor->setValue($key, '');
                            Log::warning('Template value set to empty string due to invalid characters', [
                                'placeholder' => $key
                            ]);
                        } catch (\Exception $e3) {
                            $missingPlaceholders[] = $key;
                        }
                    }
                } else {
                    // Placeholder doesn't exist in template or other error
                    $missingPlaceholders[] = $key;
                    
                    if ($logMissing) {
                        Log::debug('Template placeholder not found', [
                            'placeholder' => $key,
                            'value' => $value,
                            'error' => $errorMessage
                        ]);
                    }
                }
            }
        }
        
        // Log summary if there are missing placeholders
        if (!empty($missingPlaceholders) && $logMissing) {
            Log::info('Some template placeholders were not found', [
                'missing_count' => count($missingPlaceholders),
                'missing_placeholders' => $missingPlaceholders,
                'total_placeholders' => count($values)
            ]);
        }
        
        return $missingPlaceholders;
    }
    
    /**
     * Safely save template processor with error handling and XML validation
     * This wraps saveAs() to catch XML validation errors and verify the output file
     */
    public function safeSaveAs(TemplateProcessor $processor, string $outputPath): void
    {
        try {
            $processor->saveAs($outputPath);
            
            // Verify the file was created and is not empty
            if (!file_exists($outputPath) || filesize($outputPath) === 0) {
                throw new \RuntimeException("Generated DOCX file is empty or does not exist at: {$outputPath}");
            }
            
            // Validate XML structure inside the DOCX (it's a ZIP file)
            $zip = new \ZipArchive();
            $result = $zip->open($outputPath, \ZipArchive::CHECKCONS);
            if ($result !== true) {
                $zip->close();
                throw new \RuntimeException(
                    "Generated DOCX file is corrupted (invalid ZIP structure). " .
                    "ZipArchive error code: {$result}"
                );
            }
            
            // Validate the main document XML
            $documentXml = $zip->getFromName('word/document.xml');
            if ($documentXml === false) {
                $zip->close();
                throw new \RuntimeException("Cannot read document.xml from generated DOCX file");
            }
            
            // Check for unescaped ampersands (common XML error)
            if (preg_match('/&(?!amp;|lt;|gt;|quot;|apos;|#x[0-9A-Fa-f]+;|#[0-9]+;)/', $documentXml)) {
                $zip->close();
                throw new \RuntimeException(
                    "Generated DOCX contains invalid XML (unescaped ampersand). " .
                    "This indicates XML entity escaping failed. Please check your form data."
                );
            }
            
            // Validate XML structure
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            if (!$dom->loadXML($documentXml)) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $zip->close();
                $errorMessages = array_map(fn($e) => trim($e->message), $errors);
                throw new \RuntimeException(
                    "Generated DOCX contains invalid XML structure. " .
                    "Errors: " . implode('; ', array_slice($errorMessages, 0, 3))
                );
            }
            
            $zip->close();
            
        } catch (\RuntimeException $e) {
            // Re-throw our validation errors as-is
            throw $e;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            // Check if it's an invalid character error
            if (str_contains($errorMessage, 'invalid character') || 
                str_contains($errorMessage, 'String contains') ||
                str_contains($errorMessage, 'XML')) {
                
                Log::error('Invalid character detected during saveAs() - this indicates sanitization failed', [
                    'error' => $errorMessage,
                    'output_path' => $outputPath,
                    'exception_type' => get_class($e)
                ]);
                
                // Re-throw with a more helpful message
                throw new \RuntimeException(
                    'Document contains invalid characters that cannot be saved. ' .
                    'Please check your form data for special characters or encoding issues. ' .
                    'Original error: ' . $errorMessage
                );
            }
            
            // Re-throw other exceptions as-is
            throw $e;
        }
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

