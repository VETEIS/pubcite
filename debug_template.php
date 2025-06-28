<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

echo "Debugging TemplateProcessor...\n";

try {
    $templatePath = __DIR__ . '/storage/app/templates/Incentive_Application_Form.docx';
    echo "Template path: $templatePath\n";
    echo "Template exists: " . (file_exists($templatePath) ? 'Yes' : 'No') . "\n";
    echo "Template size: " . filesize($templatePath) . " bytes\n\n";
    
    // Load template with TemplateProcessor
    echo "Loading TemplateProcessor...\n";
    $templateProcessor = new TemplateProcessor($templatePath);
    echo "TemplateProcessor loaded successfully!\n\n";
    
    // Get all variables found in the template
    echo "Getting variables...\n";
    $variables = $templateProcessor->getVariables();
    echo "Variables found in template:\n";
    if (empty($variables)) {
        echo "  NO VARIABLES FOUND!\n";
    } else {
        foreach ($variables as $variable) {
            echo "  - $variable\n";
        }
    }
    
    echo "\nTotal variables found: " . count($variables) . "\n";
    
    // Try to set a test value
    echo "\nTesting setValue...\n";
    $templateProcessor->setValue('collegeheader', 'TEST COLLEGE');
    echo "setValue completed\n";
    
    // Try to save
    $testOutput = __DIR__ . '/test_output.docx';
    echo "Saving to: $testOutput\n";
    $templateProcessor->saveAs($testOutput);
    echo "Save completed\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} 