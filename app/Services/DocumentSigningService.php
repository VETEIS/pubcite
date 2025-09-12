<?php

namespace App\Services;

use App\Models\Request;
use App\Models\User;
use App\Models\Signature;
use App\Enums\SignatureStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;

class DocumentSigningService
{
    /**
     * Sign a document with the user's signature
     */
    public function signDocument(Request $request, User $user, Signature $signature): bool
    {
        try {
            // Get the document path
            $documentPath = $this->getDocumentPath($request);
            if (!$documentPath || !Storage::disk('local')->exists($documentPath)) {
                Log::error('Document not found for signing', ['request_id' => $request->id, 'path' => $documentPath]);
                return false;
            }

            // Create backup of current document
            $originalPath = $this->backupOriginalDocument($request, $documentPath);
            
            // Convert DOCX to PDF and overlay signature
            $signedDocumentPath = $this->convertAndOverlaySignature($request, $documentPath, $signature, $user);
            
            if (!$signedDocumentPath) {
                // Restore original if signing failed
                $this->restoreOriginalDocument($request, $originalPath);
                return false;
            }

            // Update request with signature information
            $this->updateRequestSignatureStatus($request, $user, $signedDocumentPath, $originalPath);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error signing document', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get the document path for the request
     */
    private function getDocumentPath(Request $request): ?string
    {
        $basePath = "requests/{$request->user_id}/{$request->request_code}";
        
        $possibleFiles = [
            'Incentive_Application_Form.docx',
            'Recommendation_Letter_Form.docx',
            'Terminal_Report_Form.docx'
        ];
        
        foreach ($possibleFiles as $filename) {
            $fullPath = $basePath . '/' . $filename;
            if (Storage::disk('local')->exists($fullPath)) {
                Log::info('Found document for signing', [
                    'request_id' => $request->id,
                    'path' => $fullPath
                ]);
                return $fullPath;
            }
        }
        
        Log::error('No DOCX document found for signing', [
            'request_id' => $request->id,
            'base_path' => $basePath
        ]);
        return null;
    }

    /**
     * Backup the current document before applying new signature
     */
    private function backupOriginalDocument(Request $request, string $documentPath): string
    {
        $originalFilename = basename($documentPath);
        $filenameWithoutExt = pathinfo($originalFilename, PATHINFO_FILENAME);
        $timestamp = time();
        $backupPath = "requests/{$request->user_id}/{$request->request_code}/backup.{$filenameWithoutExt}_{$timestamp}.docx";
        
        $sourcePath = Storage::disk('local')->path($documentPath);
        $backupFullPath = Storage::disk('local')->path($backupPath);
        
        $backupDir = dirname($backupFullPath);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        if (!copy($sourcePath, $backupFullPath)) {
            Log::error('Failed to create backup copy', [
                'request_id' => $request->id,
                'source' => $sourcePath,
                'backup' => $backupFullPath
            ]);
            throw new \Exception('Failed to create backup copy');
        }
        
        Log::info('Document backed up successfully before signing', [
            'request_id' => $request->id,
            'original_path' => $documentPath,
            'backup_path' => $backupPath
        ]);
        
        // Extract signatory coordinates immediately after backup is created
        $this->extractAndStoreCoordinatesIfNeeded($request, $backupPath);
        
        return $backupPath;
    }

    /**
     * Convert DOCX to PDF and overlay signature (BEST approach for layout preservation)
     */
    private function convertAndOverlaySignature(Request $request, string $documentPath, Signature $signature, User $user): ?string
    {
        try {
            Log::info('Processing signature overlay', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);

            // Convert DOCX to PDF first (preserves layout perfectly)
            $pdfPath = $this->convertDocxToPdf($documentPath, $request);
            if (!$pdfPath) {
                throw new \Exception('Failed to convert DOCX to PDF');
            }

            // Ensure coordinates are extracted from the produced PDF (pdftotext -bbox)
            $this->extractAndStorePdfCoordinatesIfNeeded($request, Storage::disk('local')->path($pdfPath));

            // Overlay signature on the PDF (maintains exact layout)
            $signedPdfPath = $this->overlaySignatureOnPdf($pdfPath, $signature, $request, $user);
            if (!$signedPdfPath) {
                throw new \Exception('Failed to overlay signature on PDF');
            }

            Log::info('Document converted and signature overlaid successfully', [
                'request_id' => $request->id,
                'original_docx' => $documentPath,
                'pdf_path' => $pdfPath,
                'signed_pdf' => $signedPdfPath
            ]);
            
            // Clean up intermediate files after successful signing
            $this->cleanupIntermediateFiles($request, $documentPath, $pdfPath);
            
            return $signedPdfPath;
            
        } catch (\Exception $e) {
            Log::error('Error converting and overlaying signature', [
                'request_id' => $request->id,
                'path' => $documentPath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Convert DOCX to PDF using LibreOffice (preserves layout perfectly)
     */
    private function convertDocxToPdf(string $docxPath, Request $request): ?string
    {
        try {
            $fullDocxPath = Storage::disk('local')->path($docxPath);
            $outputDir = Storage::disk('local')->path("requests/{$request->user_id}/{$request->request_code}");
            
            // Create output directory if it doesn't exist
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            
            $outputPath = $outputDir . '/' . pathinfo(basename($docxPath), PATHINFO_FILENAME) . '.pdf';
            
            // Check if LibreOffice is available
            if (!$this->isLibreOfficeAvailable()) {
                throw new \Exception('LibreOffice is not available. Please ensure it is installed and accessible from command line.');
            }
            
            // Use LibreOffice to convert DOCX to PDF (preserves layout perfectly)
            $libreOfficePath = $this->getLibreOfficePath();
            if (!$libreOfficePath) {
                throw new \Exception('LibreOffice path not found');
            }
            
            // Use silent flags to prevent CMD popup and ensure full automation
            $command = "\"$libreOfficePath\" --headless --invisible --nocrashreport --nodefault --nolockcheck --nologo --norestore --convert-to pdf --outdir \"$outputDir\" \"$fullDocxPath\" 2>&1";
            $output = shell_exec($command);
            
            Log::info('LibreOffice conversion output', [
                'request_id' => $request->id,
                'command' => $command,
                'output' => $output
            ]);

            if (!file_exists($outputPath)) {
                throw new \Exception('PDF conversion failed - output file not created. LibreOffice output: ' . $output);
            }
            
            // Return the relative path for storage
            $relativePath = "requests/{$request->user_id}/{$request->request_code}/" . basename($outputPath);
            
            Log::info('DOCX converted to PDF successfully', [
                'request_id' => $request->id,
                'docx_path' => $docxPath,
                'pdf_path' => $relativePath
            ]);
            
            return $relativePath;

        } catch (\Exception $e) {
            Log::error('Error converting DOCX to PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Check if LibreOffice is available
     */
    private function isLibreOfficeAvailable(): bool
    {
        return $this->getLibreOfficePath() !== null;
    }

    /**
     * Get the path to LibreOffice executable
     */
    private function getLibreOfficePath(): ?string
    {
        // Try multiple possible LibreOffice paths
        $possiblePaths = [
            'soffice', // Try PATH first
            'libreoffice', // Try libreoffice command
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe', // Windows default path
            'C:\\Program Files\\LibreOffice\\program\\soffice', // Without .exe
            '/usr/bin/libreoffice', // Linux default
            '/usr/bin/soffice', // Linux soffice
            '/usr/local/bin/libreoffice', // Linux local
            '/usr/local/bin/soffice', // Linux local soffice
        ];
        
        foreach ($possiblePaths as $path) {
            $output = shell_exec("\"$path\" --version 2>&1");
            if ($output !== null && strpos($output, 'LibreOffice') !== false) {
                Log::info('LibreOffice found at path', ['path' => $path, 'version_output' => $output]);
                return $path;
            }
        }
        
        Log::warning('LibreOffice not found in any of the expected paths', [
            'environment' => app()->environment(),
            'os_family' => PHP_OS_FAMILY
        ]);
        return null;
    }

    /**
     * Overlay signature on PDF using FPDI (preserves layout perfectly)
     */
    private function overlaySignatureOnPdf(string $pdfPath, Signature $signature, Request $request, User $user): ?string
    {
        try {
            $fullPdfPath = Storage::disk('local')->path($pdfPath);
            $signaturePath = Storage::disk('local')->path($signature->path);
            
            if (!file_exists($signaturePath)) {
                throw new \Exception('Signature file not found: ' . $signaturePath);
            }

            // Create output path for signed PDF
            $outputPath = Storage::disk('local')->path("requests/{$request->user_id}/{$request->request_code}/" . pathinfo(basename($pdfPath), PATHINFO_FILENAME) . '_signed.pdf');
            
            // Use FPDI to overlay signature on existing PDF
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($fullPdfPath);
            
            // Import all pages and overlay signature
            $signaturePlaced = false;
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
                
                // Check if this page should have a signature (only if coordinates exist for this page)
                $shouldHaveSignature = $this->shouldPageHaveSignature($pdf, $user, $i, $request);
                
                if ($shouldHaveSignature) {
                    $signaturePlaced = $this->placeSignatureUsingCoordinates($pdf, $signaturePath, $user, $i, $request);
                    
                    if ($signaturePlaced) {
                        Log::info('Signature placed on page using coordinates', [
                            'page_number' => $i,
                            'user_name' => $user->name,
                            'reason' => 'Coordinates found for this page'
                        ]);
                    }
                } else {
                    Log::info('Skipping signature on page', [
                        'page_number' => $i,
                        'user_name' => $user->name,
                        'reason' => 'No coordinates found for this page'
                    ]);
                }
            }
            
            // Log summary of signature placement across all pages
            Log::info('Signature placement summary', [
                'request_id' => $request->id,
                'total_pages' => $pageCount,
                'signature_placed' => $signaturePlaced,
                'user_name' => $user->name,
                'strategy' => 'Pure coordinate-based placement'
            ]);
            
            // Save the signed PDF
            $pdf->Output($outputPath, 'F');
            
            // Return the relative path for storage
            $relativePath = "requests/{$request->user_id}/{$request->request_code}/" . basename($outputPath);
            
            Log::info('Signature overlaid on PDF successfully', [
                'request_id' => $request->id,
                'pdf_path' => $pdfPath,
                'signed_pdf' => $relativePath,
                'signature_path' => $signaturePath,
                'signature_placed' => $signaturePlaced
            ]);

            return $relativePath;

        } catch (\Exception $e) {
            Log::error('Error overlaying signature on PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Place signature using stored coordinates
     */
    private function placeSignatureUsingCoordinates(Fpdi $pdf, string $signaturePath, User $user, int $pageNumber, Request $request): bool
    {
        try {
            // Get stored coordinates for this user on this page
            $storedCoordinates = $this->getSignatoryCoordinates($request, $user);
            
            if ($storedCoordinates && $storedCoordinates['page'] == $pageNumber) {
                Log::info('Using stored coordinates for signature placement', [
                    'page_number' => $pageNumber,
                    'user_name' => $user->name,
                    'stored_coordinates' => $storedCoordinates
                ]);
                
                // Place signature above the name (y - 30 for offset)
                $this->placeSignatureAtPosition($pdf, $signaturePath, $storedCoordinates['x'], $storedCoordinates['y'] - 30, $user);
                return true;
            }
            
            Log::warning('No stored coordinates found for user on this page', [
                'page_number' => $pageNumber,
                'user_name' => $user->name,
                'user_id' => $user->id
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error placing signature using coordinates', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'page_number' => $pageNumber
            ]);
            return false;
        }
    }

    /**
     * Place signature at a specific position
     */
    private function placeSignatureAtPosition(Fpdi $pdf, string $signaturePath, float $x, float $y, User $user): void
    {
        try {
            // Signature dimensions
            $signatureWidth = 60; // mm
            $signatureHeight = 30; // mm
            
            Log::info('Attempting to place signature at position', [
                'x' => $x,
                'y' => $y,
                'signature_width' => $signatureWidth,
                'signature_height' => $signatureHeight,
                'signature_path' => $signaturePath,
                'user_name' => $user->name,
                'page_width' => $pdf->GetPageWidth(),
                'page_height' => $pdf->GetPageHeight()
            ]);
            
            // Check if signature file exists
            if (!file_exists($signaturePath)) {
                Log::error('Signature file not found', [
                    'signature_path' => $signaturePath,
                    'file_exists' => file_exists($signaturePath)
                ]);
                return;
            }
            
            // Check if coordinates are within page boundaries
            $pageWidth = $pdf->GetPageWidth();
            $pageHeight = $pdf->GetPageHeight();
            
            if ($x < 0 || $x + $signatureWidth > $pageWidth || $y < 0 || $y + $signatureHeight > $pageHeight) {
                Log::warning('Signature coordinates outside page boundaries, adjusting', [
                    'original_x' => $x,
                    'original_y' => $y,
                    'page_width' => $pageWidth,
                    'page_height' => $pageHeight,
                    'signature_width' => $signatureWidth,
                    'signature_height' => $signatureHeight
                ]);
                
                // Adjust coordinates to fit within page
                $x = max(20, min($x, $pageWidth - $signatureWidth - 20));
                $y = max(20, min($y, $pageHeight - $signatureHeight - 20));
                
                Log::info('Adjusted coordinates', [
                    'adjusted_x' => $x,
                    'adjusted_y' => $y
                ]);
            }
            
            // Place the signature image
            $pdf->Image($signaturePath, $x, $y, $signatureWidth, $signatureHeight);
            
            Log::info('Signature placed successfully', [
                'final_x' => $x,
                'final_y' => $y,
                'user_name' => $user->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error placing signature at position', [
                'error' => $e->getMessage(),
                'x' => $x,
                'y' => $y,
                'user_name' => $user->name
            ]);
        }
    }

    /**
     * Update request with signature status
     */
    private function updateRequestSignatureStatus(Request $request, User $user, string $signedDocumentPath, string $originalPath): void
    {
        $request->update([
            'signature_status' => SignatureStatus::SIGNED,
            'signed_at' => now(),
            'signed_by' => $user->id,
            'signed_document_path' => $signedDocumentPath,
            'original_document_path' => $originalPath,
        ]);
    }

    /**
     * Restore original document if signing fails
     */
    private function restoreOriginalDocument(Request $request, string $originalPath): void
    {
        if (Storage::disk('local')->exists($originalPath)) {
            $documentPath = $this->getDocumentPath($request);
            if ($documentPath) {
                Storage::disk('local')->copy($originalPath, $documentPath);
                
                Log::info('Document restored from backup after signing failure', [
                    'request_id' => $request->id,
                    'backup_path' => $originalPath,
                    'restored_path' => $documentPath
                ]);
            }
        }
    }

    /**
     * Revert a signed document to its previous version
     */
    public function revertDocument(Request $request): bool
    {
        try {
            if (!$request->canBeReverted()) {
                return false;
            }

            $backupPath = $request->original_document_path;
            if (!$backupPath || !Storage::disk('local')->exists($backupPath)) {
                Log::error('Backup document not found for reversion', [
                    'request_id' => $request->id,
                    'backup_path' => $backupPath
                ]);
                return false;
            }

            $documentPath = $this->getDocumentPath($request);
            if ($documentPath) {
                Storage::disk('local')->delete($documentPath);
                Storage::disk('local')->copy($backupPath, $documentPath);
                
                Log::info('Document reverted to backup version', [
                    'request_id' => $request->id,
                    'backup_path' => $backupPath,
                    'restored_path' => $documentPath
                ]);
            }

            $request->update([
                'signature_status' => SignatureStatus::PENDING,
                'signed_at' => null,
                'signed_by' => null,
                'signed_document_path' => null,
                'original_document_path' => null,
            ]);

            return true;
            
        } catch (\Exception $e) {
            Log::error('Error reverting document', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clean up intermediate files after successful signing
     */
    private function cleanupIntermediateFiles(Request $request, string $docxPath, string $pdfPath): void
    {
        try {
            // Delete the modified DOCX (we have the original backup)
            if (Storage::disk('local')->exists($docxPath)) {
                Storage::disk('local')->delete($docxPath);
                Log::info('Cleaned up modified DOCX file', [
                    'request_id' => $request->id,
                    'deleted_path' => $docxPath
                ]);
            }
            
            // Delete the unsigned PDF (we have the signed version)
            if (Storage::disk('local')->exists($pdfPath)) {
                Storage::disk('local')->delete($pdfPath);
                Log::info('Cleaned up unsigned PDF file', [
                    'request_id' => $request->id,
                    'deleted_path' => $pdfPath
                ]);
            }
            
            Log::info('Intermediate files cleaned up successfully', [
                'request_id' => $request->id
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Error cleaning up intermediate files', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
            // Don't fail the signing process if cleanup fails
        }
    }

    /**
     * Extract and store coordinates if not already done
     */
    private function extractAndStoreCoordinatesIfNeeded(Request $request, string $backupPath): void
    {
        try {
            $cacheKey = "signatory_coordinates_{$request->id}";
            
            // Check if coordinates are already extracted and cached
            if (Cache::has($cacheKey)) {
                Log::info('Signatory coordinates already extracted and cached', [
                    'request_id' => $request->id,
                    'cache_key' => $cacheKey
                ]);
                return;
            }
            
            // Extract coordinates from the provided backup path
            $coordinates = $this->extractSignatoryCoordinates($request, $backupPath);
            
            if (!empty($coordinates)) {
                // Store in cache for later use
                $this->storeSignatoryCoordinates($request, $coordinates);
            }
            
        } catch (\Exception $e) {
            Log::error('Error extracting and storing coordinates', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Extract coordinates of all signatory names from the original DOCX backup
     */
    private function extractSignatoryCoordinates(Request $request, string $backupPath): array
    {
        try {
            // Use the provided backup path directly
            if (!$backupPath || !Storage::disk('local')->exists($backupPath)) {
                Log::warning('No backup document found for coordinate extraction', [
                    'request_id' => $request->id,
                    'backup_path' => $backupPath
                ]);
                return [];
            }

            $fullBackupPath = Storage::disk('local')->path($backupPath);
            
            // Use PhpWord to read the original DOCX (clean, unmodified)
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullBackupPath);
            
            $coordinates = [];
            $pageNumber = 1;
            
            // Get all signatory users from database
            $signatories = User::where('role', 'signatory')->get();
            
            Log::info('Starting coordinate extraction', [
                'request_id' => $request->id,
                'backup_path' => $backupPath,
                'signatories_count' => $signatories->count(),
                'signatories' => $signatories->pluck('name', 'id')->toArray()
            ]);
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        foreach ($element->getElements() as $textElement) {
                            if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                $text = $textElement->getText();
                                
                                // Check if this text contains any signatory name
                                foreach ($signatories as $signatory) {
                                    if (stripos($text, $signatory->name) !== false) {
                                        // Get the position information
                                        $coordinates[] = [
                                            'user_id' => $signatory->id,
                                            'name' => $signatory->name,
                                            'role' => $signatory->signatoryType(),
                                            'page' => $pageNumber,
                                            'text' => $text,
                                            'x' => $this->estimateXPosition($element),
                                            'y' => $this->estimateYPosition($element, $pageNumber)
                                        ];
                                        
                                        Log::info('Found signatory name in document', [
                                            'request_id' => $request->id,
                                            'signatory_name' => $signatory->name,
                                            'role' => $signatory->signatoryType(),
                                            'page' => $pageNumber,
                                            'text' => $text
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                $pageNumber++;
            }
            
            Log::info('Signatory coordinates extracted successfully', [
                'request_id' => $request->id,
                'coordinates_found' => count($coordinates)
            ]);
            
            return $coordinates;
            
        } catch (\Exception $e) {
            Log::error('Error extracting signatory coordinates', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Estimate X position based on element properties
     */
    private function estimateXPosition($element): float
    {
        // Try to get X position from element properties
        if (method_exists($element, 'getStyle') && $element->getStyle()) {
            $style = $element->getStyle();
            if (method_exists($style, 'getPosition') && $style->getPosition()) {
                return $style->getPosition()->getX() ?? 50;
            }
        }
        return 50; // Default fallback
    }

    /**
     * Estimate Y position based on element properties and page
     */
    private function estimateYPosition($element, int $pageNumber): float
    {
        // Try to get Y position from element properties
        if (method_exists($element, 'getStyle') && $element->getStyle()) {
            $style = $element->getStyle();
            if (method_exists($style, 'getPosition') && $style->getPosition()) {
                return $style->getPosition()->getY() ?? (200 + ($pageNumber - 1) * 300);
            }
        }
        // Default: estimate position based on page number
        return 200 + ($pageNumber - 1) * 300;
    }

    /**
     * Store signatory coordinates in cache for later use
     */
    private function storeSignatoryCoordinates(Request $request, array $coordinates): void
    {
        try {
            $cacheKey = "signatory_coordinates_{$request->id}";
            Cache::put($cacheKey, $coordinates, now()->addHours(24)); // Cache for 24 hours
            
            Log::info('Signatory coordinates stored in cache', [
                'request_id' => $request->id,
                'cache_key' => $cacheKey,
                'coordinates_count' => count($coordinates)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error storing signatory coordinates in cache', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get stored signatory coordinates for a specific user
     */
    private function getSignatoryCoordinates(Request $request, User $user): ?array
    {
        try {
            $cacheKey = "signatory_coordinates_{$request->id}";
            $coordinates = Cache::get($cacheKey, []);
            
            // Find coordinates for this specific user
            foreach ($coordinates as $coord) {
                if ($coord['user_id'] == $user->id) {
                    Log::info('Found stored coordinates for user', [
                        'request_id' => $request->id,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'coordinates' => $coord
                    ]);
                    return $coord;
                }
            }
            
            Log::info('No stored coordinates found for user', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error retrieving signatory coordinates', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if a page should have a signature based on stored coordinates
     */
    private function shouldPageHaveSignature(Fpdi $pdf, User $user, int $pageNumber, Request $request): bool
    {
        try {
            // ONLY check if we have stored coordinates for this user on this specific page
            $storedCoordinates = $this->getSignatoryCoordinates($request, $user);
            return $storedCoordinates && $storedCoordinates['page'] == $pageNumber;
            
        } catch (\Exception $e) {
            Log::error('Error checking if page should have signature', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'page_number' => $pageNumber
            ]);
            // Default to false if there's an error
            return false;
        }
    }

    /**
     * Ensure signatory coordinates are extracted from final PDF using pdftotext -bbox
     */
    private function extractAndStorePdfCoordinatesIfNeeded(Request $request, string $fullPdfPath): void
    {
        try {
            $cacheKey = "signatory_coordinates_{$request->id}";
            if (Cache::has($cacheKey)) {
                Log::info('PDF coordinates already cached', ['request_id' => $request->id]);
                return;
            }

            if (!$this->isPdftotextAvailable()) {
                Log::warning('pdftotext not available; cannot extract precise PDF coordinates');
                return;
            }

            $coords = $this->extractCoordinatesFromPdf($request, $fullPdfPath);
            if (!empty($coords)) {
                $this->storeSignatoryCoordinates($request, $coords);
            } else {
                Log::warning('No PDF coordinates extracted', ['request_id' => $request->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error ensuring PDF coordinates', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if pdftotext is available on PATH or common install locations
     */
    private function isPdftotextAvailable(): bool
    {
        $candidates = [
            'pdftotext',
            'C:\\Program Files\\poppler\\bin\\pdftotext.exe',
            'C:\\ProgramData\\chocolatey\\lib\\poppler\\tools\\pdftotext.exe'
        ];
        foreach ($candidates as $bin) {
            $out = @shell_exec("\"$bin\" -v 2>&1");
            if ($out !== null && stripos($out, 'pdftotext') !== false) {
                Log::info('pdftotext found', ['path' => $bin]);
                return true;
            }
        }
        return false;
    }

    /**
     * Extract coordinates from PDF using pdftotext -bbox; returns array of [user_id, name, page, x(mm), y(mm)]
     */
    private function extractCoordinatesFromPdf(Request $request, string $fullPdfPath): array
    {
        $coords = [];
        try {
            $bin = 'pdftotext';
            $cmd = "\"$bin\" -bbox -enc UTF-8 -q \"$fullPdfPath\" -"; // output XHTML to stdout
            $xml = shell_exec($cmd);
            if ($xml === null || $xml === '') {
                Log::warning('pdftotext -bbox returned empty output', ['pdf' => $fullPdfPath]);
                return [];
            }

            // Parse XHTML and assemble word stream by page
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true);
            $loaded = $doc->loadXML($xml);
            libxml_clear_errors();
            if (!$loaded) {
                Log::warning('Failed to parse pdftotext -bbox output as XML');
                return [];
            }

            // Gather signatories
            $signatories = User::where('role', 'signatory')->get();
            $signatoryNames = [];
            foreach ($signatories as $s) {
                $signatoryNames[$s->id] = [
                    'id' => $s->id,
                    'name' => $s->name,
                ];
            }

            // Helper: points -> mm
            $ptToMm = function (float $pt): float {
                return $pt * 25.4 / 72.0;
            };

            $pages = $doc->getElementsByTagName('page');
            foreach ($pages as $pageEl) {
                $pageNumAttr = $pageEl->getAttribute('number');
                $pageNum = $pageNumAttr !== '' ? (int)$pageNumAttr : 1;

                // Create a flat list of words with bbox
                $words = [];
                $wordNodes = $pageEl->getElementsByTagName('word');
                foreach ($wordNodes as $w) {
                    $text = trim($w->textContent);
                    if ($text === '') continue;
                    $xMin = (float)$w->getAttribute('xMin');
                    $yMin = (float)$w->getAttribute('yMin');
                    $xMax = (float)$w->getAttribute('xMax');
                    $yMax = (float)$w->getAttribute('yMax');
                    $words[] = [
                        'text' => $text,
                        'xMin' => $xMin,
                        'yMin' => $yMin,
                        'xMax' => $xMax,
                        'yMax' => $yMax,
                    ];
                }

                if (empty($words)) continue;

                // For each signatory, try to find sequential word match of their name
                foreach ($signatoryNames as $sig) {
                    $tokens = array_values(array_filter(preg_split('/\s+/', $sig['name'])));
                    if (empty($tokens)) continue;

                    for ($i = 0; $i <= count($words) - count($tokens); $i++) {
                        $match = true;
                        for ($j = 0; $j < count($tokens); $j++) {
                            if (strcasecmp($words[$i + $j]['text'], $tokens[$j]) !== 0) {
                                $match = false;
                                break;
                            }
                        }
                        if ($match) {
                            // Union bbox across the matched tokens
                            $xMin = $words[$i]['xMin'];
                            $yMin = $words[$i]['yMin'];
                            $xMax = $words[$i + count($tokens) - 1]['xMax'];
                            $yMax = max(array_column(array_slice($words, $i, count($tokens)), 'yMax'));

                            // Convert to mm
                            $xMm = $ptToMm($xMin);
                            $yMm = $ptToMm($yMin);

                            $coords[] = [
                                'user_id' => $sig['id'],
                                'name' => $sig['name'],
                                'page' => $pageNum,
                                'x' => $xMm,
                                'y' => $yMm,
                            ];

                            Log::info('PDF bbox match for signatory', [
                                'request_id' => $request->id,
                                'user_id' => $sig['id'],
                                'name' => $sig['name'],
                                'page' => $pageNum,
                                'x_mm' => $xMm,
                                'y_mm' => $yMm,
                            ]);

                            break; // first match per page
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error extracting coordinates from PDF', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
        return $coords;
    }
}
