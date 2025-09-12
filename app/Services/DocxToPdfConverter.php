<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocxToPdfConverter
{
    /**
     * Convert a DOCX file to PDF using pandoc
     *
     * @param string $docxPath Relative path to the DOCX file
     * @param string $outputDir Directory where PDF should be saved
     * @return string|null Relative path to the generated PDF file, or null on failure
     */
    public function convertDocxToPdf(string $docxPath, string $outputDir): ?string
    {
        try {
            $fullDocxPath = Storage::disk('local')->path($docxPath);
            $fullOutputDir = Storage::disk('local')->path($outputDir);
            
            // Create output directory if it doesn't exist
            if (!is_dir($fullOutputDir)) {
                mkdir($fullOutputDir, 0755, true);
            }
            
            $outputPath = $fullOutputDir . '/' . pathinfo(basename($docxPath), PATHINFO_FILENAME) . '.pdf';
            
            // Check if pandoc is available
            if (!$this->isPandocAvailable()) {
                Log::error('Pandoc is not available for DOCX to PDF conversion', [
                    'docx_path' => $docxPath,
                    'output_dir' => $outputDir,
                    'environment' => app()->environment()
                ]);
                throw new \Exception('Pandoc is not available. Please ensure it is installed and accessible from command line.');
            }
            
            // Use pandoc to convert DOCX to PDF
            $command = "pandoc \"$fullDocxPath\" -o \"$outputPath\" --pdf-engine=pdflatex 2>&1";
            $output = shell_exec($command);
            
            Log::info('Pandoc DOCX to PDF conversion', [
                'docx_path' => $docxPath,
                'output_dir' => $outputDir,
                'command' => $command,
                'output' => $output
            ]);

            if (!file_exists($outputPath)) {
                throw new \Exception('PDF conversion failed - output file not created. Pandoc output: ' . $output);
            }
            
            // Return the relative path for storage
            $relativePath = $outputDir . '/' . basename($outputPath);
            
            Log::info('DOCX converted to PDF successfully', [
                'original_docx' => $docxPath,
                'generated_pdf' => $relativePath
            ]);
            
            return $relativePath;
            
        } catch (\Exception $e) {
            Log::error('Error converting DOCX to PDF', [
                'docx_path' => $docxPath,
                'output_dir' => $outputDir,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if pandoc is available on the system
     */
    private function isPandocAvailable(): bool
    {
        $pandocPath = $this->getPandocPath();
        if (!$pandocPath) {
            return false;
        }
        
        // Test if pandoc can run
        $testCommand = "\"$pandocPath\" --version 2>&1";
        $output = shell_exec($testCommand);
        
        return strpos($output, 'pandoc') !== false;
    }

    /**
     * Get the path to pandoc executable
     */
    private function getPandocPath(): ?string
    {
        // Common pandoc installation paths
        $possiblePaths = [
            // Windows paths
            'C:\\Program Files\\Pandoc\\pandoc.exe',
            'C:\\Program Files (x86)\\Pandoc\\pandoc.exe',
            
            // Linux paths (including Docker/container environments)
            '/usr/bin/pandoc',
            '/usr/local/bin/pandoc',
            
            // macOS paths
            '/usr/local/bin/pandoc',
            '/opt/homebrew/bin/pandoc',
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                Log::info('Pandoc found at path', ['path' => $path]);
                return $path;
            }
        }
        
        // Try to find pandoc in PATH
        $whichCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $output = shell_exec("$whichCommand pandoc 2>&1");
        if ($output && !empty(trim($output))) {
            $path = trim($output);
            Log::info('Pandoc found in PATH', ['path' => $path]);
            return $path;
        }
        
        Log::warning('Pandoc not found in any expected paths', [
            'environment' => app()->environment(),
            'os_family' => PHP_OS_FAMILY
        ]);
        return null;
    }
}
