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
use Illuminate\Support\Facades\DB;
use App\Traits\SanitizesFilePaths;

class SigningController extends Controller
{
    use SanitizesFilePaths;
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
            ->whereDoesntHave('signatures', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
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
            case 'pending_user_signature':
                // After user signs their own request, move to center manager
                if ($signatoryType === 'user') {
                    return 'pending_research_manager';
                }
                return $currentState; // No change for other signatories
                
            case 'pending_research_manager':
                // After center manager signs, move to dean
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


    public function getRequestData(Request $httpRequest, $requestId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $userRequest = UserRequest::with('user')->findOrFail($requestId);

        // Parse form data once for use throughout the method
        $form = is_array($userRequest->form_data)
            ? $userRequest->form_data
            : (json_decode($userRequest->form_data ?? '[]', true) ?: []);

        // Check if user is accessing their own request for signing
        $isUserAccessingOwnRequest = $userRequest->user_id === $user->id && $userRequest->workflow_state === 'pending_user_signature';
        
        $signatoryType = null;
        if ($isUserAccessingOwnRequest) {
            $signatoryType = 'user';
        } else if ($user->isSignatory()) {
            $signatoryType = $user->signatoryType();
            $expectedWorkflowState = $this->getWorkflowStateForSignatory($signatoryType);

        $matchedRole = $this->matchesSignatory($form, $signatoryType, trim($user->name ?? ''));
        $hasSigned = RequestSignature::where('request_id', $userRequest->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$matchedRole && !$hasSigned && !in_array($signatoryType, ['deputy_director', 'rdd_director'])) {
            abort(403);
        }

        if (!$hasSigned
            && !in_array($signatoryType, ['deputy_director', 'rdd_director'])
            && $userRequest->workflow_state !== $expectedWorkflowState) {
                    abort(403);
                }
        } else {
            abort(403);
        }

        $signatureRecords = RequestSignature::where('request_id', $userRequest->id)
            ->get()
            ->keyBy('signatory_role');

        $signatories = $this->extractSignatories($form);

        $roleOrder = [
            'user' => [
                'label' => 'Applicant',
                'state' => 'pending_user_signature',
            ],
            'center_manager' => [
                'label' => 'Research Center Manager',
                'state' => 'pending_research_manager',
            ],
            'college_dean' => [
                'label' => 'College Dean',
                'state' => 'pending_dean',
            ],
            'deputy_director' => [
                'label' => 'Deputy Director',
                'state' => 'pending_deputy_director',
            ],
            'rdd_director' => [
                'label' => 'RDD Director',
                'state' => 'pending_director',
            ],
        ];

        $stateOrder = [
            'pending_user_signature' => 1,
            'pending_research_manager' => 2,
            'pending_dean' => 3,
            'pending_deputy_director' => 4,
            'pending_director' => 5,
            'completed' => 99,
        ];

        $currentState = $userRequest->workflow_state ?? 'pending_user_signature';
        $currentStateOrder = $stateOrder[$currentState] ?? 0;

        $formattedSignatories = [];
        // Exclude Deputy Director and RDD Director as they're shown as fixed directors in the modal
        $excludedRoles = ['Deputy Director', 'RDD Director'];
        
        foreach ($roleOrder as $roleKey => $meta) {
            // Skip excluded roles
            if (in_array($meta['label'], $excludedRoles)) {
                continue;
            }
            
            // For 'user' role (Applicant), use the request owner's name instead of looking in signatories
            $name = null;
            if ($roleKey === 'user') {
                $name = $userRequest->user->name ?? null;
            } else {
            $nameEntry = collect($signatories)->firstWhere('role', $meta['label']);
                $name = $nameEntry['name'] ?? null;
            }
            
            $signedRecord = $signatureRecords[$roleKey] ?? null;
            $signedAt = $signedRecord && $signedRecord->signed_at
                ? $signedRecord->signed_at->toIso8601String()
                : null;

            $stepOrder = $stateOrder[$meta['state']] ?? 0;
            $status = 'upcoming';
            if ($signedRecord) {
                $status = 'completed';
            } elseif ($currentStateOrder === $stepOrder) {
                $status = 'current';
            } elseif ($currentStateOrder > $stepOrder) {
                $status = 'pending';
            }

            $formattedSignatories[] = [
                'role_key' => $roleKey,
                'role' => $meta['label'],
                'name' => $name,
                'signed_at' => $signedAt,
                'status' => $status,
            ];
        }

        return response()->json([
            'id' => $userRequest->id,
            'request_code' => $userRequest->request_code,
            'type' => $userRequest->type,
            'status' => $userRequest->status,
            'workflow_state' => $userRequest->workflow_state,
            'requested_at' => optional($userRequest->requested_at)->toIso8601String(),
            'user' => $userRequest->user ? [
                'name' => $userRequest->user->name,
                'email' => $userRequest->user->email,
            ] : null,
            'files' => $this->getRequestFiles($userRequest, $signatoryType),
            'signatories' => $formattedSignatories,
            'download_zip_url' => route('signing.download-files', ['requestId' => $userRequest->id]),
            'signatory_type' => $signatoryType,
            'is_admin' => $user->role === 'admin',
        ]);
    }

    public function downloadRequestFile(Request $httpReq, UserRequest $request, string $type, string $key)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            Log::warning('Per-file download forbidden: not authenticated', [
                'request_id' => $request->id ?? null,
                'route_type' => $type,
                'key' => $key,
                'user_id' => $user->id ?? null,
                'reason' => 'user_not_authenticated'
            ]);
            abort(403);
        }

