<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Request as UserRequest;
use App\Models\RequestSignature;
use App\Models\Setting;
use App\Models\User;
use App\Enums\SignatureStatus;

class SigningController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');

        $candidateRequests = UserRequest::where('status', '!=', 'draft')
            ->orderByDesc('requested_at')
            ->limit(200)
            ->get();
        $needs = [];

        foreach ($candidateRequests as $req) {
            $form = is_array($req->form_data) ? $req->form_data : (json_decode($req->form_data ?? '[]', true) ?: []);
            $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
            if ($matchedRole) {
                $needs[] = [
                    'id' => $req->id,
                    'request_code' => $req->request_code,
                    'type' => $req->type,
                    'status' => $req->status,
                    'matched_role' => $matchedRole,
                    'requested_at' => $req->requested_at,
                ];
            }
        }

        $citations_request_enabled = \App\Models\Setting::get('citations_request_enabled', '1');
        
        foreach ($needs as &$request) {
            $request['signature_status'] = $request['signature_status'] ?? 'pending';
            $request['can_revert'] = false;
            if (isset($request['signed_at'])) {
                $signedAt = \Carbon\Carbon::parse($request['signed_at']);
                $request['can_revert'] = $signedAt->diffInHours(now()) < 24;
            }
        }
        
        return view('signing.index', [
            'requests' => $needs,
            'signatoryType' => $signatoryType,
            'citations_request_enabled' => $citations_request_enabled,
        ]);
    }


    private function matchesSignatory(array $form, ?string $signatoryType, string $userName): ?string
    {
        if ($userName === '') return null;
        $nameLower = mb_strtolower($userName);

        $map = [
            'faculty' => ['facultyname', 'faculty_name', 'rec_faculty_name'],
            'center_manager' => ['centermanager', 'center_manager', 'research_center_manager'],
            'college_dean' => ['collegedean', 'college_dean', 'dean', 'dean_name', 'rec_dean_name'],
        ];
        $keys = $map[$signatoryType] ?? [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $form)) continue;
            $val = trim((string) $form[$key]);
            if ($val !== '' && mb_strtolower($val) === $nameLower) {
                return $signatoryType;
            }
        }
        return null;
    }


    public function downloadRequestFiles(Request $request, $requestId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $userRequest = UserRequest::findOrFail($requestId);
        
        // Verify user is authorized to sign this request
        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');
        $form = is_array($userRequest->form_data) ? $userRequest->form_data : (json_decode($userRequest->form_data ?? '[]', true) ?: []);
        $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
        
        if (!$matchedRole) {
            abort(403, 'You are not authorized to access this request');
        }

        try {
            // Use the same logic as admin download
            $zipPath = $this->createRequestZipFromPdfPath($userRequest);
            
            return response()->download($zipPath, "request-{$userRequest->request_code}-files.zip")->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error creating request ZIP', [
                'request_id' => $userRequest->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create download package. Please try again.'
            ], 500);
        }
    }

    public function uploadSignedDocuments(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'signed_documents' => 'required|array|min:1|max:10', // Max 10 files per upload
            'signed_documents.*' => 'required|file|mimes:pdf,docx|max:10240', // 10MB max per file
        ]);

        $userRequest = UserRequest::findOrFail($validated['request_id']);

        // Allow multiple signatures from the same signatory
        // (Removed restriction: users can now sign the same request multiple times)

        // Verify user is authorized to sign this request
        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');
        $form = is_array($userRequest->form_data) ? $userRequest->form_data : (json_decode($userRequest->form_data ?? '[]', true) ?: []);
        $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
        
        if (!$matchedRole) {
            abort(403, 'You are not authorized to sign this request');
        }

        try {
            $uploadedFiles = $this->processSignedDocuments($userRequest, $validated['signed_documents'], $user);
            
            if (empty($uploadedFiles)) {
            return response()->json([
                'success' => false,
                    'message' => 'No files were successfully uploaded. Please check that the uploaded files match the original filenames exactly.'
                ], 400);
            }
            
            // Create individual signature record
            $signature = RequestSignature::create([
                'request_id' => $userRequest->id,
                'user_id' => $user->id,
                'signatory_role' => $matchedRole,
                'signatory_name' => $userName,
                'signed_at' => now(),
                'signed_document_path' => $uploadedFiles[0]['path'] ?? null, // Store first uploaded file path
                'original_document_path' => null, // Could be populated if needed
            ]);

            // Update request's overall signature status
            // For now, we'll mark it as signed when any signatory signs
            // This could be changed to require all signatories based on business logic
            $userRequest->update([
                'signature_status' => SignatureStatus::SIGNED,
                'signed_at' => now(),
                'signed_by' => $user->id, // Keep the last signatory for backward compatibility
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Signed documents uploaded successfully',
                'uploaded_files' => $uploadedFiles
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error uploading signed documents', [
                'request_id' => $userRequest->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload signed documents. Please try again.'
            ], 500);
        }
    }

    private function createRequestZipFromPdfPath(UserRequest $userRequest): string
    {
        // Parse the pdf_path JSON to get actual file locations (same as admin logic)
        $pdfPathData = json_decode($userRequest->pdf_path, true);
        
        Log::info('Creating ZIP for request', [
            'request_id' => $userRequest->id,
            'pdf_path_raw' => $userRequest->pdf_path,
            'pdf_path_parsed' => $pdfPathData
        ]);
        
        if (!$pdfPathData) {
            Log::error('No pdf_path data found for request', [
                'request_id' => $userRequest->id,
                'pdf_path' => $userRequest->pdf_path
            ]);
            throw new \Exception('No files found for this request');
        }

        $zipName = 'request-' . $userRequest->request_code . '-' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);
        
        // Create temp directory if it doesn't exist
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Could not create ZIP file');
        }

        $addedFiles = 0;
        $errors = [];

        // Add PDF files
        if (isset($pdfPathData['pdfs']) && is_array($pdfPathData['pdfs'])) {
            Log::info('Processing PDF files', ['pdfs' => $pdfPathData['pdfs']]);
            foreach ($pdfPathData['pdfs'] as $key => $fileInfo) {
                if (isset($fileInfo['path']) && is_string($fileInfo['path'])) {
                    $fullPath = $this->findFileInStorage($fileInfo['path']);
                    
                    Log::info('Checking PDF file', [
                        'key' => $key,
                        'path' => $fileInfo['path'],
                        'found' => $fullPath !== null
                    ]);
                    
                    if ($fullPath) {
                        // Use the original filename from the path to preserve exact naming
                        $originalFileName = basename($fileInfo['path']);
                        if ($zip->addFile($fullPath, 'PDFs/' . $originalFileName)) {
                            $addedFiles++;
                            Log::info('Added PDF file to ZIP', ['file' => $originalFileName]);
                        } else {
                            $errors[] = "Failed to add PDF file: {$originalFileName}";
                        }
                    } else {
                        $errors[] = "PDF file not found or not readable: {$fileInfo['path']}";
                    }
                }
            }
        } else {
            Log::info('No PDF files found in pdf_path data');
        }

        // Add DOCX files
        if (isset($pdfPathData['docxs']) && is_array($pdfPathData['docxs'])) {
            Log::info('Processing DOCX files', ['docxs' => $pdfPathData['docxs']]);
            foreach ($pdfPathData['docxs'] as $key => $docxPath) {
                if ($docxPath && is_string($docxPath)) {
                    $fullPath = $this->findFileInStorage($docxPath);
                    
                    Log::info('Checking DOCX file', [
                        'key' => $key,
                        'path' => $docxPath,
                        'found' => $fullPath !== null
                    ]);
                    
                    if ($fullPath) {
                        // Use the original filename from the path to preserve exact naming
                        $originalFileName = basename($docxPath);
                        if ($zip->addFile($fullPath, 'DOCXs/' . $originalFileName)) {
                            $addedFiles++;
                            Log::info('Added DOCX file to ZIP', ['file' => $originalFileName]);
                        } else {
                            $errors[] = "Failed to add DOCX file: {$originalFileName}";
                        }
                    } else {
                        $errors[] = "DOCX file not found or not readable: {$docxPath}";
                    }
                }
            }
        } else {
            Log::info('No DOCX files found in pdf_path data');
        }

        $zip->close();

        if ($addedFiles === 0) {
            if (!empty($errors)) {
                Log::warning('ZIP download failed - no files added', [
                    'request_id' => $userRequest->id,
                    'errors' => $errors
                ]);
            }
            throw new \Exception('No files found to include in ZIP');
        }

        Log::info('ZIP download created successfully', [
            'request_id' => $userRequest->id,
            'files_added' => $addedFiles,
            'zip_path' => $zipPath
        ]);

        return $zipPath;
    }

    private function processSignedDocuments(UserRequest $userRequest, array $files, User $user): array
    {
        // Parse existing pdf_path data
        $pdfPathData = json_decode($userRequest->pdf_path, true) ?: [];
        
        $uploadedFiles = [];
        $updatedPdfPathData = $pdfPathData;
        $processedFiles = []; // Track processed files to prevent duplicates
        
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            
            // Check for duplicate files in the same upload batch
            if (in_array($originalName, $processedFiles)) {
                Log::warning('Duplicate file detected in upload batch', [
                    'original_name' => $originalName,
                    'request_id' => $userRequest->id
                ]);
                continue;
            }
            
            // Get file info BEFORE doing anything with the file
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            // Find the matching key in pdf_path data by comparing original filenames
            $matchingKey = $this->findMatchingKeyInPdfPath($pdfPathData, $originalName, $extension);
            
            if (!$matchingKey) {
                Log::warning('No matching key found for uploaded file', [
                    'original_name' => $originalName,
                    'extension' => $extension
                ]);
                continue; // Skip files that don't match existing ones
            }
            
            // Get the original file path and create backup
            $originalPath = $this->getOriginalFilePath($pdfPathData, $matchingKey, $extension);
            $backupPath = $this->createBackupOfOriginalFile($originalPath, $userRequest->id);
            
            // Move the uploaded file to replace the original file
            $replacementSuccess = $this->replaceOriginalFileWithUploaded($originalPath, $file, $userRequest->id);
            
            if (!$replacementSuccess) {
                Log::error('Failed to replace original file with uploaded file', [
                    'original_path' => $originalPath,
                    'uploaded_file' => $originalName,
                    'request_id' => $userRequest->id
                ]);
                continue;
            }
            
            // Update the pdf_path data structure to reflect the new file
            if ($extension === 'pdf') {
                $updatedPdfPathData['pdfs'][$matchingKey] = [
                    'path' => $originalPath,
                    'original_name' => $originalName,
                ];
            } elseif ($extension === 'docx') {
                $updatedPdfPathData['docxs'][$matchingKey] = $originalPath;
            }
            
            $uploadedFiles[] = [
                'original_name' => $originalName,
                'path' => $originalPath,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'type' => $extension,
            ];
            
            // Mark this file as processed to prevent duplicates
            $processedFiles[] = $originalName;
        }
        
        // Update the request with the new pdf_path data
        $userRequest->update([
            'pdf_path' => json_encode($updatedPdfPathData),
        ]);
        
        return $uploadedFiles;
    }
    
    private function findMatchingKeyInPdfPath(array $pdfPathData, string $originalName, string $extension): ?string
    {
        $type = $extension === 'pdf' ? 'pdfs' : 'docxs';
        
        Log::info('Starting file matching process', [
            'original_name' => $originalName,
            'extension' => $extension,
            'type' => $type,
            'pdf_path_data' => $pdfPathData
        ]);
        
        if (!isset($pdfPathData[$type])) {
            Log::warning('No files found for type', ['type' => $type]);
            return null;
        }
        
        foreach ($pdfPathData[$type] as $key => $fileData) {
            $storedFileName = null;
            
            if ($extension === 'pdf' && is_array($fileData)) {
                // For PDFs, check both original_name and basename of path
                $storedFileName = $fileData['original_name'] ?? basename($fileData['path']);
            } elseif ($extension === 'docx') {
                // For DOCX, use basename of path
                $storedFileName = basename($fileData);
            }
            
            Log::info('Comparing filenames', [
                'key' => $key,
                'stored_filename' => $storedFileName,
                'uploaded_filename' => $originalName,
                'match' => $storedFileName === $originalName,
                'file_data' => $fileData
            ]);
            
            if ($storedFileName === $originalName) {
                Log::info('Found matching key for uploaded file', [
                    'original_name' => $originalName,
                    'matching_key' => $key,
                    'type' => $type
                ]);
                return $key;
            }
        }
        
        Log::warning('No matching key found for uploaded file', [
            'original_name' => $originalName,
            'extension' => $extension
        ]);
        return null;
    }
    
    private function getOriginalFilePath(array $pdfPathData, string $matchingKey, string $extension): string
    {
        $type = $extension === 'pdf' ? 'pdfs' : 'docxs';
        
        if (!isset($pdfPathData[$type][$matchingKey])) {
            throw new \Exception("No original file found for key: {$matchingKey}");
        }
        
        $fileData = $pdfPathData[$type][$matchingKey];
        return is_array($fileData) ? $fileData['path'] : $fileData;
    }
    
    private function createBackupOfOriginalFile(string $originalPath, int $requestId): ?string
    {
        $originalFullPath = $this->findFileInStorage($originalPath);
        
        if (!$originalFullPath) {
            Log::warning('Original file not found for backup', [
                'original_path' => $originalPath,
                'request_id' => $requestId
            ]);
            return null;
        }
        
        // Create backup with timestamp
        $backupPath = $originalFullPath . '.backup.' . time();
        $backupSuccess = copy($originalFullPath, $backupPath);
        
        if ($backupSuccess) {
            Log::info('Created backup of original file', [
                'original_path' => $originalFullPath,
                'backup_path' => $backupPath,
                'request_id' => $requestId
            ]);
            return $backupPath;
        } else {
            Log::error('Failed to create backup of original file', [
                'original_path' => $originalFullPath,
                'request_id' => $requestId
            ]);
            return null;
        }
    }
    
    private function replaceOriginalFileWithUploaded(string $originalPath, $uploadedFile, int $requestId): bool
    {
        $targetPath = $this->findFileInStorage($originalPath);
        
        if (!$targetPath) {
            Log::error('Cannot determine target path for file replacement', [
                'original_path' => $originalPath,
                'request_id' => $requestId
            ]);
            return false;
        }
        
        // Move the uploaded file to replace the original
        $moveSuccess = $uploadedFile->move(dirname($targetPath), basename($targetPath));
        
        if ($moveSuccess) {
            Log::info('Successfully replaced original file with uploaded file', [
                'original_path' => $targetPath,
                'uploaded_file' => $uploadedFile->getClientOriginalName(),
                'request_id' => $requestId
            ]);
            return true;
        } else {
            Log::error('Failed to move uploaded file to replace original', [
                'target_path' => $targetPath,
                'uploaded_file' => $uploadedFile->getClientOriginalName(),
                'request_id' => $requestId
            ]);
            return false;
        }
    }
    
    /**
     * Helper method to find a file in all possible storage locations
     */
    private function findFileInStorage(string $relativePath): ?string
    {
        $possiblePaths = [
            storage_path('app/public/' . $relativePath),
            storage_path('app/' . $relativePath),
            storage_path('app/private/' . $relativePath),
        ];
        
        foreach ($possiblePaths as $fullPath) {
            if (file_exists($fullPath) && is_readable($fullPath)) {
                return $fullPath;
            }
        }
        
        return null;
    }

    public function revertDocument(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
        ]);

        $userRequest = UserRequest::findOrFail($validated['request_id']);

        // Verify user is authorized to revert this request
        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');
        $form = is_array($userRequest->form_data) ? $userRequest->form_data : (json_decode($userRequest->form_data ?? '[]', true) ?: []);
        $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
        
        if (!$matchedRole) {
            abort(403, 'You are not authorized to revert this request');
        }

        // Check if this specific signatory has signed this request
        if (!$userRequest->hasBeenSignedBy($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have not signed this request, so you cannot revert it.'
            ], 400);
        }

        // Get the signature record for this user
        $signature = RequestSignature::where('request_id', $userRequest->id)
                                   ->where('user_id', $user->id)
                                   ->first();

        if (!$signature) {
            return response()->json([
                'success' => false,
                'message' => 'Signature record not found.'
            ], 400);
        }

        // Check if signature can be reverted (within 24 hours)
        if (\Carbon\Carbon::parse($signature->signed_at)->diffInHours(now()) >= 24) {
            return response()->json([
                'success' => false,
                'message' => 'This signature can only be reverted within 24 hours of signing.'
            ], 400);
        }

        try {
            // Delete the individual signature record
            $signature->delete();

            // Check if there are any remaining signatures for this request
            $remainingSignatures = RequestSignature::where('request_id', $userRequest->id)->count();
            
            if ($remainingSignatures === 0) {
                // No more signatures, revert the request status
                $userRequest->update([
                    'signature_status' => SignatureStatus::PENDING,
                    'signed_at' => null,
                    'signed_by' => null,
                ]);
            } else {
                // Update to the most recent remaining signature
                $latestSignature = RequestSignature::where('request_id', $userRequest->id)
                                                  ->orderBy('signed_at', 'desc')
                                                  ->first();
                $userRequest->update([
                    'signed_at' => $latestSignature->signed_at,
                    'signed_by' => $latestSignature->user_id,
                ]);
            }

            Log::info('Document reverted successfully', [
                'request_id' => $userRequest->id,
                'user_id' => $user->id,
                'reverted_by' => $user->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document reverted successfully. You can now sign it again.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error reverting document', [
                'request_id' => $userRequest->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to revert document. Please try again.'
            ], 500);
        }
    }
    
} 