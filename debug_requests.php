<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Requests with Files ===\n";

$requests = \App\Models\Request::whereNotNull('pdf_path')->get(['id', 'pdf_path']);

foreach ($requests as $request) {
    echo "Request ID: " . $request->id . "\n";
    echo "PDF Path: " . substr($request->pdf_path, 0, 200) . "...\n";
    
    $paths = json_decode($request->pdf_path, true);
    if ($paths) {
        echo "PDFs count: " . (isset($paths['pdfs']) ? count($paths['pdfs']) : 0) . "\n";
        echo "DOCXs count: " . (isset($paths['docxs']) ? count($paths['docxs']) : 0) . "\n";
        
        if (isset($paths['pdfs'])) {
            foreach ($paths['pdfs'] as $key => $fileInfo) {
                $filePath = storage_path('app/' . $fileInfo['path']);
                $filePathPublic = storage_path('app/public/' . $fileInfo['path']);
                echo "  PDF $key: " . $filePath . " (exists: " . (file_exists($filePath) ? 'YES' : 'NO') . ")\n";
                echo "  PDF $key (public): " . $filePathPublic . " (exists: " . (file_exists($filePathPublic) ? 'YES' : 'NO') . ")\n";
            }
        }
        
        if (isset($paths['docxs'])) {
            foreach ($paths['docxs'] as $key => $storagePath) {
                $filePath = storage_path('app/' . $storagePath);
                echo "  DOCX $key: " . $filePath . " (exists: " . (file_exists($filePath) ? 'YES' : 'NO') . ")\n";
            }
        }
    }
    echo "---\n";
}

echo "=== End ===\n"; 