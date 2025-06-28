<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

echo "Checking all templates...\n\n";

$templates = [
    'Incentive_Application_Form.docx',
    'Recommendation_Letter_Form.docx', 
    'Terminal_Report_Form.docx'
];

foreach ($templates as $template) {
    echo "=== $template ===\n";
    try {
        $templatePath = __DIR__ . '/storage/app/templates/' . $template;
        echo "Template exists: " . (file_exists($templatePath) ? 'Yes' : 'No') . "\n";
        
        if (file_exists($templatePath)) {
            $templateProcessor = new TemplateProcessor($templatePath);
            $variables = $templateProcessor->getVariables();
            
            echo "Variables found:\n";
            if (empty($variables)) {
                echo "  NO VARIABLES FOUND!\n";
            } else {
                foreach ($variables as $variable) {
                    echo "  - $variable\n";
                }
            }
            echo "Total: " . count($variables) . " variables\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
} 