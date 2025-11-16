<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupStaleLockFiles extends Command
{
    protected $signature = 'cleanup:stale-lock-files {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up stale .lock files from preview cache';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No files will be deleted');
        } else {
            $this->info('🧹 CLEANUP MODE - Files will be permanently deleted');
        }
        
        $this->newLine();
        
        $deletedCount = 0;
        $wouldDeleteCount = 0;
        $totalSize = 0;
        $maxAge = 300; // 5 minutes in seconds (matches DocumentGenerationService::LOCK_TIMEOUT)
        
        // Search in preview cache directory for .lock files
        $this->info('Searching for stale .lock files in preview cache...');
        
        $cacheBase = 'temp/docx_cache';
        
        if (!Storage::disk('local')->exists($cacheBase)) {
            $this->info("No preview cache directory found at {$cacheBase}");
            return 0;
        }
        
        $cacheDirs = Storage::disk('local')->directories($cacheBase);
        $this->info("Found " . count($cacheDirs) . " cache directories to scan");
        
        foreach ($cacheDirs as $cacheDir) {
            $files = Storage::disk('local')->files($cacheDir);
            
            foreach ($files as $file) {
                $filename = basename($file);
                
                // Check if file is a .lock file
                if (str_ends_with($filename, '.lock')) {
                    $lastModified = Storage::disk('local')->lastModified($file);
                    $fileAge = time() - $lastModified;
                    
                    // Delete locks older than maxAge
                    if ($fileAge > $maxAge) {
                        $size = Storage::disk('local')->size($file);
                        $totalSize += $size;
                        
                        if ($isDryRun) {
                            $wouldDeleteCount++;
                            $this->warn("Would delete: {$file} (Age: " . $this->formatAge($fileAge) . ", Size: " . $this->formatBytes($size) . ")");
                        } else {
                            try {
                                Storage::disk('local')->delete($file);
                                $deletedCount++;
                                $this->info("✅ Deleted: {$file}");
                            } catch (\Exception $e) {
                                $this->error("❌ Failed to delete {$file}: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }
        
        $this->newLine();
        $this->info("📊 SUMMARY:");
        if ($isDryRun) {
            $this->info("Lock files that would be deleted: {$wouldDeleteCount}");
        } else {
            $this->info("Lock files deleted: {$deletedCount}");
        }
        $this->info("Total size: " . $this->formatBytes($totalSize));
        
        if ($isDryRun) {
            $this->info("🔍 Dry run complete. Run without --dry-run to actually delete these files.");
        }
        
        return 0;
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function formatAge($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        } elseif ($seconds < 3600) {
            return round($seconds / 60, 1) . ' minutes';
        } else {
            return round($seconds / 3600, 1) . ' hours';
        }
    }
}

