<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupPreviewCache extends Command
{
    protected $signature = 'cleanup:preview-cache {--days=7 : Number of days to keep cache entries} {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up old preview cache entries (older than specified days)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $daysToKeep = (int) $this->option('days');
        $maxAge = $daysToKeep * 86400; // Convert days to seconds
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No files will be deleted');
        } else {
            $this->info('🧹 CLEANUP MODE - Files will be permanently deleted');
        }
        
        $this->info("Keeping cache entries newer than {$daysToKeep} days");
        $this->newLine();
        
        $deletedDirs = 0;
        $deletedFiles = 0;
        $totalSize = 0;
        
        // Search in preview cache directory
        $this->info('Searching for old preview cache entries...');
        
        $cacheBase = 'temp/docx_cache';
        
        if (!Storage::disk('local')->exists($cacheBase)) {
            $this->info("No preview cache directory found at {$cacheBase}");
            return 0;
        }
        
        $cacheDirs = Storage::disk('local')->directories($cacheBase);
        $this->info("Found " . count($cacheDirs) . " cache directories to scan");
        
        foreach ($cacheDirs as $cacheDir) {
            $files = Storage::disk('local')->files($cacheDir);
            
            if (empty($files)) {
                // Empty directory, can be deleted
                if ($isDryRun) {
                    $this->warn("Would delete empty directory: {$cacheDir}");
                } else {
                    try {
                        Storage::disk('local')->deleteDirectory($cacheDir);
                        $deletedDirs++;
                        $this->info("✅ Deleted empty directory: {$cacheDir}");
                    } catch (\Exception $e) {
                        $this->error("❌ Failed to delete {$cacheDir}: " . $e->getMessage());
                    }
                }
                continue;
            }
            
            // Check the oldest file in the directory
            $oldestFile = null;
            $oldestTime = time();
            
            foreach ($files as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                if ($lastModified < $oldestTime) {
                    $oldestTime = $lastModified;
                    $oldestFile = $file;
                }
            }
            
            $dirAge = time() - $oldestTime;
            
            // Delete directory if oldest file is older than maxAge
            if ($dirAge > $maxAge) {
                // Calculate directory size
                $dirSize = 0;
                foreach ($files as $file) {
                    $dirSize += Storage::disk('local')->size($file);
                }
                $totalSize += $dirSize;
                
                if ($isDryRun) {
                    $this->warn("Would delete cache directory: {$cacheDir} (Age: " . $this->formatAge($dirAge) . ", Size: " . $this->formatBytes($dirSize) . ", Files: " . count($files) . ")");
                } else {
                    try {
                        Storage::disk('local')->deleteDirectory($cacheDir);
                        $deletedDirs++;
                        $deletedFiles += count($files);
                        $this->info("✅ Deleted cache directory: {$cacheDir} (" . count($files) . " files)");
                    } catch (\Exception $e) {
                        $this->error("❌ Failed to delete {$cacheDir}: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->newLine();
        $this->info("📊 SUMMARY:");
        if ($isDryRun) {
            $this->info("Cache directories that would be deleted: {$deletedDirs}");
            $this->info("Cache files that would be deleted: {$deletedFiles}");
        } else {
            $this->info("Cache directories deleted: {$deletedDirs}");
            $this->info("Cache files deleted: {$deletedFiles}");
        }
        $this->info("Total size freed: " . $this->formatBytes($totalSize));
        
        if ($isDryRun) {
            $this->info("🔍 Dry run complete. Run without --dry-run to actually delete these directories.");
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
        if ($seconds < 3600) {
            return round($seconds / 60, 1) . ' minutes';
        } elseif ($seconds < 86400) {
            return round($seconds / 3600, 1) . ' hours';
        } else {
            return round($seconds / 86400, 1) . ' days';
        }
    }
}

