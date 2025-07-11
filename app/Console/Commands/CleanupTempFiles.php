<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:temp-files {--hours=24 : Hours after which to delete temp files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary DOCX files older than specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("Cleaning up temp files older than {$hours} hours...");
        
        $tempPath = 'temp';
        $deletedCount = 0;
        
        if (Storage::exists($tempPath)) {
            $directories = Storage::directories($tempPath);
            
            foreach ($directories as $userDir) {
                $userSubDirs = Storage::directories($userDir);
                
                foreach ($userSubDirs as $tempDir) {
                    $dirName = basename($tempDir);
                    
                    // Extract timestamp from directory name (format: preview_timestamp_random)
                    if (preg_match('/^preview_(\d+)_/', $dirName, $matches)) {
                        $timestamp = (int) $matches[1];
                        $dirTime = Carbon::createFromTimestamp($timestamp);
                        
                        if ($dirTime->lt($cutoffTime)) {
                            Storage::deleteDirectory($tempDir);
                            $deletedCount++;
                            $this->line("Deleted: {$tempDir}");
                        }
                    }
                }
            }
        }
        
        $this->info("Cleanup completed. Deleted {$deletedCount} temp directories.");
        
        return 0;
    }
} 