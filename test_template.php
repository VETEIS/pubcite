<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

echo "Testing DOCX template processing...\n";

try {
    // Test 1: Check if template exists
    $templatePath = __DIR__ . '/storage/app/templates/Incentive_Application_Form.docx';
    echo "Template path: $templatePath\n";
    echo "Template exists: " . (file_exists($templatePath) ? 'Yes' : 'No') . "\n";
    echo "Template size: " . filesize($templatePath) . " bytes\n\n";
    
    // Test 2: Try to load the template
    echo "Loading template...\n";
    $phpWord = IOFactory::load($templatePath);
    echo "Template loaded successfully!\n\n";
    
    // Test 3: Check sections and elements
    $sections = $phpWord->getSections();
    echo "Number of sections: " . count($sections) . "\n";
    
    $placeholderCount = 0;
    
    foreach ($sections as $index => $section) {
        echo "Section $index elements: " . count($section->getElements()) . "\n";
        
        // Check all elements
        $elements = $section->getElements();
        foreach ($elements as $i => $element) {
            echo "  Element $i type: " . get_class($element) . "\n";
            
            if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                // Check TextRun elements
                $textElements = $element->getElements();
                foreach ($textElements as $j => $textElement) {
                    if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text = $textElement->getText();
                        if (!empty(trim($text))) {
                            echo "    Text $j: " . substr($text, 0, 100) . "\n";
                            
                            // Check for placeholders
                            if (preg_match('/\{\{.*?\}\}/', $text)) {
                                echo "    *** Contains placeholders! ***\n";
                                preg_match_all('/\{\{.*?\}\}/', $text, $matches);
                                echo "    Placeholders found: " . implode(', ', $matches[0]) . "\n";
                                $placeholderCount++;
                            }
                        }
                    }
                }
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                $text = $element->getText();
                if (!empty(trim($text))) {
                    echo "    Text: " . substr($text, 0, 100) . "\n";
                    
                    // Check for placeholders
                    if (preg_match('/\{\{.*?\}\}/', $text)) {
                        echo "    *** Contains placeholders! ***\n";
                        preg_match_all('/\{\{.*?\}\}/', $text, $matches);
                        echo "    Placeholders found: " . implode(', ', $matches[0]) . "\n";
                        $placeholderCount++;
                    }
                }
            }
        }
    }
    
    echo "\nTotal elements with placeholders found: $placeholderCount\n";
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 