        // Check if user is accessing their own request for signing
        $isUserAccessingOwnRequest = $request->user_id === $user->id && $request->workflow_state === 'pending_user_signature';
        
        if (!$isUserAccessingOwnRequest && !$user->isSignatory()) {
            Log::warning('Per-file download forbidden: not signatory and not own request', [
                'request_id' => $request->id ?? null,
                'route_type' => $type,
                'key' => $key,
                'user_id' => $user->id ?? null,
                'reason' => 'user_not_signatory'
            ]);
            abort(403);
        }

        // Normalize type segment to match stored keys
        // Accept both singular ('pdf','docx') and plural ('pdfs','docxs')
        $originalType = $type;
        if ($type === 'pdf') {
            $type = 'pdfs';
        } elseif ($type === 'docx') {
            $type = 'docxs';
        }
        if ($originalType !== $type) {
            Log::info('Per-file download: normalized type', [
                'request_id' => $request->id,
                'original_type' => $originalType,
                'normalized_type' => $type,
                'key' => $key,
                'user_id' => $user->id
            ]);
        }

        $signatoryType = null;
        if ($isUserAccessingOwnRequest) {
            $signatoryType = 'user';
        } else {
            $signatoryType = $user->signatoryType();
        }
        
        $expectedWorkflowState = $isUserAccessingOwnRequest ? 'pending_user_signature' : $this->getWorkflowStateForSignatory($signatoryType);
        $form = is_array($request->form_data)
            ? $request->form_data
            : (json_decode($request->form_data ?? '[]', true) ?: []);

        $matchedRole = $isUserAccessingOwnRequest ? 'user' : $this->matchesSignatory($form, $signatoryType, trim($user->name ?? ''));
        $hasSigned = RequestSignature::where('request_id', $request->id)
            ->where('user_id', $user->id)
            ->exists();

        // Authorization model A: any signatory (and admin) can view/download files in the review modal
        // We intentionally bypass matchedRole/workflow state checks to align with admin behavior
        Log::info('Per-file download authorization bypass (signatory-level access)', [
            'request_id' => $request->id,
            'user_id' => $user->id,
            'signatory_type' => $signatoryType
        ]);

