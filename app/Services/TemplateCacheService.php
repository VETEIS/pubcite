<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TemplateCacheService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    private const CACHE_DURATION = 3600;
    
    /**
     * Get cached template processor or create new one
     */
    public function getTemplateProcessor(string $templatePath): TemplateProcessor
    {
        $cacheKey = 'template_processor_' . md5($templatePath);
        
        // Check if template processor is cached
        $cachedProcessor = Cache::get($cacheKey);
        
        if ($cachedProcessor && $this->isTemplateValid($templatePath, $cachedProcessor)) {
            Log::info('Template processor loaded from cache', ['template' => $templatePath]);
            return $cachedProcessor;
        }
        
        // Create new template processor
        Log::info('Creating new template processor', ['template' => $templatePath]);
        $processor = new TemplateProcessor($templatePath);
        
        // Cache the processor (serialize for storage)
        Cache::put($cacheKey, $processor, self::CACHE_DURATION);
        
        return $processor;
    }
    
    /**
     * Check if cached template processor is still valid
     */
    private function isTemplateValid(string $templatePath, TemplateProcessor $cachedProcessor): bool
    {
        // Check if template file still exists and hasn't been modified
        if (!file_exists($templatePath)) {
            return false;
        }
        
        // For now, we'll assume cached processors are valid
        // In a more sophisticated implementation, we could check file modification time
        return true;
    }
    
    /**
     * Clear template cache for a specific template
     */
    public function clearTemplateCache(string $templatePath): void
    {
        $cacheKey = 'template_processor_' . md5($templatePath);
        Cache::forget($cacheKey);
        Log::info('Template cache cleared', ['template' => $templatePath]);
    }
    
    /**
     * Clear all template caches
     */
    public function clearAllTemplateCaches(): void
    {
        // Get all cache keys that start with 'template_processor_'
        $keys = Cache::getRedis()->keys('*template_processor_*');
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        Log::info('All template caches cleared');
    }
    
    /**
     * Get template cache statistics
     */
    public function getCacheStats(): array
    {
        $keys = Cache::getRedis()->keys('*template_processor_*');
        
        return [
            'cached_templates' => count($keys),
            'cache_duration' => self::CACHE_DURATION,
            'templates' => array_map(function($key) {
                return str_replace('template_processor_', '', $key);
            }, $keys)
        ];
    }
}
