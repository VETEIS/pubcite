<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupOrphanedTempFiles extends Command
{
    protected $signature = 'cleanup:orphaned-temp-files {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up orphaned temporary files (*.tmp.*) from document generation';

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
        $maxAge = 3600; // 1 hour in seconds
        
        // Search in requests directories for .tmp.* files
        $this->info('Searching for orphaned .tmp.* files in requests directories...');
        
        $requestDirs = Storage::disk('local')->directories('requests');
        $this->info("Found " . count($requestDirs) . " request directories to scan");
        
        foreach ($requestDirs as $requestDir) {
            $files = Storage::disk('local')->files($requestDir);
            
            foreach ($files as $file) {
                $filename = basename($file);
                
                // Check if file matches .tmp.* pattern
                if (preg_match('/\.tmp\./', $filename)) {
                    $lastModified = Storage::disk('local')->lastModified($file);
                    $fileAge = time() - $lastModified;
                    
                    // Delete files older than maxAge
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
            $this->info("Files that would be deleted: {$wouldDeleteCount}");
        } else {
            $this->info("Files deleted: {$deletedCount}");
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

