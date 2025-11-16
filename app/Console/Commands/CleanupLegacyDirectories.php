<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupLegacyDirectories extends Command
{
    protected $signature = 'cleanup:legacy-directories {--dry-run : Show what would be deleted without actually deleting} {--force : Force deletion without confirmation}';
    protected $description = 'Clean up legacy directories that are no longer used (e.g., storage/app/public/temp/)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No directories will be deleted');
        } else {
            $this->info('🧹 CLEANUP MODE - Directories will be permanently deleted');
        }
        
        $this->newLine();
        
        $legacyDirs = [];
        $totalSize = 0;
        
        // Check for legacy public/temp directory
        if (Storage::disk('public')->exists('temp')) {
            $files = Storage::disk('public')->allFiles('temp');
            $dirSize = 0;
            foreach ($files as $file) {
                $dirSize += Storage::disk('public')->size($file);
            }
            
            $legacyDirs[] = [
                'path' => 'storage/app/public/temp/',
                'storage_path' => 'temp',
                'disk' => 'public',
                'files_count' => count($files),
                'size' => $dirSize,
                'reason' => 'No longer used - PublicationsController now uses storage/app/temp/'
            ];
            $totalSize += $dirSize;
        }
        
        // Check for other potentially unused directories
        // Add more checks here as needed
        
        if (empty($legacyDirs)) {
            $this->info('✅ No legacy directories found. System is clean!');
            return 0;
        }
        
        $this->info('📋 Found ' . count($legacyDirs) . ' legacy directory(ies):');
        $this->newLine();
        
        foreach ($legacyDirs as $dir) {
            $this->warn("Directory: {$dir['path']}");
            $this->line("  Files: {$dir['files_count']}");
            $this->line("  Size: " . $this->formatBytes($dir['size']));
            $this->line("  Reason: {$dir['reason']}");
            $this->newLine();
        }
        
        $this->info("Total size: " . $this->formatBytes($totalSize));
        $this->newLine();
        
        if ($isDryRun) {
            $this->info("🔍 Dry run complete. Run without --dry-run to actually delete these directories.");
            return 0;
        }
        
        // Confirm deletion
        if (!$force && !$this->confirm('Are you sure you want to delete these legacy directories? This cannot be undone!', false)) {
            $this->info('Cleanup cancelled.');
            return 0;
        }
        
        // Delete legacy directories
        $deletedCount = 0;
        $freedSize = 0;
        
        foreach ($legacyDirs as $dir) {
            try {
                Storage::disk($dir['disk'])->deleteDirectory($dir['storage_path']);
                $deletedCount++;
                $freedSize += $dir['size'];
                $this->info("✅ Deleted: {$dir['path']}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to delete {$dir['path']}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("🎉 CLEANUP COMPLETE:");
        $this->info("Directories deleted: {$deletedCount}");
        $this->info("Space freed: " . $this->formatBytes($freedSize));
        
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
}

