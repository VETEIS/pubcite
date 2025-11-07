<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Request as UserRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\TemplateProcessor;
// use App\Services\TemplateCacheService; // Disabled due to serialization issues with TemplateProcessor
use App\Services\TemplatePreloader;
// use App\Http\Controllers\Traits\DraftSessionManager; // Temporarily disabled for production fix
use App\Mail\SubmissionNotification;
use App\Mail\StatusChangeNotification;
use App\Services\DocxToPdfConverter;

class CitationsController extends Controller
{
    // use DraftSessionManager; // Temporarily disabled for production fix
    public function create()
    {
        $citations_request_enabled = \App\Models\Setting::get('citations_request_enabled', '1');
        
        if ($citations_request_enabled !== '1') {
            return redirect()->route('dashboard')->with('error', 'Citations requests are currently disabled by administrators.');
        }
        
        // Check for existing draft
        $existingDraft = \App\Models\Request::where('user_id', Auth::id())
            ->where('type', 'Citation')
            ->where('status', 'draft')
            ->orderBy('id', 'desc')
            ->first();
            
        Log::info('Citations create - checking for draft', [
            'user_id' => Auth::id(),
            'found_draft' => $existingDraft ? true : false,
            'draft_id' => $existingDraft ? $existingDraft->id : null,
            'draft_status' => $existingDraft ? $existingDraft->status : null,
            'draft_type' => $existingDraft ? $existingDraft->type : null,
            'form_data' => $existingDraft ? $existingDraft->form_data : null
        ]);
            
        $request = new \stdClass();
        $request->id = $existingDraft ? $existingDraft->id : null;
        $request->request_code = $existingDraft ? $existingDraft->request_code : null;
        $request->form_data = $existingDraft ? (is_string($existingDraft->form_data) ? json_decode($existingDraft->form_data, true) : $existingDraft->form_data) : [];
        
        // Show notification if draft was loaded
        if ($existingDraft) {
            return view('citations.request', compact('request'))->with('success', 'Draft loaded successfully!');
        }
        
        return view('citations.request', compact('request'));
    }

