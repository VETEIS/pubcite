<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;

class TemplatePreloader
{
    /**
     * Cache duration in seconds (1 hour)
     */
    private const CACHE_DURATION = 3600;
    
    /**
     * Template paths
     */
    private const TEMPLATES = [
        'incentive' => 'app/templates/Incentive_Application_Form.docx',
        'recommendation' => 'app/templates/Recommendation_Letter_Form.docx',
        'terminal' => 'app/templates/Terminal_Report_Form.docx',
        'cite_incentive' => 'app/templates/Cite_Incentive_Application.docx',
        'cite_recommendation' => 'app/templates/Cite_Recommendation_Letter.docx',
    ];
    
    /**
     * Preload all templates to warm up the system
     */
    public function preloadAllTemplates(): void
    {
        foreach (self::TEMPLATES as $type => $templatePath) {
            $this->preloadTemplate($type, $templatePath);
        }
    }
    
    /**
     * Preload a specific template
     */
    public function preloadTemplate(string $type, string $templatePath): bool
    {
        try {
            $fullPath = storage_path($templatePath);
            
            if (!file_exists($fullPath)) {
                Log::warning("Template file not found", ['type' => $type, 'path' => $fullPath]);
                return false;
            }
            
            // Check if template is already cached and valid
            $cacheKey = "template_info_{$type}";
            $cachedInfo = Cache::get($cacheKey);
            
            if ($cachedInfo && $this->isTemplateValid($fullPath, $cachedInfo)) {
                Log::info("Template already cached and valid", ['type' => $type]);
                return true;
            }
            
            // Load template to warm up PHPWord
            $startTime = microtime(true);
            $templateProcessor = new TemplateProcessor($fullPath);
            $loadTime = microtime(true) - $startTime;
            
            // Cache template info
            $templateInfo = [
                'path' => $fullPath,
                'size' => filesize($fullPath),
                'mtime' => filemtime($fullPath),
                'load_time' => $loadTime,
                'preloaded_at' => now()->toISOString()
            ];
            
            Cache::put($cacheKey, $templateInfo, self::CACHE_DURATION);
            
            Log::info("Template preloaded successfully", [
                'type' => $type,
                'load_time' => round($loadTime * 1000, 2) . 'ms',
                'size' => $templateInfo['size']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to preload template", [
                'type' => $type,
                'path' => $templatePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Check if cached template info is still valid
     */
    private function isTemplateValid(string $templatePath, array $cachedInfo): bool
    {
        if (!file_exists($templatePath)) {
            return false;
        }
        
        $currentSize = filesize($templatePath);
        $currentMtime = filemtime($templatePath);
        
        return $currentSize === $cachedInfo['size'] && 
               $currentMtime === $cachedInfo['mtime'];
    }
    
    /**
     * Get template load time from cache
     */
    public function getTemplateLoadTime(string $type): ?float
    {
        $cacheKey = "template_info_{$type}";
        $cachedInfo = Cache::get($cacheKey);
        
        return $cachedInfo['load_time'] ?? null;
    }
    
    /**
     * Clear template cache
     */
    public function clearTemplateCache(string $type = null): void
    {
        if ($type) {
            $cacheKey = "template_info_{$type}";
            Cache::forget($cacheKey);
        } else {
            foreach (array_keys(self::TEMPLATES) as $templateType) {
                $cacheKey = "template_info_{$templateType}";
                Cache::forget($cacheKey);
            }
        }
    }
}