        $pdfPathData = json_decode($request->pdf_path, true);
        if (!$pdfPathData || !isset($pdfPathData[$type])) {
            Log::warning('Per-file download missing type in pdf_path', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'type' => $type,
                'key' => $key,
                'pdf_path_present' => $pdfPathData ? true : false,
                'reason' => 'type_missing_in_pdf_path'
            ]);
            abort(404);
        }

        $fileInfo = $pdfPathData[$type][$key] ?? null;
        if (!$fileInfo) {
            Log::warning('Per-file download missing file key', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'type' => $type,
                'key' => $key,
                'reason' => 'file_key_missing'
            ]);
            abort(404);
        }

        if (is_array($fileInfo)) {
            $filePath = $fileInfo['path'] ?? null;
            $originalName = $fileInfo['original_name'] ?? ($key . '.pdf');
        } else {
            $filePath = $fileInfo;
            $originalName = $key . '.' . ($type === 'docxs' ? 'docx' : 'pdf');
        }

        if (!$filePath) {
            Log::warning('Per-file download missing file path', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'type' => $type,
                'key' => $key,
                'reason' => 'file_path_missing'
            ]);
            abort(404);
        }

        // Sanitize file path to prevent directory traversal
        $filePath = $this->sanitizePath($filePath);
        
        // Use local disk only (standardized storage)
        $fullPath = Storage::disk('local')->path($filePath);

        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Log::warning('Per-file download file not found or unreadable', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'type' => $type,
                'key' => $key,
                'resolved_path' => $fullPath,
                'reason' => 'file_missing_or_unreadable'
            ]);
            abort(404);
        }

        Log::info('Per-file download success', [
            'request_id' => $request->id,
            'user_id' => $user->id,
            'type' => $type,
            'key' => $key,
            'original_name' => $originalName,
            'path' => $fullPath
        ]);
        return response()->download($fullPath, $originalName);
    }

    public function downloadRequestFiles(Request $request, $requestId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $userRequest = UserRequest::findOrFail($requestId);
        
        // Only allow access to pending requests
        if ($userRequest->status !== 'pending') {
            abort(403, 'This request is not available for signing');
        }
        
        // Check if user is accessing their own request for signing
        $isUserAccessingOwnRequest = $userRequest->user_id === $user->id && $userRequest->workflow_state === 'pending_user_signature';
        
        if ($isUserAccessingOwnRequest) {
            // User can access their own request
        } else if ($user->isSignatory()) {
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
        } else {
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
        if (!$user) {
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

        // Determine if this is a user signing their own request or a signatory
        $signatoryType = null;
        $isUserSigningOwnRequest = false;
        
        if ($userRequest->user_id === $user->id && $userRequest->workflow_state === 'pending_user_signature') {
            // User is signing their own request
            $isUserSigningOwnRequest = true;
            $signatoryType = 'user';
        } else if ($user->isSignatory()) {
            // Regular signatory signing
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
        } else {
            abort(403, 'You are not authorized to sign this request');
        }

        try {
            // Stage file replacements and compute updated pdf_path data; do not persist DB-side changes yet
            $staged = $this->processSignedDocumentsStaged($userRequest, $validated['signed_documents'], $user);
            $uploadedFiles = $staged['uploaded_files'] ?? [];
            $updatedPdfPathData = $staged['updated_pdf_path'] ?? null;
            $backups = $staged['backups'] ?? [];
            $originals = $staged['originals'] ?? [];
            
            if (empty($uploadedFiles)) {
            return response()->json([
                'success' => false,
                    'message' => 'No files were successfully uploaded. Please check that the uploaded files match the original filenames exactly.'
                ], 400);
            }

            // Update request's workflow state and signature status
            $newWorkflowState = $this->getNextWorkflowState($userRequest->workflow_state, $signatoryType);
            
            // Validate workflow state before updating (check against database constraint)
            $validWorkflowStates = [
                'pending_user_signature',
                'pending_research_manager',
                'pending_dean',
                'pending_deputy_director',
                'pending_director',
                'completed'
            ];
            
            if (!in_array($newWorkflowState, $validWorkflowStates)) {
                Log::error('Invalid workflow state generated', [
                    'request_id' => $userRequest->id,
                    'current_state' => $userRequest->workflow_state,
                    'signatory_type' => $signatoryType,
                    'new_state' => $newWorkflowState,
                ]);
                // Restore files if we cannot proceed
                $this->restoreBackupsSafely($backups, $originals, $userRequest->id);
                throw new \Exception('Invalid workflow state: ' . $newWorkflowState);
            }
            
            // Update status to "endorsed" only when director signs (workflow completed)
            $newStatus = ($newWorkflowState === 'completed') ? 'endorsed' : 'pending';
            
            // Persist DB updates atomically so counts match actual success
            DB::beginTransaction();
            try {
            $userRequest->update([
                'workflow_state' => $newWorkflowState,
                'status' => $newStatus,
                'signature_status' => SignatureStatus::SIGNED,
                'signed_at' => now(),
                'signed_by' => $user->id, // Keep the last signatory for backward compatibility
                    // Persist the updated pdf_path along with workflow change
                    'pdf_path' => json_encode($updatedPdfPathData),
                ]);
                
                // Create individual signature record AFTER successful request update
                $signatoryRole = $isUserSigningOwnRequest ? 'user' : ($matchedRole ?? $signatoryType);
                $signatoryName = $isUserSigningOwnRequest ? $user->name : ($userName ?? $user->name);
                
                RequestSignature::create([
                    'request_id' => $userRequest->id,
                    'user_id' => $user->id,
                    'signatory_role' => $signatoryRole,
                    'signatory_name' => $signatoryName,
                    'signed_at' => now(),
                    'signed_document_path' => $uploadedFiles[0]['path'] ?? null, // Store first uploaded file path
                    'original_document_path' => null,
                ]);
                
                DB::commit();
            } catch (\Illuminate\Database\QueryException $dbException) {
                DB::rollBack();
                // Restore files if DB update fails
                $this->restoreBackupsSafely($backups, $originals, $userRequest->id);
                
                // Check if it's a constraint violation
                if (strpos($dbException->getMessage(), 'check constraint') !== false || 
                    strpos($dbException->getMessage(), 'workflow_state_check') !== false) {
                    Log::error('Workflow state constraint violation', [
                        'request_id' => $userRequest->id,
                        'user_id' => $user->id,
                        'current_state' => $userRequest->workflow_state,
                        'attempted_state' => $newWorkflowState,
                        'signatory_type' => $signatoryType,
                        'error' => $dbException->getMessage(),
                    ]);
                    throw new \Exception('Database constraint error: The workflow state "' . $newWorkflowState . '" is not allowed. Please contact support.');
                }
                // Re-throw if it's a different database error
                throw $dbException;
            }
            
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
            
        } catch (\Illuminate\Database\QueryException $dbException) {
            Log::error('Database error uploading signed documents', [
                'request_id' => $userRequest->id ?? null,
                'user_id' => $user->id ?? null,
                'error' => $dbException->getMessage(),
                'code' => $dbException->getCode(),
            ]);
            
            $errorMessage = 'Database error occurred while uploading documents. ';
            if (strpos($dbException->getMessage(), 'check constraint') !== false) {
                $errorMessage = 'Workflow state error: The system cannot transition to the next workflow state. Please contact support.';
            } else {
                $errorMessage .= 'Please try again or contact support if the problem persists.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_type' => 'database_error'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error uploading signed documents', [
                'request_id' => $userRequest->id ?? null,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = $e->getMessage();
            // Provide user-friendly message if it's a known error
            if (strpos($errorMessage, 'Database constraint error') !== false || 
                strpos($errorMessage, 'Invalid workflow state') !== false) {
                $errorMessage = 'Workflow state error: ' . $errorMessage;
            } else {
                $errorMessage = 'Failed to upload signed documents. Please try again.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_type' => 'general_error'
            ], 500);
        }
    }

    private function getRequestFiles(UserRequest $request, ?string $signatoryType = null): array
    {
        $files = [];
        $pdfPathData = json_decode($request->pdf_path, true);

        if (!$pdfPathData) {
            return $files;
        }

        // Determine which files need signing based on signatory type
        $filesNeedingSignature = $this->getFilesNeedingSignature($signatoryType);

        if (isset($pdfPathData['pdfs']) && is_array($pdfPathData['pdfs'])) {
            foreach ($pdfPathData['pdfs'] as $key => $fileInfo) {
                $filePath = null;
                $originalName = null;

                if (is_array($fileInfo)) {
                    $filePath = $fileInfo['path'] ?? null;
                    $originalName = $fileInfo['original_name'] ?? null;
                } elseif (is_string($fileInfo)) {
                    $filePath = $fileInfo;
                }

                if (!$filePath) {
                    continue;
                }

                // Sanitize file path to prevent directory traversal
                $filePath = $this->sanitizePath($filePath);
                
                // Use local disk only (standardized storage)
                $fullPath = Storage::disk('local')->path($filePath);

                if (!file_exists($fullPath) || !is_readable($fullPath)) {
                    continue;
                }

                $downloadUrl = route('signing.request.file', [
                    'request' => $request->id,
                    'type' => 'pdfs',
                    'key' => $key,
                ]);
                Log::info('Generated per-file download URL', [
                    'request_id' => $request->id,
                    'key' => $key,
                    'type' => 'pdfs',
                    'url' => $downloadUrl,
                ]);
                
                // Check if this file needs signing
                $needsSigning = in_array($key, $filesNeedingSignature);

                $files[] = [
                    'name' => $originalName ?: ucfirst(str_replace('_', ' ', $key)) . '.pdf',
                    'path' => $filePath,
                    'type' => 'pdf',
                    'size' => $this->formatFileSize(filesize($fullPath)),
                    'key' => $key,
                    'download_url' => $downloadUrl,
                    'needs_signing' => $needsSigning,
                ];
            }
        }

        if (isset($pdfPathData['docxs']) && is_array($pdfPathData['docxs'])) {
            foreach ($pdfPathData['docxs'] as $key => $fileInfo) {
                $filePath = is_array($fileInfo) ? ($fileInfo['path'] ?? null) : $fileInfo;
                if (!$filePath) {
                    continue;
                }

                // Sanitize file path to prevent directory traversal
                $filePath = $this->sanitizePath($filePath);
                
                // Use local disk only (standardized storage)
                $fullPath = Storage::disk('local')->path($filePath);

                if (!file_exists($fullPath) || !is_readable($fullPath)) {
                    continue;
                }

                // Check if this file needs signing
                $needsSigning = in_array($key, $filesNeedingSignature);

                $files[] = [
                    'name' => ucfirst(str_replace('_', ' ', $key)) . '.docx',
                    'path' => $filePath,
                    'type' => 'docx',
                    'size' => $this->formatFileSize(filesize($fullPath)),
                    'key' => $key,
                    'download_url' => route('signing.request.file', [
                        'request' => $request->id,
                        'type' => 'docxs',
                        'key' => $key,
                    ]),
                    'needs_signing' => $needsSigning,
                ];
            }
        }

        return $files;
    }

    /**
     * Determine which files need signing based on signatory type
     * 
     * @param string|null $signatoryType
     * @return array Array of file keys that need signing
     */
    private function getFilesNeedingSignature(?string $signatoryType): array
    {
        if (!$signatoryType) {
            return [];
        }

        // Most signatories only need to sign the incentive application form
        $defaultFiles = ['incentive'];

        // College dean needs to sign both incentive and recommendation letter
        if ($signatoryType === 'college_dean') {
            return ['incentive', 'recommendation'];
        }

        return $defaultFiles;
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $size = $bytes / (1024 ** $power);

        return number_format($size, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }

    private function extractSignatories($formData): array
    {
        if (is_string($formData)) {
            $decoded = json_decode($formData, true);
            $formData = is_array($decoded) ? $decoded : [];
        }

        if (!$formData || !is_array($formData)) {
            return [];
        }

        $normalized = [];
        foreach ($formData as $k => $v) {
            $normalized[strtolower($k)] = $v;
        }

        $roleToFields = [
            'Faculty' => ['facultyname', 'faculty_name', 'rec_faculty_name'],
            'Research Center Manager' => ['centermanager', 'center_manager', 'research_center_manager'],
            'College Dean' => ['collegedean', 'college_dean', 'dean', 'dean_name', 'rec_dean_name'],
            'Deputy Director' => ['deputy_director', 'deputy_director_name', 'official_deputy_director_name'],
            'RDD Director' => ['rdd_director', 'rdd_director_name', 'official_rdd_director_name'],
        ];

        $signatories = [];
        $seen = [];

        foreach ($roleToFields as $role => $fields) {
            foreach ($fields as $field) {
                if (!array_key_exists($field, $normalized)) {
                    continue;
                }

                $value = trim((string) $normalized[$field]);
                if ($value === '') {
                    continue;
                }

                $lower = mb_strtolower($value);
                if (isset($seen[$lower])) {
                    continue;
                }

                $signatories[] = [
                    'role' => $role,
                    'name' => $value,
                ];
                $seen[$lower] = true;
                break;
            }
        }

        return $signatories;
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
        
        // Update deferred to caller (to ensure atomic DB update with workflow)
        // Return uploaded files and the new pdf_path data for the caller to persist
        return $uploadedFiles;
    }
    
    /**
     * A staged variant returning backups and updated pdf_path without persisting DB-side changes.
     * This allows the caller to atomically update workflow state and pdf_path, and restore on failure.
     */
    private function processSignedDocumentsStaged(UserRequest $userRequest, array $files, User $user): array
    {
        $pdfPathData = json_decode($userRequest->pdf_path, true) ?: [];
        $updatedPdfPathData = $pdfPathData;
        $uploadedFiles = [];
        $backups = [];
        $originals = [];
        $processedFiles = [];
        
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            if (in_array($originalName, $processedFiles)) {
                continue;
            }
            
            $matchingKey = $this->findMatchingKeyInPdfPath($pdfPathData, $originalName, $extension);
            if (!$matchingKey) {
                continue;
            }
            
            $originalPath = $this->getOriginalFilePath($pdfPathData, $matchingKey, $extension);
            $backupPath = $this->createBackupOfOriginalFile($originalPath, $userRequest->id);
            $replaceOk = $this->replaceOriginalFileWithUploaded($originalPath, $file, $userRequest->id);
            if (!$replaceOk) {
                // If replacement failed and backup exists, attempt to keep original intact by restoring
                if ($backupPath) {
                    @copy($backupPath, $this->findFileInStorage($originalPath) ?? $originalPath);
                }
                continue;
            }
            
            // Track for possible restoration on failure
            $backups[] = $backupPath;
            $originals[] = $originalPath;
            
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
            
            $processedFiles[] = $originalName;
        }
        
        return [
            'uploaded_files' => $uploadedFiles,
            'updated_pdf_path' => $updatedPdfPathData,
            'backups' => $backups,
            'originals' => $originals,
        ];
    }
    
    private function restoreBackupsSafely(array $backupPaths, array $originalPaths, int $requestId): void
    {
        // Attempt to restore each backup to its original file
        foreach ($backupPaths as $idx => $backup) {
            $original = $originalPaths[$idx] ?? null;
            if (!$backup || !$original) {
                continue;
            }
            try {
                $originalFull = $this->findFileInStorage($original) ?? $original;
                if (file_exists($backup)) {
                    @copy($backup, $originalFull);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to restore backup after DB failure', [
                    'request_id' => $requestId,
                    'backup' => $backup,
                    'original' => $original,
                    'error' => $e->getMessage(),
                ]);
            }
        }
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
                case 'pending_research_manager':
                    // Find the Research Manager (center_manager) from form data
                    $form = is_array($request->form_data) ? $request->form_data : (json_decode($request->form_data ?? '[]', true) ?: []);
                    $signatories = $this->extractSignatories($form);
                    foreach ($signatories as $signatory) {
                        if ($signatory['role'] === 'Research Center Manager' || $signatory['role'] === 'center_manager') {
                            $nextSignatory = \App\Models\User::where('name', trim($signatory['name']))->first();
                            $signatoryName = trim($signatory['name']);
                            break;
                        }
                    }
                    break;
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
                // Determine role for email template
                $roleForEmail = 'college_dean';
                if ($workflowState === 'pending_research_manager') {
                    $roleForEmail = 'center_manager';
                }
                \Illuminate\Support\Facades\Mail::to($nextSignatory->email)->queue(new \App\Mail\SignatoryNotification($request, $roleForEmail, $signatoryName));
                
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
    
    /**
     * Delete all files for a request (Redo action for center manager)
     */
    public function redoRequest(Request $httpRequest, $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Log what we received
        Log::info('Redo request received', [
            'request_param_type' => gettype($request),
            'request_param_value' => $request instanceof UserRequest ? $request->id : $request,
            'route_params' => $httpRequest->route()->parameters(),
            'user_id' => $user->id,
        ]);

        // Resolve the request - handle both route model binding and manual lookup
        if ($request instanceof UserRequest) {
            $userRequest = $request;
        } else {
            // If route model binding didn't work, try to find by ID
            $requestId = is_numeric($request) ? $request : ($httpRequest->route('request') ?? $request);
            if (!$requestId) {
                Log::error('Redo request ID not found', [
                    'request_param' => $request,
                    'route_params' => $httpRequest->route()->parameters(),
                    'user_id' => $user->id,
                ]);
                abort(404, 'Request ID not provided');
            }
            $userRequest = UserRequest::find($requestId);
            if (!$userRequest) {
                Log::error('Redo request not found in database', [
                    'request_id' => $requestId,
                    'user_id' => $user->id,
                ]);
                abort(404, 'Request not found');
            }
        }

        // Check if user is the request owner and can redo their own request
        $isUserOwnRequest = $userRequest->user_id === $user->id && $userRequest->workflow_state === 'pending_user_signature';
        
        // Check if user is a signatory
        $isSignatory = $user->isSignatory();
        $signatoryType = $isSignatory ? $user->signatoryType() : null;
        
        // Allow redo if:
        // 1. User is the request owner and request is in pending_user_signature state
        // 2. User is a center manager (for their workflow stage)
        // 3. User is an admin
        if (!$isUserOwnRequest && !$isSignatory && $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        
        if (!$isUserOwnRequest && $isSignatory) {
            // For signatories, only center managers can perform redo action
            if ($signatoryType !== 'center_manager' && $user->role !== 'admin') {
                abort(403, 'Only center managers or admins can perform this action');
            }
        }

        // Validate reason is provided
        $validated = $httpRequest->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        // Admin/Center Manager parity: no name-match requirement for redo

        // Refresh the request from database to ensure we have the latest status
        $userRequest->refresh();
        
        // Log the request status for debugging
        Log::info('Redo request check', [
            'request_id' => $userRequest->id,
            'status' => $userRequest->status,
            'workflow_state' => $userRequest->workflow_state,
            'user_id' => $user->id,
            'signatory_type' => $signatoryType,
        ]);
        
        // Only allow redo on pending requests (not endorsed or rejected)
        // Check workflow state based on user type
        if ($isUserOwnRequest) {
            // User can redo their own request if it's in pending_user_signature state
            $isInCorrectWorkflowState = $userRequest->workflow_state === 'pending_user_signature';
        } else if ($isSignatory && $signatoryType) {
            // Signatory can redo if workflow state matches their expected stage
            $expectedWorkflowState = $this->getWorkflowStateForSignatory($signatoryType);
            $isInCorrectWorkflowState = $userRequest->workflow_state === $expectedWorkflowState;
        } else {
            // Admin can redo any pending request
            $isInCorrectWorkflowState = true;
        }
        
        $isPending = strtolower(trim($userRequest->status)) === 'pending';
        
        if (!$isPending && !$isInCorrectWorkflowState) {
            Log::warning('Redo blocked: request not pending and not in correct workflow state', [
                'request_id' => $userRequest->id,
                'actual_status' => $userRequest->status,
                'workflow_state' => $userRequest->workflow_state,
                'is_user_own_request' => $isUserOwnRequest,
                'is_pending' => $isPending,
                'is_in_correct_workflow_state' => $isInCorrectWorkflowState,
            ]);
            abort(403, 'This action can only be performed on pending requests in your workflow stage');
        }
        
        // Additional check: don't allow redo on endorsed requests
        if (strtolower(trim($userRequest->status)) === 'endorsed') {
            Log::warning('Redo blocked: request is endorsed', [
                                    'request_id' => $userRequest->id,
                'status' => $userRequest->status,
            ]);
            abort(403, 'Cannot redo an endorsed request');
        }

        try {
            $requestId = $userRequest->id;
            $requestCode = $userRequest->request_code;
            $userId = $userRequest->user_id;
            
            // Get user info before deleting the request
            $requestUser = $userRequest->user;
            $signedDocumentPath = $userRequest->signed_document_path;
            $originalDocumentPath = $userRequest->original_document_path;
            
            // Delete the entire folder containing all files
            if ($userId && $requestCode) {
                $dir = "requests/{$userId}/{$requestCode}";
                
                // Delete from local storage
                if (Storage::disk('local')->exists($dir)) {
                    Storage::disk('local')->deleteDirectory($dir);
                    Log::info('Deleted request folder from local storage during redo', [
                        'request_id' => $requestId,
                        'dir' => $dir
                    ]);
                }
                
                // Delete from public storage
                if (Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->deleteDirectory($dir);
                    Log::info('Deleted request folder from public storage during redo', [
                        'request_id' => $requestId,
                        'dir' => $dir
                                ]);
                            }
                        }
            
            // Delete signed document folders if they exist
            if ($signedDocumentPath) {
                $signedDir = dirname($signedDocumentPath);
                if (Storage::disk('local')->exists($signedDir)) {
                    Storage::disk('local')->deleteDirectory($signedDir);
                    Log::info('Deleted signed document folder during redo', [
                        'request_id' => $requestId,
                        'dir' => $signedDir
                    ]);
                }
            }
            
            if ($originalDocumentPath) {
                $backupDir = dirname($originalDocumentPath);
                if (Storage::disk('local')->exists($backupDir)) {
                    Storage::disk('local')->deleteDirectory($backupDir);
                    Log::info('Deleted original document folder during redo', [
                        'request_id' => $requestId,
                        'dir' => $backupDir
                    ]);
                }
            }
            
            // Delete all RequestSignature records for this request
            $deletedSignatures = RequestSignature::where('request_id', $requestId)->delete();
            Log::info('Deleted request signatures during redo', [
                'request_id' => $requestId,
                'deleted_count' => $deletedSignatures
            ]);
            
            // Delete the request from the database
            $userRequest->delete();
            Log::info('Deleted request from database during redo', [
                'request_id' => $requestId,
                'request_code' => $requestCode
            ]);

            // Note: Email notification skipped since request is completely deleted
            // This is a permanent deletion, not a resubmission request

            // Log the action
            Log::info('Center manager redo action performed - request completely deleted', [
                'request_id' => $requestId,
                'request_code' => $requestCode,
                'center_manager_id' => $user->id,
                'center_manager_name' => $user->name,
                'reason' => $validated['reason'],
                'deleted_signatures_count' => $deletedSignatures
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request and all associated files have been permanently deleted.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error performing redo action', [
                'request_id' => $requestId ?? null,
                'center_manager_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete request: ' . $e->getMessage()
            ], 500);
        }
    }
    
} 