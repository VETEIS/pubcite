<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

// Function to extract placeholders from DOCX
function extractPlaceholders($templatePath) {
    if (!file_exists($templatePath)) {
        echo "Template not found: $templatePath\n";
        return [];
    }
    
    try {
        $templateProcessor = new TemplateProcessor($templatePath);
        $variables = $templateProcessor->getVariables();
        return $variables;
    } catch (Exception $e) {
        echo "Error processing template: " . $e->getMessage() . "\n";
        return [];
    }
}

// Templates to check
$templates = [
    'Incentive_Application_Form.docx',
    'Recommendation_Letter_Form.docx', 
    'Terminal_Report_Form.docx'
];

echo "=== DOCX Template Placeholders Analysis ===\n\n";

foreach ($templates as $template) {
    $templatePath = __DIR__ . '/storage/app/templates/' . $template;
    echo "📄 $template:\n";
    
    $placeholders = extractPlaceholders($templatePath);
    
    if (empty($placeholders)) {
        echo "   No placeholders found or error occurred\n";
    } else {
        foreach ($placeholders as $placeholder) {
            echo "   - $placeholder\n";
        }
    }
    echo "\n";
}

echo "=== End Analysis ===\n"; 