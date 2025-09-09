<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Request as UserRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Mail\SubmissionNotification;
use App\Mail\StatusChangeNotification;

class PublicationsController extends Controller
{
    public function create()
    {
        // Check for existing draft
        $existingDraft = \App\Models\Request::where('user_id', Auth::id())
            ->where('type', 'Publication')
            ->where('status', 'draft')
            ->orderBy('id', 'desc')
            ->first();
            
        \Log::info('Publications create - checking for draft', [
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
        
        return view('publications.request-clean', compact('request'));
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
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'request_id' => $request->id,
                    'action' => 'status_changed',
                    'details' => [
                        'old_status' => $oldStatus,
                        'new_status' => $request->status,
                        'request_code' => $request->request_code,
                        'type' => $request->type,
                        'changed_at' => now()->toDateTimeString(),
                    ],
                    'created_at' => now(),
                ]);
                
                Log::info('Activity log created successfully for status change', [
                    'request_id' => $request->id,
                    'request_code' => $request->request_code,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create activity log for status change: ' . $e->getMessage(), [
                    'request_id' => $request->id,
                    'request_code' => $request->request_code,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'error' => $e->getMessage()
                ]);
            }
            $adminComment = $httpRequest->input('admin_comment', null);
            Mail::to($request->user->email)->send(new \App\Mail\StatusChangeNotification($request, $request->user, $request->status, $adminComment));
        }

        return back()->with('success', 'Request status updated successfully.');
    }

    public function generateIncentiveDocx(Request $request)
    {
        try {
            $reqId = $request->input('request_id');
            $docxType = $request->input('docx_type', 'incentive');
            $data = $request->all();
            $isPreview = !$reqId;
            if ($isPreview) {
                $userId = Auth::id();
                $tempCode = 'preview_' . time() . '_' . Str::random(8);
                $uploadPath = "temp/{$userId}/{$tempCode}";
                Log::info('Generating DOCX in preview mode', ['userId' => $userId, 'tempCode' => $tempCode]);
            } else {
                $userRequest = \App\Models\Request::find($reqId);
                if (!$userRequest) {
                    throw new \Exception('Request not found for DOCX generation');
                }
                $reqCode = $userRequest->request_code;
                $userId = $userRequest->user_id;
                $uploadPath = "requests/{$userId}/{$reqCode}";
                Log::info('Generating DOCX for saved request', ['request_id' => $reqId, 'request_code' => $reqCode]);
            }
            $filename = null;
            $fullPath = null;
            switch ($docxType) {
                case 'incentive':
                    $filtered = $this->mapIncentiveFields($data);
                    Log::info('Filtered data for incentive', ['filtered' => $filtered]);
                    $fullPath = $this->generateIncentiveDocxFromHtml($filtered, $uploadPath);
                    $filename = 'Incentive_Application_Form.docx';
                    break;
                case 'recommendation':
                    $filtered = $this->mapRecommendationFields($data);
                    Log::info('Filtered data for recommendation', ['filtered' => $filtered]);
                    $fullPath = $this->generateRecommendationDocxFromHtml($filtered, $uploadPath);
                    $filename = 'Recommendation_Letter_Form.docx';
                    break;
                case 'terminal':
                    $filtered = $this->mapTerminalFields($data);
                    Log::info('Filtered data for terminal', ['filtered' => $filtered]);
                    $fullPath = $this->generateTerminalDocxFromHtml($filtered, $uploadPath);
                    $filename = 'Terminal_Report_Form.docx';
                    break;
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            if (!file_exists($fullPath)) {
                throw new \Exception('Generated file not found');
            }
            $userAgent = request()->header('User-Agent');
            $isIOS = preg_match('/iPhone|iPad|iPod/i', $userAgent);
            $contentDisposition = $isIOS ? 'inline' : 'attachment';
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => $contentDisposition . '; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating DOCX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateIncentiveDocxFromHtml($data, $uploadPath)
    {
        try {
            Log::info('Starting generateIncentiveDocxFromHtml', ['uploadPath' => $uploadPath]);
            
            $privateUploadPath = $uploadPath;
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            
            $templatePath = storage_path('app/templates/Incentive_Application_Form.docx');
            $outputPath = $privateUploadPath . '/Incentive_Application_Form.docx';
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            $templateProcessor = new TemplateProcessor($templatePath);
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
            
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Error in generateIncentiveDocxFromHtml: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    private function generateRecommendationDocxFromHtml($data, $uploadPath)
    {
        try {
            $privateUploadPath = $uploadPath;
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            
            $templatePath = storage_path('app/templates/Recommendation_Letter_Form.docx');
            $outputPath = $privateUploadPath . '/Recommendation_Letter_Form.docx';
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            $templateProcessor = new TemplateProcessor($templatePath);
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
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Error in generateRecommendationDocxFromHtml: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateTerminalDocxFromHtml($data, $uploadPath)
    {
        try {
            $privateUploadPath = $uploadPath;
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            
            $templatePath = storage_path('app/templates/Terminal_Report_Form.docx');
            $outputPath = $privateUploadPath . '/Terminal_Report_Form.docx';
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            $templateProcessor = new TemplateProcessor($templatePath);
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
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Error in generateTerminalDocxFromHtml: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateDocxFromTemplate($templateName, $data, $typeChecked = [], $indexedChecked = [], $outputPath)
    {
        try {
            $templatePath = Storage::disk('public')->path('templates/' . $templateName);
            
            if (!file_exists($templatePath)) {
                throw new \Exception("Template file not found: {$templatePath}");
            }
            
            Log::info('Loading template', ['templatePath' => $templatePath]);
            
            $phpWord = IOFactory::load($templatePath);
            
            $sections = $phpWord->getSections();
            
            foreach ($sections as $section) {
                $elements = $section->getElements();
                
                foreach ($elements as $element) {
                    $this->replacePlaceholdersInElement($element, $data, $typeChecked, $indexedChecked);
                }
            }
            
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($outputPath);
            
            Log::info('Template processing completed', ['outputPath' => $outputPath]);
            
        } catch (\Exception $e) {
            Log::error('Error in generateDocxFromTemplate: ' . $e->getMessage(), [
                'template' => $templateName,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    private function replacePlaceholdersInElement($element, $data, $typeChecked, $indexedChecked)
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $this->replacePlaceholdersInText($element, $data, $typeChecked, $indexedChecked);
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $textElement) {
                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                    $this->replacePlaceholdersInText($textElement, $data, $typeChecked, $indexedChecked);
                }
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $this->replacePlaceholdersInElement($cellElement, $data, $typeChecked, $indexedChecked);
                    }
                }
            }
        }
    }

    private function replacePlaceholdersInText($textElement, $data, $typeChecked, $indexedChecked)
    {
        $text = $textElement->getText();
        
        $replacements = [
            '{{collegeheader}}' => $data['collegeheader'] ?? '',
            '{{name}}' => $data['name'] ?? '',
            '{{academicrank}}' => $data['rank'] ?? '',
            '{{employmentstatus}}' => $data['employmentstatus'] ?? '',
            '{{college}}' => $data['college'] ?? '',
            '{{campus}}' => $data['campus'] ?? '',
            '{{field}}' => $data['field'] ?? '',
            '{{years}}' => $data['years'] ?? '',
            '{{papertitle}}' => $data['papertitle'] ?? '',
            '{{coauthors}}' => $data['coauthors'] ?? '',
            '{{journaltitle}}' => $data['journaltitle'] ?? '',
            '{{version}}' => $data['version'] ?? '',
            '{{pissn}}' => $data['pissn'] ?? '',
            '{{eissn}}' => $data['eissn'] ?? '',
            '{{doi}}' => $data['doi'] ?? '',
            '{{publisher}}' => $data['publisher'] ?? '',
            '{{citescore}}' => $data['citescore'] ?? '',
            '{{particulars}}' => $data['particulars'] ?? '',
            '{{facultyname}}' => $data['faculty_name'] ?? '',
            '{{centermanager}}' => $data['center_manager'] ?? '',
            '{{collegedean}}' => $data['dean_name'] ?? '',
            
            '{{rec_collegeheader}}' => $data['rec_collegeheader'] ?? '',
            '{{rec_date}}' => $data['rec_date'] ?? now()->format('Y-m-d'),
            '{{rec_facultyname}}' => $data['rec_faculty_name'] ?? '',
            '{{details}}' => $data['rec_publication_details'] ?? '',
            '{{indexing}}' => $data['rec_indexing_details'] ?? '',
            '{{dean}}' => $data['rec_dean_name'] ?? '',
            
            '{{title}}' => $data['title'] ?? '',
            '{{author}}' => $data['author'] ?? '',
            '{{duration}}' => $data['duration'] ?? '',
            '{{abstract}}' => $data['abstract'] ?? '',
            '{{introduction}}' => $data['introduction'] ?? '',
            '{{methodology}}' => $data['methodology'] ?? '',
            '{{rnd}}' => $data['rnd'] ?? '',
            '{{car}}' => $data['car'] ?? '',
            '{{references}}' => $data['references'] ?? '',
            '{{appendices}}' => $data['appendices'] ?? '',
            
            '{{regional}}' => $typeChecked['regional'] ?? '☐',
            '{{national}}' => $typeChecked['national'] ?? '☐',
            '{{international}}' => $typeChecked['international'] ?? '☐',
            '{{scopus}}' => $indexedChecked['scopus'] ?? '☐',
            '{{wos}}' => $indexedChecked['wos'] ?? '☐',
            '{{aci}}' => $indexedChecked['aci'] ?? '☐',
            '{{pubmed}}' => $indexedChecked['pubmed'] ?? '☐',
        ];
        
        $newText = str_replace(array_keys($replacements), array_values($replacements), $text);
        
        if ($newText !== $text) {
            $textElement->setText($newText);
            Log::info('Replaced placeholders in text', ['original' => $text, 'new' => $newText]);
        }
    }

    private function convertHtmlToDocx($html, $outputPath)
    {
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized.');
        }
        $request = \App\Models\Request::find($id);
        if (!$request) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Request not found.'], 404);
            }
            return redirect()->back()->with('error', 'Request not found.');
        }
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
            $fullDir = Storage::disk('public')->path($dir);
            if (is_dir($fullDir)) {
                $files = glob($fullDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($fullDir);
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
        
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'request_id' => $request->id,
            'action' => 'deleted',
            'details' => $requestDetails,
            'created_at' => now(),
        ]);
        
        $request->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Request and files deleted successfully.']);
        }
        return redirect()->back()->with('success', 'Request and files deleted successfully.');
    }

    public function submitPublicationRequest(Request $request)
    {
        Log::info('Publication request submission started', [
            'user_id' => Auth::id(),
            'has_files' => $request->hasFile('recommendation_letter'),
            'is_draft' => $request->has('save_draft')
        ]);

        // Check if this is a draft save
        $isDraft = $request->has('save_draft');
        
        // Prevent duplicate submissions (only for final submissions, not drafts)
        if (!$isDraft) {
            $recentSubmission = \App\Models\Request::where('user_id', Auth::id())
                ->where('type', 'Publication')
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subMinutes(5)) // Within last 5 minutes
                ->first();
                
            if ($recentSubmission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before submitting another request. You can only submit one request every 5 minutes.'
                ], 429);
            }
        }
        
        // Different validation rules for draft vs final submission
        if ($isDraft) {
            // For drafts, make most fields optional
            $validationRules = [
                'name' => 'nullable|string',
                'rank' => 'nullable|string',
                'college' => 'nullable|string',
                'bibentry' => 'nullable|string',
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
                'rec_publication_details' => 'nullable|string',
                'rec_indexing_details' => 'nullable|string',
                'rec_dean_name' => 'nullable|string',
                'title' => 'nullable|string',
                'author' => 'nullable|string',
                'duration' => 'nullable|string',
                'abstract' => 'nullable|string',
                'introduction' => 'nullable|string',
                'methodology' => 'nullable|string',
                'rnd' => 'nullable|string',
                'car' => 'nullable|string',
                'references' => 'nullable|string',
                'appendices' => 'nullable|string',
            ];
        } else {
            // For final submission, require all fields
            $validationRules = [
                'name' => 'required|string',
                'rank' => 'required|string',
                'college' => 'required|string',
                'bibentry' => 'required|string',
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
                'rec_publication_details' => 'required|string',
                'rec_indexing_details' => 'required|string',
                'rec_dean_name' => 'required|string',
                'title' => 'required|string',
                'author' => 'required|string',
                'duration' => 'required|string',
                'abstract' => 'required|string',
                'introduction' => 'required|string',
                'methodology' => 'required|string',
                'rnd' => 'required|string',
                'car' => 'required|string',
                'references' => 'required|string',
                'appendices' => 'nullable|string',
            ];
        }
        
        // Only require files for final submission, not for draft
        if (!$isDraft) {
            $validationRules = array_merge($validationRules, [
                'recommendation_letter' => 'required|file|mimes:pdf|max:20480',
                'published_article' => 'required|file|mimes:pdf|max:20480',
                'peer_review' => 'required|file|mimes:pdf|max:20480',
                'terminal_report' => 'required|file|mimes:pdf|max:20480',
            ]);
        } else {
            // For drafts, make files optional
            $validationRules = array_merge($validationRules, [
                'recommendation_letter' => 'nullable|file|mimes:pdf|max:20480',
                'published_article' => 'nullable|file|mimes:pdf|max:20480',
                'peer_review' => 'nullable|file|mimes:pdf|max:20480',
                'terminal_report' => 'nullable|file|mimes:pdf|max:20480',
            ]);
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            Log::info('Publication request validation failed', [
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

        $data = $validator->validated();
        
        try {
            $userId = Auth::id();
            $requestCode = 'PUB-' . now()->format('Ymd-His');
            $uploadPath = "requests/{$userId}/{$requestCode}";
            
            Log::info('Processing file uploads', [
                'requestCode' => $requestCode,
                'uploadPath' => $uploadPath
            ]);
            
            $attachments = [];
            foreach ([
                'recommendation_letter',
                'published_article',
                'peer_review',
                'terminal_report'
            ] as $field) {
                $file = $request->file($field);
                
                Log::info('Processing file upload', [
                    'field' => $field,
                    'file_exists' => $file ? 'YES' : 'NO',
                    'original_name' => $file ? $file->getClientOriginalName() : 'N/A',
                    'file_size' => $file ? $file->getSize() : 'N/A',
                    'mime_type' => $file ? $file->getMimeType() : 'N/A'
                ]);
                
                if ($file) {
                    $storedPath = $file->storeAs($uploadPath, $file->getClientOriginalName(), 'local');
                    $cleanPath = $storedPath;
                    $attachments[$field] = [
                        'path' => $cleanPath,
                        'original_name' => $file->getClientOriginalName(),
                    ];
                } elseif (!$isDraft) {
                    // Only require files for final submission
                    Log::error('File upload failed - file is null', ['field' => $field]);
                    throw new \Exception("File upload failed for field: {$field}");
                }
            }

            $docxPaths = [];
            $docxPaths['incentive'] = $this->generateIncentiveDocxFromHtml($data, $uploadPath);
            $docxPaths['recommendation'] = $this->generateRecommendationDocxFromHtml($data, $uploadPath);
            $docxPaths['terminal'] = $this->generateTerminalDocxFromHtml($data, $uploadPath);

            Log::info('Creating database entry', [
                'requestCode' => $requestCode,
                'userId' => $userId
            ]);

            // Check if this is an update to existing draft
            $existingDraft = \App\Models\Request::where('user_id', $userId)
                ->where('type', 'Publication')
                ->where('status', 'draft')
                ->orderBy('id', 'desc')
                ->first();
                
            if ($existingDraft && $isDraft) {
                // Update existing draft
                $existingDraft->update([
                    'form_data' => $data,
                    'pdf_path' => json_encode([
                        'pdfs' => $attachments,
                        'docxs' => [],
                    ]),
                ]);
                $userRequest = $existingDraft;
            } else {
                // Create new request
                $userRequest = UserRequest::create([
                    'user_id' => $userId,
                    'request_code' => $requestCode,
                    'type' => 'Publication',
                    'status' => $isDraft ? 'draft' : 'pending',
                    'requested_at' => now(), // Always set requested_at, even for drafts
                    'form_data' => $data,
                    'pdf_path' => json_encode([
                        'pdfs' => $attachments,
                        'docxs' => $isDraft ? [] : $docxPaths,
                    ]),
                ]);
            }

            try {
                \App\Models\ActivityLog::create([
                    'user_id' => $userId,
                    'request_id' => $userRequest->id,
                    'action' => 'created',
                    'details' => [
                        'request_code' => $userRequest->request_code,
                        'type' => $userRequest->type,
                        'created_at' => now()->toDateTimeString(),
                    ],
                    'created_at' => now(),
                ]);
                
                Log::info('Activity log created successfully for publication request', [
                    'request_id' => $userRequest->id,
                    'request_code' => $userRequest->request_code,
                    'user_id' => $userId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create activity log for publication request: ' . $e->getMessage(), [
                    'request_id' => $userRequest->id,
                    'request_code' => $userRequest->request_code,
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Publication request submitted successfully', [
                'requestId' => $userRequest->id,
                'requestCode' => $requestCode
            ]);

            // Only send emails for final submission, not for drafts
            if (!$isDraft) {
                try {
                    Mail::to($userRequest->user->email)->send(new SubmissionNotification($userRequest, $userRequest->user, false));
                    
                    $adminUsers = \App\Models\User::where('role', 'admin')->get();
                    foreach ($adminUsers as $adminUser) {
                        Mail::to($adminUser->email)->send(new SubmissionNotification($userRequest, $userRequest->user, true));
                    }
                    
                    Log::info('Email notifications sent successfully', [
                        'requestId' => $userRequest->id,
                        'userEmail' => $userRequest->user->email,
                        'adminEmails' => $adminUsers->pluck('email')->toArray()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending email notifications: ' . $e->getMessage());
                }
            }

            if ($isDraft) {
                Log::info('Draft saved successfully', [
                    'request_id' => $userRequest->id,
                    'request_code' => $userRequest->request_code,
                    'form_data' => $userRequest->form_data
                ]);
                return response()->json(['success' => true, 'message' => 'Draft saved successfully!']);
            } else {
                // Clean up any existing drafts for this user after successful final submission
                try {
                    $deletedDrafts = \App\Models\Request::where('user_id', $userId)
                        ->where('type', 'Publication')
                        ->where('status', 'draft')
                        ->delete();
                    
                    if ($deletedDrafts > 0) {
                        Log::info('Cleaned up draft entries after successful submission', [
                            'user_id' => $userId,
                            'deleted_drafts_count' => $deletedDrafts,
                            'final_request_id' => $userRequest->id
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to clean up draft entries: ' . $e->getMessage(), [
                        'user_id' => $userId,
                        'final_request_id' => $userRequest->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the submission if draft cleanup fails
                }
                return redirect()->route('publications.request')->with('success', 'Publication request submitted successfully! Request Code: ' . $requestCode);
            }

        } catch (\Exception $e) {
            Log::error('Error submitting publication request: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error submitting request. Please try again.');
        }
    }

    public function adminDownloadFile(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Unauthorized');
            }

            $fileType = $httpRequest->query('type');
            $fileKey = $httpRequest->query('key');

            if (!$fileType || !$fileKey) {
                abort(400, 'Missing file type or key');
            }

            $pdfPath = $request->pdf_path;
            if (!$pdfPath) {
                abort(404, 'No file data found for this request');
            }

            $paths = json_decode($pdfPath, true);
            if (!$paths) {
                abort(404, 'Invalid file data format');
            }

            $storagePath = null;
            $originalName = null;
            $ext = null;

            if ($fileType === 'pdf') {
                if (!isset($paths['pdfs']) || !isset($paths['pdfs'][$fileKey])) {
                    abort(404, 'PDF file not found: ' . $fileKey);
                }

                $fileInfo = $paths['pdfs'][$fileKey];
                $storagePath = $fileInfo['path'] ?? null;
                $originalName = $fileInfo['original_name'] ?? $fileKey . '.pdf';
                $ext = 'pdf';

            } elseif ($fileType === 'docx') {
                if (!isset($paths['docxs']) || !isset($paths['docxs'][$fileKey])) {
                    abort(404, 'DOCX file not found: ' . $fileKey);
                }

                $storagePath = $paths['docxs'][$fileKey];
                $userName = $request->user->name ?? 'User';
                $sanitizedUserName = preg_replace('/[^a-zA-Z0-9\s]/', '', $userName);
                $sanitizedUserName = preg_replace('/\s+/', '_', $sanitizedUserName);
                $date = $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('Y-m-d') : date('Y-m-d');
                $typeLabel = '';
                if ($fileKey === 'incentive') {
                    $typeLabel = 'Publication_Incentive_Application';
                } elseif ($fileKey === 'recommendation') {
                    $typeLabel = 'Publication_Recommendation_Letter';
                } elseif ($fileKey === 'terminal') {
                    $typeLabel = 'Publication_Terminal_Report';
                } else {
                    $typeLabel = ucfirst($fileKey);
                }
                $originalName = $sanitizedUserName . '_' . $typeLabel . '_' . $date . '.docx';
                $ext = 'docx';
                $downloadName = $originalName;

            } else {
                abort(400, 'Invalid file type: ' . $fileType);
            }

            if (!$storagePath) {
                abort(404, 'File path not found');
            }

            $fullPath = Storage::disk('local')->path($storagePath);
            if (!file_exists($fullPath)) {
                $fullPath = Storage::disk('public')->path($storagePath);
            }

            Log::info('Admin download attempt', [
                'request_id' => $request->id,
                'file_type' => $fileType,
                'file_key' => $fileKey,
                'storage_path' => $storagePath,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath),
                'file_size' => file_exists($fullPath) ? filesize($fullPath) : 'N/A'
            ]);

            if (!file_exists($fullPath)) {
                abort(404, 'File not found on disk: ' . $storagePath);
            }

            $userName = $request->user->name ?? 'user';
            $userNameSlug = Str::slug($userName, '_');
            $downloadName = $userNameSlug . '_' . $originalName;

            $mime = $ext === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            $userAgent = request()->header('User-Agent');
            $isIOS = preg_match('/iPhone|iPad|iPod/i', $userAgent);
            $contentDisposition = $isIOS ? 'inline' : 'attachment';
            return response()->download($fullPath, $downloadName, [
                'Content-Type' => $mime,
                'Content-Disposition' => $contentDisposition . '; filename="' . $downloadName . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin download error', [
                'request_id' => $request->id ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Download failed: ' . $e->getMessage());
        }
    }

    public function serveFile(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Unauthorized');
            }

            $fileType = $httpRequest->query('type');
            $fileKey = $httpRequest->query('key');

            if (!$fileType || !$fileKey) {
                abort(400, 'Missing parameters');
            }

            $pdfPath = $request->pdf_path;
            if (!$pdfPath) {
                abort(404, 'No file data');
            }

            $paths = json_decode($pdfPath, true);
            if (!$paths) {
                abort(404, 'Invalid data format');
            }

            $filePath = null;
            $fileName = null;

            if ($fileType === 'pdf' && isset($paths['pdfs'][$fileKey])) {
                $fileInfo = $paths['pdfs'][$fileKey];
                $filePath = $fileInfo['path'];
                $fileName = $fileInfo['original_name'] ?? $fileKey . '.pdf';
            } elseif ($fileType === 'docx' && isset($paths['docxs'][$fileKey])) {
                $filePath = $paths['docxs'][$fileKey];
                $fileName = ucfirst($fileKey) . '_Form.docx';
            } else {
                abort(404, 'File not found');
            }

            if ($fileType === 'pdf') {
            if (!Storage::disk('public')->exists($filePath)) {
                    abort(404, 'PDF file not found on disk');
                }
                $fullPath = Storage::disk('public')->path($filePath);
            } else {
                if (!file_exists(Storage::disk('public')->path($filePath))) {
                    abort(404, 'DOCX file not found on disk');
                }
                $fullPath = Storage::disk('public')->path($filePath);
            }

            $userName = $request->user->name ?? 'user';
            $userNameSlug = Str::slug($userName, '_');
            $downloadName = $userNameSlug . '_' . $fileName;

            return response()->download($fullPath, $downloadName);

        } catch (\Exception $e) {
            Log::error('File serving error', [
                'error' => $e->getMessage(),
                'request_id' => $request->id ?? 'unknown'
            ]);
            abort(500, 'File serving failed');
        }
    }

    public function debugFilePaths(Request $httpRequest, \App\Models\Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $pdfPath = $request->pdf_path;
        $paths = json_decode($pdfPath, true);

        $debug = [
            'request_id' => $request->id,
            'pdf_path_raw' => $pdfPath,
            'pdf_path_decoded' => $paths,
            'storage_base' => Storage::disk('public')->path(''),
        ];

        if ($paths) {
            $debug['pdfs'] = [];
            if (isset($paths['pdfs'])) {
                foreach ($paths['pdfs'] as $key => $fileInfo) {
                    $fullPath = Storage::disk('public')->path($fileInfo['path']);
                    $debug['pdfs'][$key] = [
                        'path' => $fileInfo['path'],
                        'full_path' => $fullPath,
                        'exists' => file_exists($fullPath),
                        'size' => file_exists($fullPath) ? filesize($fullPath) : 'N/A',
                        'original_name' => $fileInfo['original_name'] ?? 'N/A'
                    ];
                }
            }

            $debug['docxs'] = [];
            if (isset($paths['docxs'])) {
                foreach ($paths['docxs'] as $key => $storagePath) {
                    $fullPath = Storage::disk('public')->path($storagePath);
                    $debug['docxs'][$key] = [
                        'path' => $storagePath,
                        'full_path' => $fullPath,
                        'exists' => file_exists($fullPath),
                        'size' => file_exists($fullPath) ? filesize($fullPath) : 'N/A'
                    ];
                }
            }
        }

        return response()->json($debug);
    }

    public function adminDownloadZip(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Unauthorized');
            }

            $pdfPath = $request->pdf_path;
            $paths = json_decode($pdfPath, true);
            
            Log::info('ZIP download attempt', [
                'request_id' => $request->id,
                'pdf_path_raw' => $pdfPath,
                'paths_decoded' => $paths,
                'pdfs_count' => isset($paths['pdfs']) ? count($paths['pdfs']) : 0,
                'docxs_count' => isset($paths['docxs']) ? count($paths['docxs']) : 0
            ]);
            
            if (!$paths) {
                abort(404, 'No files found for this request');
            }

            $userName = $request->user->name ?? 'user';
            $userNameSlug = Str::slug($userName, '_');
            $zipName = $userNameSlug . '_request_files.zip';
            
            $zipPath = Storage::disk('public')->path('temp/' . $zipName);
            $zipDir = dirname($zipPath);
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0777, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                abort(500, 'Could not create ZIP file');
            }

            $addedFiles = [];
            $missingFiles = [];

            if (isset($paths['pdfs'])) {
                foreach ($paths['pdfs'] as $key => $fileInfo) {
                    $filePath = Storage::disk('public')->path($fileInfo['path']);
                    $originalName = $fileInfo['original_name'] ?? $key . '.pdf';
                    
                    Log::info('Processing PDF file', [
                        'key' => $key,
                        'file_path' => $filePath,
                        'exists' => file_exists($filePath),
                        'original_name' => $originalName
                    ]);
                    
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, 'PDFs/' . $originalName);
                        $addedFiles[] = 'PDFs/' . $originalName;
                    } else {
                        Log::warning('PDF file not found on disk', [
                            'file_path' => $filePath,
                            'key' => $key
                        ]);
                        $missingFiles[] = 'PDFs/' . $originalName;
                    }
                }
            }

            if (isset($paths['docxs'])) {
                foreach ($paths['docxs'] as $key => $storagePath) {
                    $filePath = Storage::disk('public')->path($storagePath);
                    $docxName = ucfirst($key) . '_Form.docx';
                    
                    Log::info('Processing DOCX file', [
                        'key' => $key,
                        'file_path' => $filePath,
                        'exists' => file_exists($filePath),
                        'docx_name' => $docxName
                    ]);
                    
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, 'DOCX/' . $docxName);
                        $addedFiles[] = 'DOCX/' . $docxName;
                    } else {
                        Log::warning('DOCX file not found on disk', [
                            'file_path' => $filePath,
                            'key' => $key
                        ]);
                        $missingFiles[] = 'DOCX/' . $docxName;
                    }
                }
            }

            $zip->close();

            Log::info('ZIP file created', [
                'zip_path' => $zipPath,
                'added_files' => $addedFiles,
                'missing_files' => $missingFiles,
                'total_files' => count($addedFiles)
            ]);

            if (empty($addedFiles)) {
                abort(404, 'No files found on disk for this request. Files may have been deleted or moved.');
            }

            return response()->download($zipPath, $zipName, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $zipName . '"'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('ZIP download error', [
                'request_id' => $request->id ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'ZIP download failed: ' . $e->getMessage());
        }
    }

    private function mapIncentiveFields($data) {
        return [
            'college' => $data['college'] ?? '',
            'name' => $data['name'] ?? '',
            'academicrank' => $data['rank'] ?? '',
            'papertitle' => $data['papertitle'] ?? '',
            'bibentry' => $data['bibentry'] ?? '',
            'journaltitle' => $data['journaltitle'] ?? '',
            'issn' => $data['issn'] ?? '',
            'doi' => $data['doi'] ?? '',
            'publisher' => $data['publisher'] ?? '',
            'scopus' => (isset($data['scopus']) && $data['scopus'] === '1') ? '☑' : '☐',
            'wos' => (isset($data['wos']) && $data['wos'] === '1') ? '☑' : '☐',
            'aci' => (isset($data['aci']) && $data['aci'] === '1') ? '☑' : '☐',
            'regional' => (isset($data['type']) && $data['type'] === 'Regional') ? '☑' : '☐',
            'national' => (isset($data['type']) && $data['type'] === 'National') ? '☑' : '☐',
            'international' => (isset($data['type']) && $data['type'] === 'International') ? '☑' : '☐',
            'citescore' => $data['citescore'] ?? '',
            'particulars' => $data['particulars'] ?? '',
            'faculty' => $data['faculty_name'] ?? '',
            'facultyname' => $data['faculty_name'] ?? '',
            'centermanager' => $data['center_manager'] ?? '',
            'dean' => $data['dean_name'] ?? '',
            'date' => $data['date'] ?? now()->format('Y-m-d'),
        ];
    }
    private function mapRecommendationFields($data) {
        $collegeheader = '';
        if (isset($data['rec_collegeheader'])) {
            if (is_array($data['rec_collegeheader'])) {
                $collegeheader = end($data['rec_collegeheader']);
            } else {
                $collegeheader = $data['rec_collegeheader'];
            }
        } else if (request()->has('rec_collegeheader')) {
            $collegeheader = request()->input('rec_collegeheader');
        } else if (isset($_POST['rec_collegeheader'])) {
            $collegeheader = $_POST['rec_collegeheader'];
        }
        Log::info('FORCED rec_collegeheader VALUE', ['value' => $collegeheader, 'raw' => $data['rec_collegeheader'] ?? null, 'request_input' => request()->input('rec_collegeheader'), 'post' => $_POST['rec_collegeheader'] ?? null]);
        return [
            'collegeheader' => $collegeheader,
            'facultyname' => $data['rec_faculty_name'] ?? '',
            'details' => $data['rec_publication_details'] ?? '',
            'indexing' => $data['rec_indexing_details'] ?? '',
            'dean' => $data['rec_dean_name'] ?? '',
            'date' => $data['date'] ?? now()->format('Y-m-d'),
        ];
    }
    private function mapTerminalFields($data) {
        return [
            'title' => $data['title'] ?? '',
            'author' => $data['author'] ?? '',
            'duration' => $data['duration'] ?? '',
            'abstract' => $data['abstract'] ?? '',
            'introduction' => $data['introduction'] ?? '',
            'methodology' => $data['methodology'] ?? '',
            'rnd' => $data['rnd'] ?? '',
            'car' => $data['car'] ?? '',
            'references' => $data['references'] ?? '',
            'appendices' => $data['appendices'] ?? '',
        ];
    }
    public function generateDocx(Request $request)
    {
        Log::info('RAW REQUEST DATA', $request->all());
        try {
            $reqId = $request->input('request_id');
            $docxType = $request->input('docx_type', 'incentive');
            $data = $request->all();
            $isPreview = !$reqId;
            if ($isPreview) {
                $userId = Auth::id();
                $tempCode = 'preview_' . time() . '_' . Str::random(8);
                $uploadPath = "temp/{$userId}/{$tempCode}";
                Log::info('Generating DOCX in preview mode', ['userId' => $userId, 'tempCode' => $tempCode]);
            } else {
                $userRequest = \App\Models\Request::find($reqId);
                if (!$userRequest) {
                    throw new \Exception('Request not found for DOCX generation');
                }
                $reqCode = $userRequest->request_code;
                $userId = $userRequest->user_id;
                $uploadPath = "requests/{$userId}/{$reqCode}";
                Log::info('Generating DOCX for saved request', ['request_id' => $reqId, 'request_code' => $reqCode]);
            }
            $filename = null;
            $fullPath = null;
            switch ($docxType) {
                case 'incentive':
                    $filtered = $this->mapIncentiveFields($data);
                    Log::info('Filtered data for incentive', ['filtered' => $filtered]);
                    $fullPath = $this->generateIncentiveDocxFromHtml($filtered, $uploadPath);
                    $filename = 'Incentive_Application_Form.docx';
                    break;
                case 'recommendation':
                    $filtered = $this->mapRecommendationFields($data);
                    Log::info('Filtered data for recommendation', ['filtered' => $filtered]);
                    $fullPath = $this->generateRecommendationDocxFromHtml($filtered, $uploadPath);
                    $filename = 'Recommendation_Letter_Form.docx';
                    break;
                case 'terminal':
                    $filtered = $this->mapTerminalFields($data);
                    Log::info('Filtered data for terminal', ['filtered' => $filtered]);
                    $fullPath = $this->generateTerminalDocxFromHtml($filtered, $uploadPath);
                    $filename = 'Terminal_Report_Form.docx';
                    break;
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            
            $absolutePath = Storage::disk('local')->path($fullPath);
            if (!file_exists($absolutePath)) {
                throw new \Exception('Generated file not found at: ' . $absolutePath);
            }
            
            $userAgent = request()->header('User-Agent');
            $isIOS = preg_match('/iPhone|iPad|iPod/i', $userAgent);
            $contentDisposition = $isIOS ? 'inline' : 'attachment';
            return response()->download($absolutePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => $contentDisposition . '; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating DOCX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage()
            ], 500);
        }
    }
} 