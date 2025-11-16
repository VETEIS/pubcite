<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    protected $signature = 'cleanup:temp-files';
    protected $description = 'Clean up temporary files older than 24 hours';

    public function handle()
    {
        $this->info('Starting cleanup of temporary files...');
        
        // Standardized temp directories
        // Note: temp/docx_cache is handled by cleanup:preview-cache command
        $tempDirectories = [
            'temp', // storage/app/temp/ (ZIP files, progress files)
        ];
        
        // Also check public/temp for legacy files (will be migrated to temp/)
        if (Storage::disk('public')->exists('temp')) {
            $this->info('Found legacy public/temp directory. Consider migrating files to storage/app/temp/');
        }
        
        $deletedCount = 0;
        $totalSize = 0;
        
        foreach ($tempDirectories as $directory) {
            // Use local disk for temp directory (storage/app/temp/)
            if (Storage::disk('local')->exists($directory)) {
                $files = Storage::disk('local')->files($directory);
                
                foreach ($files as $file) {
                    $lastModified = Storage::disk('local')->lastModified($file);
                    $fileAge = Carbon::createFromTimestamp($lastModified);
                    
                    // Delete files older than 24 hours
                    if ($fileAge->diffInHours(now()) > 24) {
                        $size = Storage::disk('local')->size($file);
                        Storage::disk('local')->delete($file);
                        $deletedCount++;
                        $totalSize += $size;
                        
                        $this->line("Deleted: {$file} (Size: " . $this->formatBytes($size) . ")");
                    }
                }
            }
        }
        
        // Also check legacy public/temp directory if it exists
        if (Storage::disk('public')->exists('temp')) {
            $this->warn('Found legacy public/temp directory. Files should be migrated to storage/app/temp/');
            $publicFiles = Storage::disk('public')->files('temp');
            
            foreach ($publicFiles as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);
                $fileAge = Carbon::createFromTimestamp($lastModified);
                
                // Delete files older than 24 hours
                if ($fileAge->diffInHours(now()) > 24) {
                    $size = Storage::disk('public')->size($file);
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                    $totalSize += $size;
                    
                    $this->line("Deleted (legacy): {$file} (Size: " . $this->formatBytes($size) . ")");
                }
            }
        }
        
        $this->info("Cleanup completed!");
        $this->info("Files deleted: {$deletedCount}");
        $this->info("Total space freed: " . $this->formatBytes($totalSize));
        
        return 0;
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
} 