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
        
        $tempDirectories = [
            'temp',
            'uploads/temp',
            'public/temp'
        ];
        
        $deletedCount = 0;
        $totalSize = 0;
        
        foreach ($tempDirectories as $directory) {
            if (Storage::exists($directory)) {
                $files = Storage::files($directory);
                
                foreach ($files as $file) {
                    $lastModified = Storage::lastModified($file);
                    $fileAge = Carbon::createFromTimestamp($lastModified);
                    
                    // Delete files older than 24 hours
                    if ($fileAge->diffInHours(now()) > 24) {
                        $size = Storage::size($file);
                        Storage::delete($file);
                        $deletedCount++;
                        $totalSize += $size;
                        
                        $this->line("Deleted: {$file} (Size: " . $this->formatBytes($size) . ")");
                    }
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