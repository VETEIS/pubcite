<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProgressController extends Controller
{
    public function streamProgress(Request $request)
    {
        // Set headers for Server-Sent Events
        $response = response()->stream(function () use ($request) {
            $requestId = $request->get('request_id');
            $type = $request->get('type', 'publication'); // publication or citation
            
            // Send initial connection message
            echo "data: " . json_encode([
                'type' => 'connected',
                'message' => 'Connected to progress stream'
            ]) . "\n\n";
            
            // Flush the output buffer
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
            
            // Monitor log file for new entries
            $logFile = storage_path('logs/laravel.log');
            $lastPosition = 0;
            
            // Get initial file size
            if (file_exists($logFile)) {
                $lastPosition = filesize($logFile);
            }
            
            $timeout = 30; // 30 second timeout
            $startTime = time();
            
            while ((time() - $startTime) < $timeout) {
                if (file_exists($logFile)) {
                    $currentSize = filesize($logFile);
                    
                    if ($currentSize > $lastPosition) {
                        // Read new content
                        $handle = fopen($logFile, 'r');
                        fseek($handle, $lastPosition);
                        $newContent = fread($handle, $currentSize - $lastPosition);
                        fclose($handle);
                        
                        // Parse log entries and send relevant ones
                        $lines = explode("\n", $newContent);
                        foreach ($lines as $line) {
                            if (empty(trim($line))) continue;
                            
                            // Look for our specific log messages
                            if (strpos($line, 'Publication request submission started') !== false ||
                                strpos($line, 'Citation request submission started') !== false ||
                                strpos($line, 'Processing file uploads') !== false ||
                                strpos($line, 'Processing file upload') !== false ||
                                strpos($line, 'Moving pre-generated DOCX files') !== false ||
                                strpos($line, 'Creating admin notifications') !== false ||
                                strpos($line, 'Email notifications queued successfully') !== false ||
                                strpos($line, 'request submitted successfully') !== false ||
                                strpos($line, 'DOCX generation - Received data') !== false ||
                                strpos($line, 'Filtered data for') !== false ||
                                strpos($line, 'DOCX generated and found, ready to serve') !== false) {
                                
                                // Extract the log message
                                $message = $this->extractLogMessage($line);
                                if ($message) {
                                    echo "data: " . json_encode([
                                        'type' => 'progress',
                                        'message' => $message
                                    ]) . "\n\n";
                                    
                                    if (ob_get_level()) {
                                        ob_flush();
                                    }
                                    flush();
                                }
                            }
                        }
                        
                        $lastPosition = $currentSize;
                    }
                }
                
                // Check if request is complete
                if (strpos($newContent ?? '', 'request submitted successfully') !== false) {
                    echo "data: " . json_encode([
                        'type' => 'complete',
                        'message' => 'Request submitted successfully'
                    ]) . "\n\n";
                    
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                    break;
                }
                
                // Sleep for a short time before checking again
                usleep(500000); // 0.5 seconds
            }
            
            // Send timeout message if we exit the loop
            if ((time() - $startTime) >= $timeout) {
                echo "data: " . json_encode([
                    'type' => 'timeout',
                    'message' => 'Progress monitoring timed out'
                ]) . "\n\n";
                
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }
            
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Cache-Control'
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
