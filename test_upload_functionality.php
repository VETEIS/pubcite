<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing upload functionality...\n";
    
    // Check for endorsed requests with their file data
    $requests = App\Models\Request::where('status', 'endorsed')->get();
    echo "Found " . $requests->count() . " endorsed requests\n";
    
    foreach($requests as $request) {
        echo "\nRequest: " . $request->request_code . "\n";
        $pdfPathData = json_decode($request->pdf_path, true);
        
        if ($pdfPathData) {
            echo "  PDF files:\n";
            if (isset($pdfPathData['pdfs'])) {
                foreach($pdfPathData['pdfs'] as $key => $fileData) {
                    if (is_array($fileData)) {
                        echo "    Key: $key, Original: " . ($fileData['original_name'] ?? 'N/A') . ", Path: " . basename($fileData['path']) . "\n";
                    } else {
                        echo "    Key: $key, Path: " . basename($fileData) . "\n";
                    }
                }
            }
            
            echo "  DOCX files:\n";
            if (isset($pdfPathData['docxs'])) {
                foreach($pdfPathData['docxs'] as $key => $filePath) {
                    echo "    Key: $key, Path: " . basename($filePath) . "\n";
                }
            }
        } else {
            echo "  No pdf_path data found\n";
        }
    }
    
    // Test the findMatchingKeyInPdfPath method
    echo "\nTesting file matching logic...\n";
    $controller = new App\Http\Controllers\SigningController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('findMatchingKeyInPdfPath');
    $method->setAccessible(true);
    
    if ($requests->count() > 0) {
        $request = $requests->first();
        $pdfPathData = json_decode($request->pdf_path, true);
        
        if ($pdfPathData && isset($pdfPathData['pdfs'])) {
            foreach($pdfPathData['pdfs'] as $key => $fileData) {
                $originalName = is_array($fileData) ? ($fileData['original_name'] ?? basename($fileData['path'])) : basename($fileData);
                $extension = 'pdf';
                
                echo "Testing match for: $originalName\n";
                $matchingKey = $method->invoke($controller, $pdfPathData, $originalName, $extension);
                echo "  Matching key: " . ($matchingKey ?: 'NOT FOUND') . "\n";
                break; // Test just one
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
