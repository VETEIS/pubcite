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

        // Determine which workflow state this signatory should see
        $workflowState = $this->getWorkflowStateForSignatory($signatoryType);
        
        $candidateRequests = UserRequest::where('status', 'pending')
            ->where('workflow_state', $workflowState)
            ->orderByDesc('requested_at')
            ->limit(200)
            ->get();
        $needs = [];

        foreach ($candidateRequests as $req) {
            $form = is_array($req->form_data) ? $req->form_data : (json_decode($req->form_data ?? '[]', true) ?: []);
            $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
            
            // Deputy Director and RDD Director see ALL requests in their workflow state
            if ($signatoryType === 'deputy_director' || $signatoryType === 'rdd_director') {
                $matchedRole = $signatoryType; // They can sign all requests
            }
            
            if ($matchedRole) {
                $college = trim((string)($form['college'] ?? ''));
                $needs[] = [
                    'id' => $req->id,
                    'request_code' => $req->request_code,
                    'type' => $req->type,
                    'status' => $req->status,
                    'workflow_state' => $req->workflow_state,
                    'matched_role' => $matchedRole,
                    'college' => $college,
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


    private function getWorkflowStateForSignatory(?string $signatoryType): string
    {
        switch ($signatoryType) {
            case 'center_manager':
                return 'pending_research_manager';
            case 'college_dean':
                return 'pending_dean';
            case 'deputy_director':
                return 'pending_deputy_director';
            case 'rdd_director':
                return 'pending_director';
            default:
                return 'pending_research_manager';
        }
    }

    private function getNextWorkflowState(string $currentState, string $signatoryType): string
    {
        switch ($currentState) {
            case 'pending_research_manager':
                // After research manager signs, move to dean
                if ($signatoryType === 'center_manager') {
                    return 'pending_dean';
                }
                return $currentState; // No change for other signatories
                
            case 'pending_dean':
                // After dean signs, move to deputy director
                if ($signatoryType === 'college_dean') {
                    return 'pending_deputy_director';
                }
                return $currentState; // No change for other signatories
                
            case 'pending_deputy_director':
                // After deputy director signs, move to director
                if ($signatoryType === 'deputy_director') {
                    return 'pending_director';
                }
                return $currentState; // No change for other signatories
                
            case 'pending_director':
                // After director signs, mark as completed
                if ($signatoryType === 'rdd_director') {
                    return 'completed';
                }
                return $currentState; // No change for other signatories
                
            case 'completed':
                return $currentState; // Already completed, no change
                
            default:
                return $currentState; // Unknown state, no change
        }
    }

    private function matchesSignatory(array $form, ?string $signatoryType, string $userName): ?string
    {
        if ($userName === '') return null;
        $nameLower = mb_strtolower($userName);

        $map = [
            'faculty' => ['facultyname', 'faculty_name', 'rec_faculty_name'],
            'center_manager' => ['centermanager', 'center_manager', 'research_center_manager'],
            'college_dean' => ['collegedean', 'college_dean', 'dean', 'dean_name', 'rec_dean_name'],
            'deputy_director' => ['deputy_director', 'deputy_director_name', 'official_deputy_director_name'],
            'rdd_director' => ['rdd_director', 'rdd_director_name', 'official_rdd_director_name'],
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
        
        // Only allow access to pending requests
        if ($userRequest->status !== 'pending') {
            abort(403, 'This request is not available for signing');
        }
        
        // Verify user is authorized to sign this request based on workflow state
        $signatoryType = $user->signatoryType();
        $expectedWorkflowState = $this->getWorkflowStateForSignatory($signatoryType);
        
        // Check if request is in the correct workflow state for this signatory
        if ($userRequest->workflow_state !== $expectedWorkflowState) {
            abort(403, 'This request is not available for your signature at this time');
        }
        
        $userName = trim($user->name ?? '');
        $form = is_array($userRequest->form_data) ? $userRequest->form_data : (json_decode($userRequest->form_data ?? '[]', true) ?: []);
        $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
        
        // Deputy Director and RDD Director can access ALL endorsed requests in their workflow state
        if ($signatoryType === 'deputy_director' || $signatoryType === 'rdd_director') {
            $matchedRole = $signatoryType; // They can access all endorsed requests
        }
        
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

        // Only allow signing of pending requests
        if ($userRequest->status !== 'pending') {
            abort(403, 'This request is not available for signing');
        }

        // Verify user is authorized to sign this request based on workflow state
        $signatoryType = $user->signatoryType();
        $expectedWorkflowState = $this->getWorkflowStateForSignatory($signatoryType);
        
        // Check if request is in the correct workflow state for this signatory
        if ($userRequest->workflow_state !== $expectedWorkflowState) {
            abort(403, 'This request is not available for your signature at this time');
        }

        $userName = trim($user->name ?? '');
        $form = is_array($userRequest->form_data) ? $userRequest->form_data : (json_decode($userRequest->form_data ?? '[]', true) ?: []);
        $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
        
        // Deputy Director and RDD Director can access ALL endorsed requests in their workflow state
        if ($signatoryType === 'deputy_director' || $signatoryType === 'rdd_director') {
            $matchedRole = $signatoryType; // They can access all endorsed requests
        }
        
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

            // Update request's workflow state and signature status
            $newWorkflowState = $this->getNextWorkflowState($userRequest->workflow_state, $signatoryType);
            
            // Update status to "endorsed" only when director signs (workflow completed)
            $newStatus = ($newWorkflowState === 'completed') ? 'endorsed' : 'pending';
            
            $userRequest->update([
                'workflow_state' => $newWorkflowState,
                'status' => $newStatus,
                'signature_status' => SignatureStatus::SIGNED,
                'signed_at' => now(),
                'signed_by' => $user->id, // Keep the last signatory for backward compatibility
            ]);
            
            // If workflow is completed (Director signed), notify admins
            if ($newWorkflowState === 'completed') {
                $this->notifyAdminsOfCompletedWorkflow($userRequest);
            } else {
                // Notify the next signatory in the workflow
                $this->notifyNextSignatory($userRequest, $newWorkflowState);
            }

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
            // Standard Laravel storage paths
            storage_path('app/public/' . $relativePath),
            storage_path('app/' . $relativePath),
            storage_path('app/private/' . $relativePath),
            // Production-specific paths
            public_path('storage/' . $relativePath),
            base_path('storage/app/public/' . $relativePath),
            base_path('storage/app/' . $relativePath),
            // Direct path if already absolute
            $relativePath,
        ];
        
        Log::info('Searching for file', [
            'relative_path' => $relativePath,
            'possible_paths' => $possiblePaths
        ]);
        
        foreach ($possiblePaths as $fullPath) {
            if (file_exists($fullPath) && is_readable($fullPath)) {
                Log::info('File found', [
                    'found_path' => $fullPath,
                    'file_size' => filesize($fullPath)
                ]);
                return $fullPath;
            }
        }
        
        Log::warning('File not found in any location', [
            'relative_path' => $relativePath,
            'checked_paths' => $possiblePaths
        ]);
        
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
    
    /**
     * Notify the next signatory in the workflow
     */
    private function notifyNextSignatory(\App\Models\Request $request, string $workflowState)
    {
        try {
            $nextSignatory = null;
            $signatoryEmail = null;
            $signatoryName = null;
            
            switch ($workflowState) {
                case 'pending_dean':
                    // Find the Dean from form data
                    $form = is_array($request->form_data) ? $request->form_data : (json_decode($request->form_data ?? '[]', true) ?: []);
                    $deanName = $form['collegedean'] ?? $form['college_dean'] ?? $form['dean'] ?? $form['dean_name'] ?? $form['rec_dean_name'] ?? null;
                    if ($deanName) {
                        $nextSignatory = \App\Models\User::where('name', trim($deanName))->first();
                        $signatoryName = trim($deanName);
                    }
                    break;
                    
                case 'pending_deputy_director':
                    // Get Deputy Director from settings
                    $signatoryEmail = \App\Models\Setting::get('deputy_director_email');
                    $signatoryName = \App\Models\Setting::get('official_deputy_director_name', 'Deputy Director');
                    break;
                    
                case 'pending_director':
                    // Get Director from settings
                    $signatoryEmail = \App\Models\Setting::get('rdd_director_email');
                    $signatoryName = \App\Models\Setting::get('official_rdd_director_name', 'RDD Director');
                    break;
            }
            
            if ($nextSignatory && $nextSignatory->email) {
                \Illuminate\Support\Facades\Mail::to($nextSignatory->email)->queue(new \App\Mail\SignatoryNotification($request, 'college_dean', $signatoryName));
                
                Log::info('Next signatory notification queued', [
                    'requestId' => $request->id,
                    'workflowState' => $workflowState,
                    'signatoryName' => $signatoryName,
                    'signatoryEmail' => $nextSignatory->email
                ]);
            } elseif ($signatoryEmail && $signatoryName) {
                \Illuminate\Support\Facades\Mail::to($signatoryEmail)->queue(new \App\Mail\SignatoryNotification($request, 
                    $workflowState === 'pending_deputy_director' ? 'deputy_director' : 'rdd_director', 
                    $signatoryName));
                
                Log::info('Next signatory notification queued (from settings)', [
                    'requestId' => $request->id,
                    'workflowState' => $workflowState,
                    'signatoryName' => $signatoryName,
                    'signatoryEmail' => $signatoryEmail
                ]);
            } else {
                Log::warning('No next signatory found for workflow state', [
                    'requestId' => $request->id,
                    'workflowState' => $workflowState
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error notifying next signatory', [
                'requestId' => $request->id,
                'workflowState' => $workflowState,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Notify admins when workflow is completed (Director signed)
     */
    private function notifyAdminsOfCompletedWorkflow(\App\Models\Request $request)
    {
        try {
            // Create admin notifications for completed workflow
            $admins = \App\Models\User::where('role', 'admin')->get();
            Log::info('Creating admin notifications for completed workflow', [
                'requestId' => $request->id,
                'requestCode' => $request->request_code,
                'adminCount' => $admins->count()
            ]);
            
            foreach ($admins as $admin) {
                \App\Models\AdminNotification::create([
                    'user_id' => $admin->id,
                    'request_id' => $request->id,
                    'type' => 'workflow_completed',
                    'title' => 'Request Ready for Download',
                    'message' => $request->user->name . '\'s ' . $request->type . ' request has completed the signature workflow and is ready for download: ' . $request->request_code,
                    'data' => [
                        'request_code' => $request->request_code,
                        'user_name' => $request->user->name,
                        'user_email' => $request->user->email,
                        'type' => $request->type,
                        'workflow_state' => 'completed'
                    ]
                ]);
            }
            
            // Send email notifications to admins
            foreach ($admins as $admin) {
                \Illuminate\Support\Facades\Mail::to($admin->email)->queue(new \App\Mail\SubmissionNotification($request, $request->user, true));
            }
            
            Log::info('Admin notifications for completed workflow queued successfully', [
                'requestId' => $request->id,
                'adminEmails' => $admins->pluck('email')->toArray()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error notifying admins of completed workflow', [
                'requestId' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
} 