<?php
require 'vendor/autoload.php';

echo "=== Citation Incentive Application Template Variables ===\n";
$template1 = new \PhpOffice\PhpWord\TemplateProcessor('storage/app/templates/Cite_Incentive_Application.docx');
$variables1 = $template1->getVariables();
print_r($variables1);

echo "\n=== Citation Recommendation Letter Template Variables ===\n";
$template2 = new \PhpOffice\PhpWord\TemplateProcessor('storage/app/templates/Cite_Recommendation_Letter.docx');
$variables2 = $template2->getVariables();
print_r($variables2);
?> 