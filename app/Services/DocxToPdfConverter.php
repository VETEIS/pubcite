<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocxToPdfConverter
{
    /**
     * Convert a DOCX file to PDF using LibreOffice
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
            
            // Check if LibreOffice is available
            if (!$this->isLibreOfficeAvailable()) {
                Log::error('LibreOffice is not available for DOCX to PDF conversion', [
                    'docx_path' => $docxPath,
                    'output_dir' => $outputDir,
                    'environment' => app()->environment()
                ]);
                throw new \Exception('LibreOffice is not available. Please ensure it is installed and accessible from command line.');
            }
            
            // Use LibreOffice or unoconv to convert DOCX to PDF
            $converterPath = $this->getLibreOfficePath();
            if (!$converterPath) {
                throw new \Exception('Document converter not found (LibreOffice or unoconv)');
            }
            
            // Check if we're using unoconv or LibreOffice
            if (strpos($converterPath, 'unoconv') !== false) {
                // Use unoconv (more reliable in Docker)
                $command = "unoconv -f pdf -o \"$outputPath\" \"$fullDocxPath\" 2>&1";
            } else {
                // Use LibreOffice with silent flags
                $command = "\"$converterPath\" --headless --invisible --nocrashreport --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir \"$fullOutputDir\" \"$fullDocxPath\" 2>&1";
            }
            
            $output = shell_exec($command);
            
            Log::info('LibreOffice DOCX to PDF conversion', [
                'docx_path' => $docxPath,
                'output_dir' => $outputDir,
                'command' => $command,
                'output' => $output
            ]);

            if (!file_exists($outputPath)) {
                throw new \Exception('PDF conversion failed - output file not created. LibreOffice output: ' . $output);
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
     * Check if LibreOffice or unoconv is available on the system
     */
    private function isLibreOfficeAvailable(): bool
    {
        $converterPath = $this->getLibreOfficePath();
        if (!$converterPath) {
            return false;
        }
        
        // Test if converter can run
        if (strpos($converterPath, 'unoconv') !== false) {
            // Test unoconv
            $testCommand = "unoconv --version 2>&1";
            $output = shell_exec($testCommand);
            return strpos($output, 'unoconv') !== false;
        } else {
            // Test LibreOffice
            $testCommand = "\"$converterPath\" --headless --invisible --nocrashreport --nodefault --nolockcheck --nologo --norestore --version 2>&1";
            $output = shell_exec($testCommand);
            return strpos($output, 'LibreOffice') !== false;
        }
    }

    /**
     * Get the path to LibreOffice executable or unoconv
     */
    private function getLibreOfficePath(): ?string
    {
        // Common LibreOffice and unoconv installation paths
        $possiblePaths = [
            // unoconv (preferred for Docker)
            '/usr/bin/unoconv',
            '/usr/local/bin/unoconv',
            
            // LibreOffice paths
            // Windows paths
            'C:\\Program Files\\LibreOffice\\program\\soffice.com',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.com',
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            
            // Linux paths (including Docker/container environments)
            '/usr/bin/libreoffice',
            '/usr/local/bin/libreoffice',
            '/opt/libreoffice/program/soffice',
            '/usr/bin/soffice',
            '/usr/local/bin/soffice',
            
            // macOS paths
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                Log::info('LibreOffice found at path', ['path' => $path]);
                return $path;
            }
        }
        
        // Try to find unoconv in PATH first (preferred for Docker)
        $whichCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $output = shell_exec("$whichCommand unoconv 2>&1");
        if ($output && !empty(trim($output))) {
            $path = trim($output);
            Log::info('unoconv found in PATH', ['path' => $path]);
            return $path;
        }
        
        // Try to find LibreOffice in PATH
        $output = shell_exec("$whichCommand soffice 2>&1");
        if ($output && !empty(trim($output))) {
            $path = trim($output);
            Log::info('LibreOffice found in PATH', ['path' => $path]);
            return $path;
        }
        
        // Try to find libreoffice command in PATH
        $output = shell_exec("$whichCommand libreoffice 2>&1");
        if ($output && !empty(trim($output))) {
            $path = trim($output);
            Log::info('LibreOffice found in PATH (libreoffice command)', ['path' => $path]);
            return $path;
        }
        
        Log::warning('LibreOffice not found in any expected paths', [
            'environment' => app()->environment(),
            'os_family' => PHP_OS_FAMILY
        ]);
        return null;
    }
}
