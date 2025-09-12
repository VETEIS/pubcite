<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Request;

class CleanupOrphanedDirectories extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cleanup:orphaned-directories {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up orphaned request directories that are no longer referenced in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No files will be deleted');
        } else {
            $this->info('🧹 CLEANUP MODE - Files will be permanently deleted');
        }
        
        $this->newLine();
        
        // Get all request directories from storage
        $requestDirs = Storage::disk('local')->directories('requests');
        $this->info("Found " . count($requestDirs) . " request directories in storage");
        
        // Get all valid request codes from database
        $validRequestCodes = Request::pluck('request_code')->toArray();
        $this->info("Found " . count($validRequestCodes) . " valid request codes in database");
        
        $orphanedDirs = [];
        $totalSize = 0;
        
        foreach ($requestDirs as $requestDir) {
            // Extract user ID and request code from path
            $pathParts = explode('/', $requestDir);
            if (count($pathParts) >= 3) {
                $userId = $pathParts[1];
                $requestCode = $pathParts[2];
                
                // Check if this request code exists in database
                if (!in_array($requestCode, $validRequestCodes)) {
                    $orphanedDirs[] = $requestDir;
                    
                    // Calculate directory size
                    $files = Storage::disk('local')->allFiles($requestDir);
                    $dirSize = 0;
                    foreach ($files as $file) {
                        $dirSize += Storage::disk('local')->size($file);
                    }
                    $totalSize += $dirSize;
                    
                    $this->warn("Orphaned directory: {$requestDir} (Size: " . $this->formatBytes($dirSize) . ")");
                }
            }
        }
        
        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("Orphaned directories: " . count($orphanedDirs));
        $this->info("Total size to be freed: " . $this->formatBytes($totalSize));
        
        if (count($orphanedDirs) === 0) {
            $this->info("✅ No orphaned directories found!");
            return 0;
        }
        
        if ($isDryRun) {
            $this->info("🔍 Dry run complete. Use --no-dry-run to actually delete these directories.");
            return 0;
        }
        
        // Confirm deletion
        if (!$this->confirm("Are you sure you want to delete " . count($orphanedDirs) . " orphaned directories?")) {
            $this->info("Cleanup cancelled.");
            return 0;
        }
        
        // Delete orphaned directories
        $deletedCount = 0;
        $freedSize = 0;
        
        foreach ($orphanedDirs as $dir) {
            try {
                // Calculate size before deletion
                $files = Storage::disk('local')->allFiles($dir);
                $dirSize = 0;
                foreach ($files as $file) {
                    $dirSize += Storage::disk('local')->size($file);
                }
                
                // Delete directory
                Storage::disk('local')->deleteDirectory($dir);
                $deletedCount++;
                $freedSize += $dirSize;
                
                $this->info("✅ Deleted: {$dir}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to delete {$dir}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("🎉 CLEANUP COMPLETE:");
        $this->info("Directories deleted: {$deletedCount}");
        $this->info("Space freed: " . $this->formatBytes($freedSize));
        
        return 0;
    }
    
    /**
     * Format bytes into human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
