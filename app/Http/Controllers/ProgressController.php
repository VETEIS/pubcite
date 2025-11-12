<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProgressController extends Controller
{
    public function streamProgress(Request $request)
    {
        // Increase execution time limit for this specific request
        set_time_limit(60);
        
        // Set headers for Server-Sent Events
        $response = response()->stream(function () use ($request) {
            $requestId = $request->get('request_id');
            $type = $request->get('type', 'publication'); // publication or citation
            
            // Send initial connection message
            echo "data: " . json_encode([
                'type' => 'connected',
                'message' => 'Connected to progress stream'
            ]) . "\n\n";
            
            // Flush the output buffer immediately
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();
            
            // Use a more efficient approach - check cache/status file instead of reading entire log
            $statusFile = storage_path("app/temp/progress_{$requestId}.json");
            $lastModified = 0;
            
            $timeout = 60; // Increased to 60 seconds
            $startTime = time();
            $newContent = '';
            
            while ((time() - $startTime) < $timeout) {
                // Check if status file exists and was modified
                if (file_exists($statusFile)) {
                    $currentModified = filemtime($statusFile);
                    if ($currentModified > $lastModified) {
                        $status = json_decode(file_get_contents($statusFile), true);
                        if ($status && isset($status['message'])) {
                            echo "data: " . json_encode([
                                'type' => $status['type'] ?? 'progress',
                                'message' => $status['message']
                            ]) . "\n\n";
                            
                            flush();
                            
                            if (($status['type'] ?? '') === 'complete') {
                                // Clean up status file
                                @unlink($statusFile);
                                break;
                            }
                        }
                        $lastModified = $currentModified;
                    }
                }
                
                // Fallback: Check log file (less frequently)
                if ((time() - $startTime) % 2 === 0) { // Every 2 seconds
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        $handle = @fopen($logFile, 'r');
                        if ($handle) {
                            // Read last few KB only
                            fseek($handle, -8192, SEEK_END);
                            $logContent = fread($handle, 8192);
                            fclose($handle);
                            
                            // Check for completion
                            if (strpos($logContent, 'request submitted successfully') !== false ||
                                strpos($logContent, 'DOCX generated and found, ready to serve') !== false) {
                                echo "data: " . json_encode([
                                    'type' => 'complete',
                                    'message' => 'Operation completed successfully'
                                ]) . "\n\n";
                                flush();
                                break;
                            }
                        }
                    }
                }
                
                // Sleep for a short time before checking again
                usleep(1000000); // 1 second (reduced frequency)
            }
            
            // Send timeout message if we exit the loop
            if ((time() - $startTime) >= $timeout) {
                echo "data: " . json_encode([
                    'type' => 'timeout',
                    'message' => 'Progress monitoring timed out'
                ]) . "\n\n";
                flush();
            }
            
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
        ]);
        
        return $response;
    }
    
    private function extractLogMessage($logLine)
    {
        // Extract the actual log message from Laravel log format
        // Format: [timestamp] local.INFO: message
        if (preg_match('/\[.*?\] .*?\.INFO: (.+)/', $logLine, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}