    public function adminUpdate(Request $httpRequest, \App\Models\Request $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,endorsed,rejected',
        ]);
        $oldStatus = $request->status;
        $request->status = $httpRequest->input('status');
        $request->save();

        if ($oldStatus !== $request->status) {
            $adminComment = $httpRequest->input('admin_comment', null);
            Mail::to($request->user->email)->send(new StatusChangeNotification($request, $request->user, $request->status, $adminComment));
            
            // If status changed to 'endorsed', notify signatories
            if ($request->status === 'endorsed') {
                $this->notifySignatories($request);
            }
        }

        return back()->with('success', 'Request status updated successfully.');
    }

    public function generateCitationDocx(Request $request)
    {
        $citations_request_enabled = \App\Models\Setting::get('citations_request_enabled', '1');
        
        if ($citations_request_enabled !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Citations requests are currently disabled by administrators.'
            ], 403);
        }
        
        try {
            $data = $request->all();
            
            // Filter out error messages and invalid data
            $data = array_filter($data, function($value, $key) {
                // Skip error messages and system fields
                if (is_string($value) && (
                    strpos($value, 'false message') !== false ||
                    strpos($value, 'Please wait before submitting') !== false ||
                    strpos($value, 'Validation failed') !== false
                )) {
                    return false;
                }
                // Skip system fields
                if (in_array($key, ['_token', 'docx_type', 'store_for_submit', 'request_id', 'save_draft'])) {
                    return false;
                }
                return true;
            }, ARRAY_FILTER_USE_BOTH);
            
            $docxType = $request->input('docx_type', 'incentive');
            $storeForSubmit = $request->input('store_for_submit', false);
            $reqId = $request->input('request_id');
            
            $isPreview = !$reqId;
            
            if ($isPreview) {
                // Simple temp path for immediate preview - no complex directory structure
                $uploadPath = "temp/preview_" . time();
            } else {
                $userRequest = \App\Models\Request::find($reqId);
                if (!$userRequest) {
                    throw new \Exception('Request not found for DOCX generation');
                }
                $requestCode = $userRequest->request_code;
                $userId = $userRequest->user_id;
                $uploadPath = "requests/{$userId}/{$requestCode}";
                Log::info('Generating Citation DOCX for saved request', ['request_id' => $reqId, 'request_code' => $requestCode]);
            }
            
            // Add fallback data if form data is corrupted
            $fallbackData = [
                'name' => 'Sample Name',
                'rank' => 'Sample Rank', 
                'college' => 'Sample College',
                'bibentry' => 'Sample Bibliography Entry',
                'issn' => 'Sample ISSN',
                'doi' => 'Sample DOI',
                'scopus' => '1',
                'wos' => '1',
                'aci' => '1',
                'facultyname' => 'Sample Faculty',
                'centermanager' => 'Sample Manager',
                'dean' => 'Sample Dean',
                'date' => date('F j, Y'),
                'title' => 'Sample Title',
                'journal' => 'Sample Journal',
                'publisher' => 'Sample Publisher',
                'citescore' => 'Sample CiteScore',
                'citedbibentry' => 'Sample Cited Bibliography'
            ];
            
            // Merge fallback data with filtered form data
            $data = array_merge($fallbackData, $data);
            
            Log::info('Citation DOCX generation - Received data:', ['type' => $docxType, 'data' => $data, 'isPreview' => $isPreview]);
            
            $hashSource = json_encode([
                'type' => $docxType,
                'data' => $data
            ]);
            $uniqueHash = substr(hash('sha256', $hashSource), 0, 16); // 16 chars is enough
            $filename = null;
            $fullPath = null;
            
            switch ($docxType) {
                case 'incentive':
                    $filtered = $this->mapIncentiveFields($data);
                    Log::info('Filtered data for incentive', ['filtered' => $filtered]);
                    $fullPath = $this->generateCitationIncentiveDocxFromHtml($filtered, $uploadPath, false); // No PDF conversion for preview
                    $filename = 'Incentive_Application_Form.docx';
                    break;
                    
                case 'recommendation':
                    $filtered = $this->mapRecommendationFields($data);
                    Log::info('Filtered data for recommendation', ['filtered' => $filtered]);
                    $fullPath = $this->generateCitationRecommendationDocxFromHtml($filtered, $uploadPath, false); // No PDF conversion for preview
                    $filename = 'Recommendation_Letter_Form.docx';
                    break;
                    
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            
            $absolutePath = Storage::disk('local')->path($fullPath);
            if (!file_exists($absolutePath)) {
                throw new \Exception('Generated file not found at: ' . $absolutePath);
            }
            
            Log::info('Citation DOCX generated and found, ready to serve', ['type' => $docxType, 'path' => $fullPath, 'isPreview' => $isPreview]);
            
            // If storing for submit, return file path instead of downloading
            if ($storeForSubmit && $isPreview) {
                return response()->json([
                    'success' => true,
                    'filePath' => $fullPath,
                    'filename' => $filename
                ]);
            }
            
            // Otherwise, download the file
            return response()->download($absolutePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating Citation DOCX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateCitationIncentiveDocxFromHtml($data, $uploadPath, $convertToPdf = false)
    {
        try {
            Log::info('Starting generateCitationIncentiveDocxFromHtml', ['uploadPath' => $uploadPath]);
            
            $privateUploadPath = $uploadPath; // uploadPath is already in correct format
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            
            $templatePath = storage_path('app/templates/Cite_Incentive_Application.docx');
            $filename = 'Incentive_Application_Form.docx';
            $outputPath = $privateUploadPath . '/' . $filename;
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            // Use direct TemplateProcessor (caching disabled due to serialization issues)
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }
            
            $signaturePlaceholders = [
                'facultysignature' => '${facultysignature}',
                'centermanagersignature' => '${centermanagersignature}',
                'deansignature' => '${deansignature}',
                'deputydirectorsignature' => '${deputydirectorsignature}',
                'directorsignature' => '${directorsignature}'
            ];
            
            foreach ($signaturePlaceholders as $key => $placeholder) {
                $templateProcessor->setValue($key, $placeholder);
            }
            
            try {
                Log::info('About to save DOCX', ['fullOutputPath' => $fullOutputPath]);
                $templateProcessor->saveAs($fullOutputPath);
                Log::info('DOCX saved successfully', ['path' => $fullOutputPath]);
            } catch (\Exception $e) {
                Log::error('Exception during DOCX save', ['error' => $e->getMessage(), 'path' => $fullOutputPath]);
                throw $e;
            }
            
            // Only convert to PDF if requested (for submission, not preview)
            if ($convertToPdf) {
                $converter = new DocxToPdfConverter();
                $pdfPath = $converter->convertDocxToPdf($outputPath, $privateUploadPath);
                
                if ($pdfPath) {
                    // Delete the original DOCX file
                    Storage::disk('local')->delete($outputPath);
                    Log::info('Converted DOCX to PDF and deleted original DOCX', [
                        'original_docx' => $outputPath,
                        'generated_pdf' => $pdfPath
                    ]);
                    return $pdfPath;
                } else {
                    Log::warning('PDF conversion failed, keeping original DOCX', ['docx_path' => $outputPath]);
                    return $outputPath;
                }
            }
            
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Error in generateCitationIncentiveDocxFromHtml: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    public function generateCitationRecommendationDocxFromHtml($data, $uploadPath, $convertToPdf = false)
    {
        try {
            Log::info('CITATION RECO: Raw data', $data);
            Log::info('CITATION RECO: Using mapped data', $data);
            
            $privateUploadPath = $uploadPath; // uploadPath is already in correct format
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            
            $templatePath = storage_path('app/templates/Cite_Recommendation_Letter.docx');
            $filename = 'Recommendation_Letter_Form.docx';
            $outputPath = $privateUploadPath . '/' . $filename;
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            // Use direct TemplateProcessor (caching disabled due to serialization issues)
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }
            
            $signaturePlaceholders = [
                'facultysignature' => '${facultysignature}',
                'centermanagersignature' => '${centermanagersignature}',
                'deansignature' => '${deansignature}',
                'deputydirectorsignature' => '${deputydirectorsignature}',
                'directorsignature' => '${directorsignature}'
            ];
            
            foreach ($signaturePlaceholders as $key => $placeholder) {
                $templateProcessor->setValue($key, $placeholder);
            }
            
            $templateProcessor->saveAs($fullOutputPath);
            Log::info('CITATION RECO: Saved populated docx', ['output' => $fullOutputPath]);
            
            // Only convert to PDF if requested (for submission, not preview)
            if ($convertToPdf) {
                $converter = new DocxToPdfConverter();
                $pdfPath = $converter->convertDocxToPdf($outputPath, $privateUploadPath);
                
                if ($pdfPath) {
                    // Delete the original DOCX file
                    Storage::disk('local')->delete($outputPath);
                    Log::info('CITATION RECO: Converted DOCX to PDF and deleted original DOCX', [
                        'original_docx' => $outputPath,
                        'generated_pdf' => $pdfPath
                    ]);
                    return $pdfPath;
                } else {
                    Log::warning('CITATION RECO: PDF conversion failed, keeping original DOCX', ['docx_path' => $outputPath]);
                    return $outputPath;
                }
            }
            
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('CITATION RECO: Error generating docx', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                return back()->with('error', 'Unauthorized.');
            }
            
            $request = UserRequest::findOrFail($id);
            
            $pdfPath = $request->pdf_path ? json_decode($request->pdf_path, true) : [];
            $allFiles = [];
            
            if (isset($pdfPath['pdfs']) && is_array($pdfPath['pdfs'])) {
                foreach ($pdfPath['pdfs'] as $file) {
                    if (isset($file['path'])) {
                        $allFiles[] = $file['path'];
                    }
                }
            }
            
            if (isset($pdfPath['docxs']) && is_array($pdfPath['docxs'])) {
                foreach ($pdfPath['docxs'] as $docxPath) {
                    if ($docxPath) {
                        $allFiles[] = $docxPath;
                    }
                }
            }
            
            if ($request->signed_document_path) {
                $allFiles[] = $request->signed_document_path;
            }
            if ($request->original_document_path) {
                $allFiles[] = $request->original_document_path;
            }
            
            foreach ($allFiles as $filePath) {
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                    Storage::disk('local')->delete($filePath);
                }
            }
            
            if (isset($request->user_id) && isset($request->request_code)) {
                $dir = "requests/{$request->user_id}/{$request->request_code}";
                
                // Delete from local storage
                if (Storage::disk('local')->exists($dir)) {
                    Storage::disk('local')->deleteDirectory($dir);
                    Log::info('Deleted request folder from local storage', ['dir' => $dir]);
                }
                
                // Delete from public storage
                if (Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->deleteDirectory($dir);
                    Log::info('Deleted request folder from public storage', ['dir' => $dir]);
                }
            }
            
            if ($request->signed_document_path) {
                $signedDir = dirname($request->signed_document_path);
                if (Storage::disk('local')->exists($signedDir)) {
                    Storage::disk('local')->deleteDirectory($signedDir);
                }
            }
            
            if ($request->original_document_path) {
                $backupDir = dirname($request->original_document_path);
                if (Storage::disk('local')->exists($backupDir)) {
                    Storage::disk('local')->deleteDirectory($backupDir);
                }
            }
            
            $requestDetails = [
                'request_code' => $request->request_code,
                'type' => $request->type,
                'status' => $request->status,
                'user_name' => $request->user->name ?? 'Unknown User',
                'user_email' => $request->user->email ?? 'Unknown Email',
                'form_data' => is_string($request->form_data) ? json_decode($request->form_data, true) : $request->form_data,
                'pdf_path' => is_string($request->pdf_path) ? json_decode($request->pdf_path, true) : $request->pdf_path,
                'deleted_at' => now()->toDateTimeString(),
                'deleted_by_admin' => $user->name,
                'deleted_by_admin_id' => $user->id,
            ];
            
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'request_id' => $request->id,
                    'action' => 'deleted',
                    'details' => $requestDetails,
                    'created_at' => now(),
                ]);
                
                Log::info('Activity log created successfully for request deletion', [
                    'request_id' => $request->id,
                    'request_code' => $request->request_code,
                    'deleted_by_user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create activity log for request deletion: ' . $e->getMessage(), [
                    'request_id' => $request->id,
                    'request_code' => $request->request_code,
                    'deleted_by_user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Delete associated admin notifications for this request
            \App\Models\AdminNotification::where('request_id', $request->id)->delete();
            
            $request->delete();
            return back()->with('success', 'Request and files deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting request: ' . $e->getMessage());
            return back()->with('error', 'Error deleting request.');
        }
    }

    public function submitCitationRequest(Request $request)
    {
        $user = Auth::user();
        
        Log::info('Citation request submission started', [
            'user_id' => $user->id,
            'has_files' => $request->hasFile('citing_article') || $request->hasFile('cited_article') || $request->hasFile('indexing_evidence'),
            'is_draft' => $request->has('save_draft')
        ]);
        
        $citations_request_enabled = \App\Models\Setting::get('citations_request_enabled', '1');
        
        if ($citations_request_enabled !== '1') {
            return redirect()->route('dashboard')->with('error', 'Citations requests are currently disabled by administrators.');
        }
        
        // Check if this is a draft save
        $isDraft = $request->has('save_draft');
        
        // Prevent duplicate submissions for both drafts and final submissions
        $recentSubmission = \App\Models\Request::where('user_id', $user->id)
            ->where('type', 'Citation')
            ->where('status', $isDraft ? 'draft' : 'pending')
            ->where('requested_at', '>=', now()->subSeconds(10)) // Within last 10 seconds
            ->first();
            
        if ($recentSubmission) {
            Log::info('Duplicate citation submission prevented', [
                'user_id' => $user->id,
                'is_draft' => $isDraft,
                'recent_submission_id' => $recentSubmission->id,
                'recent_submission_time' => $recentSubmission->requested_at
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate submission prevented. Please wait a moment before trying again.'
                ], 429);
            } else {
                return back()->with('error', 'Duplicate submission prevented. Please wait a moment before trying again.');
            }
        }
        
        try {
            // Different validation rules for draft vs final submission
            if ($isDraft) {
                // For drafts, make most fields optional
                $validationRules = [
                    'name' => 'nullable|string',
                    'rank' => 'nullable|string',
                    'college' => 'nullable|string',
                    'bibentry' => 'nullable|string',
                    'citedbibentry' => 'nullable|string',
                    'issn' => 'nullable|string',
                    'doi' => 'nullable|string',
                    'scopus' => 'nullable',
                    'wos' => 'nullable',
                    'aci' => 'nullable',
                    'faculty_name' => 'nullable|string',
                    'center_manager' => 'nullable|string',
                    'dean_name' => 'nullable|string',
                    'rec_collegeheader' => 'nullable|string',
                    'date' => 'nullable|string',
                    'rec_faculty_name' => 'nullable|string',
                    'rec_citing_details' => 'nullable|string',
                    'rec_indexing_details' => 'nullable|string',
                    'rec_dean_name' => 'nullable|string',
                ];
            } else {
                // For final submission, require all fields
                $validationRules = [
                    'name' => 'required|string',
                    'rank' => 'required|string',
                    'college' => 'required|string',
                    'bibentry' => 'required|string',
                    'citedbibentry' => 'required|string',
                    'issn' => 'required|string',
                    'doi' => 'nullable|string',
                    'scopus' => 'nullable',
                    'wos' => 'nullable',
                    'aci' => 'nullable',
                    'faculty_name' => 'required|string',
                    'center_manager' => 'nullable|string',
                    'dean_name' => 'required|string',
                    'rec_collegeheader' => 'required|string',
                    'date' => 'required|string',
                    'rec_faculty_name' => 'required|string',
                    'rec_citing_details' => 'required|string',
                    'rec_indexing_details' => 'required|string',
                    'rec_dean_name' => 'required|string',
                ];
            }
            
            // Only require files for final submission, not for draft
            if (!$isDraft) {
                $validationRules = array_merge($validationRules, [
                    'citing_article' => 'required|file|mimes:pdf|max:20480',
                    'cited_article' => 'required|file|mimes:pdf|max:20480',
                    'indexing_evidence' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
                ]);
            } else {
                // For drafts, make files optional
                $validationRules = array_merge($validationRules, [
                    'citing_article' => 'nullable|file|mimes:pdf|max:20480',
                    'cited_article' => 'nullable|file|mimes:pdf|max:20480',
                    'indexing_evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
                ]);
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                Log::info('Citation request validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                // Return JSON response for draft saves, HTML redirect for regular submissions
                if ($isDraft) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()->toArray()
                    ], 422);
                } else {
                    return back()->withErrors($validator)->withInput();
                }
            }

            $userId = $user->id;
            
            // Check for existing draft first (with session optimization)
            $existingDraft = null;
            
            // First check session for draft ID (faster than database query)
            $draftId = session("draft_citation_{$userId}");
            if ($draftId) {
                $existingDraft = \App\Models\Request::where('id', $draftId)
                    ->where('user_id', $userId)
                    ->where('type', 'Citation')
                    ->where('status', 'draft')
                    ->first();
            }
            
            // Fallback to database query if session doesn't have draft ID
            if (!$existingDraft) {
                $existingDraft = \App\Models\Request::where('user_id', $userId)
                    ->where('type', 'Citation')
                    ->where('status', 'draft')
                    ->first();
                
                // Store draft ID in session for future requests
                if ($existingDraft) {
                    session(["draft_citation_{$userId}" => $existingDraft->id]);
                }
            }
            
            if ($existingDraft && $isDraft) {
                // Reuse existing draft directory
                $requestCode = $existingDraft->request_code;
                $uploadPath = "requests/{$userId}/{$requestCode}";
                Log::info('Reusing existing citation draft directory', [
                    'request_id' => $existingDraft->id,
                    'request_code' => $requestCode,
                    'upload_path' => $uploadPath
                ]);
            } else {
                // Generate new request code only for new requests
                $requestCode = 'CITE-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $uploadPath = "requests/{$userId}/{$requestCode}";
                Log::info('Creating new citation request directory', [
                    'request_code' => $requestCode,
                    'upload_path' => $uploadPath,
                    'is_draft' => $isDraft
                ]);
            }

            // Define preview status (this method is for submissions, not previews)
            $isPreview = false;
            
            // Skip file processing for previews - only process for actual submissions
            if (!$isPreview) {
                // Store PDF files in per-request folder
                $pdfPaths = [];
                $fields = [
                    'citing_article',
                    'cited_article',
                    'indexing_evidence',
                ];
                foreach ($fields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $storedPath = $file->storeAs($uploadPath, $file->getClientOriginalName(), 'local');
                    // Store path as is since we're using private disk
                    $cleanPath = $storedPath;
                    $pdfPaths[$field] = [
                        'path' => $cleanPath,
                        'original_name' => $file->getClientOriginalName()
                    ];
                } elseif (!$isDraft) {
                    // Only require files for final submission
                    throw new \Exception("File upload failed for field: {$field}");
                }
            }

            // Generate DOCX files only for final submission
            $docxPaths = [];
            if (!$isDraft) {
                try {
                    $filtered = $this->mapIncentiveFields($request->all());
                    $incentivePath = $this->generateCitationIncentiveDocxFromHtml($filtered, $uploadPath, true);
                    if ($incentivePath) {
                        $docxPaths['incentive'] = [
                            'path' => $incentivePath,
                            'original_name' => 'Incentive_Application_Form.pdf'
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Error generating incentive DOCX: ' . $e->getMessage());
                }
                try {
                    $filtered = $this->mapRecommendationFields($request->all());
                    $recommendationPath = $this->generateCitationRecommendationDocxFromHtml($filtered, $uploadPath, true);
                    if ($recommendationPath) {
                        $docxPaths['recommendation'] = [
                            'path' => $recommendationPath,
                            'original_name' => 'Recommendation_Letter_Form.pdf'
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Error generating recommendation DOCX: ' . $e->getMessage());
                }
            }
            } else {
                // For previews, initialize empty arrays
                $pdfPaths = [];
                $docxPaths = [];
            }

            // Check if this is an update to existing draft
            $existingDraft = \App\Models\Request::where('user_id', $userId)
                ->where('type', 'Citation')
                ->where('status', 'draft')
                ->orderBy('id', 'desc')
                ->first();
                
            if ($existingDraft && $isDraft) {
                // Update existing draft - reuse the same directory
                $existingDraft->update([
                    'form_data' => json_encode($request->except(['_token', ...$fields])),
                    'pdf_path' => json_encode([
                        'pdfs' => array_merge($pdfPaths, $docxPaths),
                        'docxs' => [], // Drafts don't have DOCX files
                    ]),
                    'requested_at' => now(), // Update timestamp
                ]);
                $userRequest = $existingDraft;
                Log::info('Updated existing citation draft', [
                    'request_id' => $existingDraft->id,
                    'request_code' => $existingDraft->request_code
                ]);
            } else {
                // Create new request
                $userRequest = new UserRequest();
                $userRequest->user_id = $userId;
                $userRequest->request_code = $requestCode;
                $userRequest->type = 'Citation';
                $userRequest->status = $isDraft ? 'draft' : 'pending';
                $userRequest->requested_at = now(); // Always set requested_at, even for drafts
                $userRequest->pdf_path = json_encode([
                    'pdfs' => array_merge($pdfPaths, $docxPaths),
                    'docxs' => [], // All files are now PDFs after conversion
                ]);
                $userRequest->form_data = json_encode($request->except(['_token', ...$fields]));
                $userRequest->save();
                Log::info('Created new citation request', [
                    'request_id' => $userRequest->id,
                    'request_code' => $requestCode,
                    'is_draft' => $isDraft
                ]);
                
                // Store new draft ID in session
                if ($isDraft) {
                    session(["draft_citation_{$userId}" => $userRequest->id]);
                }
            }

            // Activity log creation removed - now handled by notification bell system

            Log::info('Citation request submitted successfully', [
                'requestId' => $userRequest->id,
                'requestCode' => $requestCode,
                'isDraft' => $isDraft,
                'pdf_count' => count($pdfPaths),
                'docx_count' => count($docxPaths)
            ]);

            // Only send emails and create notifications for final submission, not for drafts
            if (!$isDraft) {
                // Send email to user confirming submission
                try {
                    Mail::to($user->email)->queue(new SubmissionNotification($userRequest, $userRequest->user, false));
                    Log::info('User submission confirmation email queued', [
                        'requestId' => $userRequest->id,
                        'userEmail' => $user->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error queuing user confirmation email: ' . $e->getMessage());
                }
                
                // Notify only the first signatory (Research Manager) in the workflow
                $this->notifyFirstSignatory($userRequest);
            }

            if ($isDraft) {
                Log::info('Citation draft saved successfully', [
                    'request_id' => $userRequest->id,
                    'request_code' => $userRequest->request_code,
                    'form_data' => $userRequest->form_data
                ]);
                
                // Check if this is an AJAX request (auto-save) or form submission (manual draft save)
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Draft saved successfully!']);
                } else {
                    // For manual draft saves via form submission, redirect without notification
                    return redirect()->route('citations.request');
                }
            } else {
                // Clear draft session when submitting final request
                session()->forget("draft_citation_{$userId}");
                return redirect()->route('dashboard')->with('success', 'Citation request submitted successfully! Request Code: ' . $requestCode);
            }
        } catch (\Exception $e) {
            Log::error('Error submitting citation request: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while submitting your request. Please try again.');
        }
    }

    public function success()
    {
        $requestCode = session('request_code');
        if (!$requestCode) {
            return redirect()->route('citations.request');
        }
        return view('citations.success', compact('requestCode'));
    }

    public function adminDownloadFile(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $type = $httpRequest->query('type');
            $key = $httpRequest->query('key');
            
            if (!$type || !$key) {
                return response()->json(['error' => 'Missing type or key parameter'], 400);
            }
            
            $pdfData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
            
            if (!isset($pdfData['pdfs'][$key])) {
                return response()->json(['error' => 'File not found'], 404);
            }
            
            $filePath = $pdfData['pdfs'][$key]['path'];
            
            // Check both local and public disks for generated PDFs
            if (Storage::disk('local')->exists($filePath)) {
                $fullPath = Storage::disk('local')->path($filePath);
            } elseif (Storage::disk('public')->exists($filePath)) {
                $fullPath = Storage::disk('public')->path($filePath);
            } else {
                return response()->json(['error' => 'File not found on disk'], 404);
            }
            
            return response()->download($fullPath, $pdfData['pdfs'][$key]['original_name']);
            
        } catch (\Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage());
            return response()->json(['error' => 'Error downloading file'], 500);
        }
    }

    public function serveFile(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $type = $httpRequest->query('type');
            $key = $httpRequest->query('key');
            
            if (!$type || !$key) {
                return response()->json(['error' => 'Missing type or key parameter'], 400);
            }
            
            if ($type === 'pdf') {
                $pdfData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                
                if (!isset($pdfData['pdfs'][$key])) {
                    return response()->json(['error' => 'File not found'], 404);
                }
                
                $filePath = $pdfData['pdfs'][$key]['path'];
                
                // Check both local and public disks for generated PDFs
                if (Storage::disk('local')->exists($filePath)) {
                    $fullPath = Storage::disk('local')->path($filePath);
                } elseif (Storage::disk('public')->exists($filePath)) {
                    $fullPath = Storage::disk('public')->path($filePath);
                } else {
                    return response()->json(['error' => 'File not found on disk'], 404);
                }
                
                return response()->file($fullPath);
                
            } elseif ($type === 'docx') {
                $docxData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                
                if (!isset($docxData['docxs'][$key])) {
                    return response()->json(['error' => 'File not found'], 404);
                }
                
                $filePath = $docxData['docxs'][$key];
                $fullPath = storage_path('app/' . $filePath);
                
                if (!file_exists($fullPath)) {
                    return response()->json(['error' => 'File not found on disk'], 404);
                }
                
                return response()->file($fullPath);
            }
            
            return response()->json(['error' => 'Invalid file type'], 400);
            
        } catch (\Exception $e) {
            Log::error('Error serving file: ' . $e->getMessage());
            return response()->json(['error' => 'Error serving file'], 500);
        }
    }

    public function debugFilePaths(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $debugInfo = [
                'request_id' => $request->id,
                'request_code' => $request->request_code,
                'type' => $request->type,
                'pdf_path' => $request->pdf_path,
                'form_data' => $request->form_data,
            ];
            
            // Check PDF files
            if ($request->pdf_path) {
                $pdfData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                $debugInfo['pdf_files'] = [];
                
                if (isset($pdfData['pdfs'])) {
                    foreach ($pdfData['pdfs'] as $key => $pdf) {
                        $fullPath = storage_path('app/public/' . $pdf['path']);
                        $debugInfo['pdf_files'][$key] = [
                            'path' => $pdf['path'],
                            'full_path' => $fullPath,
                            'exists' => file_exists($fullPath),
                            'size' => file_exists($fullPath) ? filesize($fullPath) : 'N/A'
                        ];
                    }
                }
            }
            
            // Check DOCX files
            if ($request->pdf_path) {
                $docxData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                $debugInfo['docx_files'] = [];
                
                if (isset($docxData['docxs'])) {
                    foreach ($docxData['docxs'] as $key => $docx) {
                        $fullPath = storage_path('app/' . $docx);
                        $debugInfo['docx_files'][$key] = [
                            'path' => $docx,
                            'full_path' => $fullPath,
                            'exists' => file_exists($fullPath),
                            'size' => file_exists($fullPath) ? filesize($fullPath) : 'N/A'
                        ];
                    }
                }
            }
            
            return response()->json($debugInfo);
            
        } catch (\Exception $e) {
            Log::error('Error debugging file paths: ' . $e->getMessage());
            return response()->json(['error' => 'Error debugging file paths'], 500);
        }
    }

    public function adminDownloadZip(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $zipPath = storage_path('app/temp/' . $request->request_code . '_files.zip');
            $zipDir = dirname($zipPath);
            
            if (!file_exists($zipDir)) {
                mkdir($zipDir, 0777, true);
            }
            
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Could not create ZIP file');
            }
            
            // Add PDF files
            if ($request->pdf_path) {
                $pdfData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                
                if (isset($pdfData['pdfs'])) {
                    foreach ($pdfData['pdfs'] as $key => $pdf) {
                        $fullPath = storage_path('app/public/' . $pdf['path']);
                        if (file_exists($fullPath)) {
                            $zip->addFile($fullPath, 'PDFs/' . ucfirst(str_replace('_', ' ', $key)) . '.pdf');
                        }
                    }
                }
            }
            
            // Add DOCX files
            if ($request->pdf_path) {
                $docxData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                
                if (isset($docxData['docxs'])) {
                    foreach ($docxData['docxs'] as $key => $docx) {
                        $fullPath = storage_path('app/' . $docx);
                        if (file_exists($fullPath)) {
                            $zip->addFile($fullPath, 'DOCXs/' . ucfirst(str_replace('_', ' ', $key)) . '.docx');
                        }
                    }
                }
            }
            
            $zip->close();
            
            return response()->download($zipPath, $request->request_code . '_files.zip')->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            Log::error('Error creating ZIP file: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating ZIP file'], 500);
        }
    }

    private function mapIncentiveFields($data) {
        return [
            'college' => $data['college'] ?? '',
            'name' => $data['name'] ?? '',
            'rank' => $data['rank'] ?? '',
            'bibentry' => $data['bibentry'] ?? '',
            'issn' => $data['issn'] ?? '',
            'doi' => $data['doi'] ?? '',
            'scopus' => isset($data['scopus']) ? '☑' : '☐',
            'wos' => isset($data['wos']) ? '☑' : '☐',
            'aci' => isset($data['aci']) ? '☑' : '☐',
            // Placeholders kept for template compatibility (set blank)
            'title' => '',
            'journal' => '',
            'publisher' => '',
            'citescore' => '',
            // Citation detail fields from form data
            'citedbibentry' => $data['citedbibentry'] ?? '',
            'facultyname' => $data['faculty_name'] ?? '',
            'centermanager' => $data['center_manager'] ?? '',
            'dean' => $data['dean_name'] ?? '',
            'date' => $data['date'] ?? now()->format('Y-m-d'),
        ];
    }

    private function mapRecommendationFields($data) {
        return [
            'collegeheader' => $data['rec_collegeheader'] ?? '',
            'facultyname' => $data['rec_faculty_name'] ?? '',
            'details' => $data['rec_citing_details'] ?? '',
            'indexing' => $data['rec_indexing_details'] ?? '',
            'dean' => $data['rec_dean_name'] ?? '',
            'date' => $data['date'] ?? now()->format('Y-m-d'),
        ];
    }
    
    /**
     * Preload templates for faster generation
     */
    public function preloadTemplates()
    {
        try {
            $preloader = new TemplatePreloader();
            $preloader->preloadAllTemplates();
            
            return response()->json([
                'success' => true,
                'message' => 'Templates preloaded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Template preload failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Template preload failed'
            ], 500);
        }
    }

    /**
     * Notify all signatories about new requests
     */
    private function notifySignatories(\App\Models\Request $request)
    {
        try {
            // Extract signatories from form data using the same logic as admin review modal
            $signatories = $this->extractSignatories($request->form_data);
            
            // Always include Deputy Director and RDD Director
            $deputyDirectorEmail = \App\Models\Setting::get('deputy_director_email');
            $rddDirectorEmail = \App\Models\Setting::get('rdd_director_email');
            
            $emailsSent = [];
            
            // Send emails to detected signatories
            foreach ($signatories as $signatory) {
                $user = \App\Models\User::where('name', $signatory['name'])->first();
                if ($user && $user->email) {
                    Mail::to($user->email)->queue(new \App\Mail\SignatoryNotification($request, $signatory['role'], $signatory['name']));
                    $emailsSent[] = $user->email;
                }
            }
            
            // Send emails to Deputy Director and RDD Director
            if ($deputyDirectorEmail) {
                $deputyDirectorName = \App\Models\Setting::get('official_deputy_director_name', 'Deputy Director');
                Mail::to($deputyDirectorEmail)->queue(new \App\Mail\SignatoryNotification($request, 'deputy_director', $deputyDirectorName));
                $emailsSent[] = $deputyDirectorEmail;
            }
            
            if ($rddDirectorEmail) {
                $rddDirectorName = \App\Models\Setting::get('official_rdd_director_name', 'RDD Director');
                Mail::to($rddDirectorEmail)->queue(new \App\Mail\SignatoryNotification($request, 'rdd_director', $rddDirectorName));
                $emailsSent[] = $rddDirectorEmail;
            }

            Log::info('Signatory notifications queued', [
                'requestId' => $request->id,
                'signatories' => $signatories,
                'emailsSent' => $emailsSent
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue signatory notifications', [
                'requestId' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Notify only the first signatory (Research Manager) in the workflow
     */
    private function notifyFirstSignatory(\App\Models\Request $request)
    {
        try {
            // Extract signatories from form data
            $signatories = $this->extractSignatories($request->form_data);
            
            // Find the Research Manager (center_manager) from the signatories
            $researchManager = null;
            foreach ($signatories as $signatory) {
                if ($signatory['role'] === 'Research Center Manager') {
                    $researchManager = $signatory;
                    break;
                }
            }
            
            if ($researchManager) {
                $user = \App\Models\User::where('name', $researchManager['name'])->first();
                if ($user && $user->email) {
                    Mail::to($user->email)->queue(new \App\Mail\SignatoryNotification($request, 'center_manager', $researchManager['name']));
                    
                    Log::info('First signatory notification queued', [
                        'requestId' => $request->id,
                        'signatoryName' => $researchManager['name'],
                        'signatoryEmail' => $user->email,
                        'role' => $researchManager['role']
                    ]);
                }
            } else {
                Log::warning('No Research Manager found in signatories', [
                    'requestId' => $request->id,
                    'signatories' => $signatories
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to queue first signatory notification', [
                'requestId' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Extract signatories from form data (same logic as admin review modal)
     */
    private function extractSignatories($formData)
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
            'Faculty' => ['facultyname', 'faculty_name', 'rec_facultyname'],
            'Research Center Manager' => ['centermanager', 'center_manager', 'research_center_manager'],
            'College Dean' => ['collegedean', 'college_dean', 'dean', 'dean_name', 'rec_dean_name'],
        ];

        $signatories = [];
        $seenValues = [];

        foreach ($roleToFields as $role => $candidates) {
            $value = null;
            foreach ($candidates as $field) {
                if (isset($normalized[$field]) && trim((string)$normalized[$field]) !== '') {
                    $value = trim((string)$normalized[$field]);
                    break;
                }
            }
            if ($value !== null && !isset($seenValues[mb_strtolower($value)])) {
                $signatories[] = [
                    'role' => $role,
                    'field' => $role,
                    'name'  => $value,
                ];
                $seenValues[mb_strtolower($value)] = true;
            }
        }

        return $signatories;
    }
} 