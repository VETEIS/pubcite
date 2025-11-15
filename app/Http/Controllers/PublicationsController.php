<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Request as UserRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
// use App\Services\TemplateCacheService; // Disabled due to serialization issues with TemplateProcessor
use App\Services\TemplatePreloader;
// use App\Http\Controllers\Traits\DraftSessionManager; // Temporarily disabled for production fix
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Mail\SubmissionNotification;
use App\Mail\StatusChangeNotification;
use App\Services\DocxToPdfConverter;
use App\Services\RecaptchaService;
use App\Traits\SanitizesFilePaths;
use App\Traits\EnsuresTemplateFiles;

class PublicationsController extends Controller
{
    use SanitizesFilePaths, EnsuresTemplateFiles;
    // use DraftSessionManager; // Temporarily disabled for production fix
    public function create()
    {
        $publications_request_enabled = \App\Models\Setting::get('publications_request_enabled', '1');
        
        if ($publications_request_enabled !== '1') {
            return redirect()->route('dashboard')->with('error', 'Publications requests are currently disabled by administrators.');
        }
        
        // Check for existing draft
        $existingDraft = \App\Models\Request::where('user_id', Auth::id())
            ->where('type', 'Publication')
            ->where('status', 'draft')
            ->orderBy('id', 'desc')
            ->first();
            
        Log::info('Publications create - checking for draft', [
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
            return view('publications.request', compact('request'))->with('success', 'Draft loaded successfully!');
        }
        
        return view('publications.request', compact('request'));
    }

    // REMOVED: adminUpdate method - Status changes are now automated through the 5-stage signature workflow
    // Admins can no longer manually set request status to maintain workflow integrity


    private function generateIncentiveDocxFromHtml($data, $uploadPath, $convertToPdf = false)
    {
        try {
            $privateUploadPath = $uploadPath;
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            
            $templatePath = $this->ensureTemplateAvailable('Incentive_Application_Form.docx');
            $outputPath = $privateUploadPath . '/Incentive_Application_Form.docx';
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            // Use direct TemplateProcessor (caching disabled due to serialization issues)
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            // Prepare all values at once for better performance
            $allValues = array_merge($data, [
                'facultysignature' => '${facultysignature}',
                'centermanagersignature' => '${centermanagersignature}',
                'deansignature' => '${deansignature}',
                'deputydirectorsignature' => '${deputydirectorsignature}',
                'directorsignature' => '${directorsignature}'
            ]);
            
            // Set all values in one pass
            foreach ($allValues as $key => $value) {
                try {
                    $templateProcessor->setValue($key, (string)($value ?? ''));
                } catch (\Exception $e) {
                    // Skip if placeholder doesn't exist in template (silent fail for better performance)
                }
            }
            
            $templateProcessor->saveAs($fullOutputPath);
            
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
            Log::error('Error in generateIncentiveDocxFromHtml: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    private function generateRecommendationDocxFromHtml($data, $uploadPath, $convertToPdf = false)
    {
        try {
            $privateUploadPath = $uploadPath;
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            
            $templatePath = $this->ensureTemplateAvailable('Recommendation_Letter_Form.docx');
            $outputPath = $privateUploadPath . '/Recommendation_Letter_Form.docx';
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            // Use direct TemplateProcessor (caching disabled due to serialization issues)
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            // Prepare all values at once for better performance
            $allValues = array_merge($data, [
                'facultysignature' => '${facultysignature}',
                'centermanagersignature' => '${centermanagersignature}',
                'deansignature' => '${deansignature}',
                'deputydirectorsignature' => '${deputydirectorsignature}',
                'directorsignature' => '${directorsignature}'
            ]);
            
            // Set all values in one pass
            foreach ($allValues as $key => $value) {
                try {
                    $templateProcessor->setValue($key, (string)($value ?? ''));
                } catch (\Exception $e) {
                    // Skip if placeholder doesn't exist in template (silent fail for better performance)
                }
            }
            
            $templateProcessor->saveAs($fullOutputPath);
            
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
            Log::error('Error in generateRecommendationDocxFromHtml: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateTerminalDocxFromHtml($data, $uploadPath, $convertToPdf = false)
    {
        try {
            $privateUploadPath = $uploadPath;
            $fullPath = Storage::disk('local')->path($privateUploadPath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            
            $templatePath = $this->ensureTemplateAvailable('Terminal_Report_Form.docx');
            $outputPath = $privateUploadPath . '/Terminal_Report_Form.docx';
            $fullOutputPath = Storage::disk('local')->path($outputPath);
            
            // Use direct TemplateProcessor (caching disabled due to serialization issues)
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            // Prepare all values at once for better performance
            $allValues = array_merge($data, [
                'facultysignature' => '${facultysignature}',
                'centermanagersignature' => '${centermanagersignature}',
                'deansignature' => '${deansignature}',
                'deputydirectorsignature' => '${deputydirectorsignature}',
                'directorsignature' => '${directorsignature}'
            ]);
            
            // Set all values in one pass
            foreach ($allValues as $key => $value) {
                try {
                    $templateProcessor->setValue($key, (string)($value ?? ''));
                } catch (\Exception $e) {
                    // Skip if placeholder doesn't exist in template (silent fail for better performance)
                }
            }
            
            $templateProcessor->saveAs($fullOutputPath);
            
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
            '{{name}}' => $data['name'] ?? $data['rec_faculty_name'] ?? '', // Template uses ${name}
            '{{rec_facultyname}}' => $data['name'] ?? $data['rec_faculty_name'] ?? '', // Keep for backward compatibility
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
        
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'request_id' => $request->id,
            'action' => 'deleted',
            'details' => $requestDetails,
            'created_at' => now(),
        ]);
        
        // Delete associated admin notifications for this request
        \App\Models\AdminNotification::where('request_id', $request->id)->delete();
        
        $request->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Request and files deleted successfully.']);
        }
        return redirect()->back()->with('success', 'Request and files deleted successfully.');
    }

    public function submitPublicationRequest(Request $request, RecaptchaService $recaptchaService)
    {
        $user = Auth::user();
        
        Log::info('Publication request submission started', [
            'user_id' => $user->id,
            'has_files' => $request->hasFile('published_article') || $request->hasFile('indexing_evidence'),
            'is_draft' => $request->has('save_draft')
        ]);

        // Check if this is a draft save
        $isDraft = $request->has('save_draft');
        
        // Verify reCAPTCHA for final submissions (not drafts) only if widget was rendered
        if (!$isDraft && $recaptchaService->shouldDisplay() && $request->has('recaptcha_widget_rendered')) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            if (empty($recaptchaToken)) {
                return back()->withErrors(['g-recaptcha-response' => 'Please complete the reCAPTCHA verification.'])->withInput();
            }
            if (!$recaptchaService->verify($recaptchaToken, $request->ip())) {
                return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
            }
        }
        
        // Prevent duplicate submissions for both drafts and final submissions
        $recentSubmission = \App\Models\Request::where('user_id', $user->id)
            ->where('type', 'Publication')
            ->where('status', $isDraft ? 'draft' : 'pending')
            ->where('requested_at', '>=', now()->subSeconds(10)) // Within last 10 seconds
            ->first();
            
        if ($recentSubmission) {
            Log::info('Duplicate submission prevented', [
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
                'faculty_name' => 'nullable|string',
                'center_manager' => 'nullable|string',
                'dean_name' => 'required|string',
                'rec_collegeheader' => 'required|string',
                'date' => 'required|string',
                'rec_faculty_name' => 'nullable|string',
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
                'published_article' => 'required|file|mimes:pdf|max:20480',
                'indexing_evidence' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            ]);
        } else {
            // For drafts, make files optional
            $validationRules = array_merge($validationRules, [
                'published_article' => 'nullable|file|mimes:pdf|max:20480',
                'indexing_evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
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
        
        // Auto-populate faculty_name from name field if not provided
        if (empty($data['faculty_name']) && !empty($data['name'])) {
            $data['faculty_name'] = $data['name'];
        }
        
        // Auto-populate rec_faculty_name from name field if not provided
        if (empty($data['rec_faculty_name']) && !empty($data['name'])) {
            $data['rec_faculty_name'] = $data['name'];
        }
        
        try {
            $userId = $user->id;
            
            // Check for existing draft first (with session optimization)
            $existingDraft = null;
            
            // First check session for draft ID (faster than database query)
            $draftId = session("draft_publication_{$userId}");
            if ($draftId) {
                $existingDraft = \App\Models\Request::where('id', $draftId)
                    ->where('user_id', $userId)
                    ->where('type', 'Publication')
                    ->where('status', 'draft')
                    ->first();
            }
            
            // Fallback to database query if session doesn't have draft ID
            if (!$existingDraft) {
                $existingDraft = \App\Models\Request::where('user_id', $userId)
                    ->where('type', 'Publication')
                    ->where('status', 'draft')
                    ->first();
                
                // Store draft ID in session for future requests
                if ($existingDraft) {
                    session(["draft_publication_{$userId}" => $existingDraft->id]);
                }
            }
            
            if ($existingDraft && $isDraft) {
                // Reuse existing draft directory
                $requestCode = $existingDraft->request_code;
                $uploadPath = "requests/{$userId}/{$requestCode}";
                Log::info('Reusing existing draft directory', [
                    'request_id' => $existingDraft->id,
                    'request_code' => $requestCode,
                    'upload_path' => $uploadPath
                ]);
            } else {
                // Generate new request code only for new requests
                $requestCode = 'PUB-' . now()->format('Ymd-His');
                $uploadPath = "requests/{$userId}/{$requestCode}";
                Log::info('Creating new request directory', [
                    'request_code' => $requestCode,
                    'upload_path' => $uploadPath,
                    'is_draft' => $isDraft
                ]);
            }
            
            // Define preview status (this method is for submissions, not previews)
            $isPreview = false;
            
            // Skip file processing for previews - only process for actual submissions
            if (!$isPreview) {
                Log::info('Processing file uploads', [
                    'requestCode' => $requestCode,
                    'uploadPath' => $uploadPath
                ]);
                
                $attachments = [];
            foreach ([
                'published_article',
                'indexing_evidence'
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
                    // Sanitize filename to prevent path injection
                    $sanitizedFilename = $this->sanitizePath(basename($file->getClientOriginalName()));
                    $sanitizedUploadPath = $this->sanitizePath($uploadPath);
                    
                    $storedPath = $file->storeAs($sanitizedUploadPath, $sanitizedFilename, 'local');
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

            // Check if we have pre-generated files to move instead of regenerating
            $docxPaths = [];
            $tempFiles = $request->input('generated_docx_files', []);
            
            // Debug: Log what we received
            Log::info('Checking for pre-generated DOCX files', [
                'has_tempFiles' => !empty($tempFiles),
                'tempFiles' => $tempFiles,
                'all_inputs' => array_keys($request->all())
            ]);
            
            if (!empty($tempFiles) && is_array($tempFiles)) {
                // Move pre-generated files instead of regenerating (much faster)
                Log::info('Moving pre-generated DOCX files', ['tempFiles' => $tempFiles]);
                foreach ($tempFiles as $type => $tempPath) {
                    if ($tempPath && Storage::disk('local')->exists($tempPath)) {
                        $filename = $type === 'incentive' ? 'Incentive_Application_Form.docx' :
                                   ($type === 'recommendation' ? 'Recommendation_Letter_Form.docx' : 'Terminal_Report_Form.docx');
                        $finalPath = $uploadPath . '/' . $filename;
                        
                        // Move file from temp to final location (atomic operation)
                        Storage::disk('local')->move($tempPath, $finalPath);
                        $docxPaths[$type] = [
                            'path' => $finalPath,
                            'original_name' => $filename
                        ];
                        
                        Log::info('Moved DOCX file', ['type' => $type, 'from' => $tempPath, 'to' => $finalPath]);
                    }
                }
            }
            
            // Generate any missing DOCX files (convert to PDF for submission)
            // Only generate what's missing to minimize processing time
            if (!isset($docxPaths['incentive'])) {
                $filtered = $this->mapIncentiveFields($data);
                $pdfPath = $this->generateIncentiveDocxFromHtml($filtered, $uploadPath, true);
                if ($pdfPath) {
                    $docxPaths['incentive'] = [
                        'path' => $pdfPath,
                        'original_name' => 'Incentive_Application_Form.pdf'
                    ];
                }
            }
            if (!isset($docxPaths['recommendation'])) {
                $filtered = $this->mapRecommendationFields($data);
                $pdfPath = $this->generateRecommendationDocxFromHtml($filtered, $uploadPath, true);
                if ($pdfPath) {
                    $docxPaths['recommendation'] = [
                        'path' => $pdfPath,
                        'original_name' => 'Recommendation_Letter_Form.pdf'
                    ];
                }
            }
            if (!isset($docxPaths['terminal'])) {
                $filtered = $this->mapTerminalFields($data);
                $pdfPath = $this->generateTerminalDocxFromHtml($filtered, $uploadPath, true);
                if ($pdfPath) {
                    $docxPaths['terminal'] = [
                        'path' => $pdfPath,
                        'original_name' => 'Terminal_Report_Form.pdf'
                    ];
                }
            }
            } else {
                // For previews, initialize empty arrays
                $attachments = [];
                $docxPaths = [];
            }

            Log::info('Creating database entry', [
                'requestCode' => $requestCode,
                'userId' => $userId
            ]);

            // Use database transaction for atomicity and performance
            $userRequest = DB::transaction(function () use ($existingDraft, $isDraft, $data, $attachments, $docxPaths, $userId, $requestCode) {
                // Update or create request based on existing draft
                if ($existingDraft && $isDraft) {
                    // Update existing draft - reuse the same directory
                    $existingDraft->update([
                        'form_data' => json_encode($data), // Ensure proper JSON encoding
                        'pdf_path' => json_encode([
                            'pdfs' => array_merge($attachments, $docxPaths),
                            'docxs' => [], // Drafts don't have DOCX files
                        ]),
                        'requested_at' => now(), // Update timestamp
                    ]);
                    Log::info('Updated existing draft', [
                        'request_id' => $existingDraft->id,
                        'request_code' => $existingDraft->request_code
                    ]);
                    return $existingDraft;
                } else {
                    // Create new request (only if not a draft or no existing draft)
                    $userRequest = UserRequest::create([
                        'user_id' => $userId,
                        'request_code' => $requestCode,
                        'type' => 'Publication',
                        'status' => $isDraft ? 'draft' : 'pending',
                        'workflow_state' => 'pending_user_signature', // User must sign first (applies to drafts too)
                        'requested_at' => now(),
                        'form_data' => json_encode($data), // Ensure proper JSON encoding
                        'pdf_path' => json_encode([
                            'pdfs' => array_merge($attachments, $docxPaths),
                            'docxs' => [], // All files are now PDFs after conversion
                        ]),
                    ]);
                    Log::info('Created new request', [
                        'request_id' => $userRequest->id,
                        'request_code' => $requestCode,
                        'is_draft' => $isDraft
                    ]);
                    
                    // Store new draft ID in session
                    if ($isDraft) {
                        session(["draft_publication_{$userId}" => $userRequest->id]);
                    }
                    
                    return $userRequest;
                }
            });

            // Activity log creation removed - now handled by notification bell system

            Log::info('Publication request submitted successfully', [
                'requestId' => $userRequest->id,
                'requestCode' => $requestCode
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
                Log::info('Draft saved successfully', [
                    'request_id' => $userRequest->id,
                    'request_code' => $userRequest->request_code,
                    'form_data' => $userRequest->form_data
                ]);
                
                // Check if this is an AJAX request (auto-save) or form submission (manual draft save)
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Draft saved successfully!']);
                } else {
                    // For manual draft saves via form submission, redirect without notification
                    return redirect()->route('publications.request');
                }
            } else {
                // Clear draft session when submitting final request
                session()->forget("draft_publication_{$userId}");
                return redirect()->route('dashboard')->with('success', 'Publication request submitted successfully! Request Code: ' . $requestCode);
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

            // Sanitize file path to prevent directory traversal
            $storagePath = $this->sanitizePath($storagePath);
            
            // Use local disk only (standardized storage)
            $fullPath = Storage::disk('local')->path($storagePath);
            
            if (!file_exists($fullPath)) {
                abort(404, 'File not found: ' . basename($storagePath));
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
                // Sanitize file path to prevent directory traversal
                $filePath = $this->sanitizePath($filePath);
                
                // Use local disk only (standardized storage)
                if (Storage::disk('local')->exists($filePath)) {
                    $fullPath = Storage::disk('local')->path($filePath);
                } else {
                    abort(404, 'PDF file not found on disk');
                }
            } else {
                // Sanitize file path to prevent directory traversal
                $filePath = $this->sanitizePath($filePath);
                
                // Use local disk only (standardized storage)
                if (Storage::disk('local')->exists($filePath)) {
                    $fullPath = Storage::disk('local')->path($filePath);
                } else {
                    abort(404, 'DOCX file not found on disk');
                }
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

    // SECURITY FIX: Removed debug endpoint that exposed system information
    // Debug endpoints should never be available in production

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
            'academicrank' => $data['academicrank'] ?? $data['rank'] ?? '', // Support both field names
            'bibentry' => $data['bibentry'] ?? '',
            'issn' => $data['issn'] ?? '',
            'doi' => $data['doi'] ?? '',
            'scopus' => isset($data['scopus']) ? '☑' : '☐',
            'wos' => isset($data['wos']) ? '☑' : '☐',
            'aci' => isset($data['aci']) ? '☑' : '☐',
            'regional' => isset($data['regional']) ? '☑' : '☐',
            'national' => isset($data['national']) ? '☑' : '☐',
            'international' => isset($data['international']) ? '☑' : '☐',
            'particulars' => $data['particulars'] ?? '',
            'faculty' => $data['facultyname'] ?? $data['faculty_name'] ?? '', // Support both field names
            'facultyname' => $data['facultyname'] ?? $data['faculty_name'] ?? '', // Support both field names
            'centermanager' => $data['centermanager'] ?? $data['center_manager'] ?? '', // Support both field names
            'dean' => $data['collegedean'] ?? $data['dean_name'] ?? '', // Support both field names
            'date' => $data['date'] ?? now()->format('Y-m-d'),
        ];
    }
    private function mapRecommendationFields($data) {
        return [
            'collegeheader' => $data['rec_collegeheader'] ?? '',
            'name' => $data['name'] ?? $data['rec_faculty_name'] ?? '', // Use name field (template uses ${name})
            'facultyname' => $data['name'] ?? $data['rec_faculty_name'] ?? '', // Keep for backward compatibility
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
    public function generatePublicationDocx(Request $request)
    {
        try {
            // Handle GET request for pre-generated files
            if ($request->isMethod('GET') && $request->has('file_path')) {
                $filePath = $request->input('file_path');
                // Validate path (remove directory traversal attempts but keep structure)
                $filePath = str_replace(['../', '..\\'], '', $filePath);
                $filePath = str_replace("\0", '', $filePath);
                $filePath = trim($filePath, '/\\');
                
                $absolutePath = Storage::disk('local')->path($filePath);
                $exists = Storage::disk('local')->exists($filePath);
                $fileExists = file_exists($absolutePath);
                
                Log::info('Fetching pre-generated DOCX file', [
                    'file_path' => $filePath,
                    'absolute_path' => $absolutePath,
                    'docx_type' => $request->input('docx_type', 'incentive'),
                    'storage_exists' => $exists,
                    'file_exists' => $fileExists,
                    'storage_path' => storage_path('app'),
                    'local_disk_path' => Storage::disk('local')->path('')
                ]);
                
                // Check both Storage::exists() and file_exists() for debugging
                if ($exists || $fileExists) {
                    // Ensure we have the correct absolute path
                    if (!$exists && $fileExists) {
                        // Storage says no but file exists - file might be outside storage root
                        // Try to use the absolute path we already computed
                    } else {
                        // Both agree or Storage says it exists - use Storage path
                        $absolutePath = Storage::disk('local')->path($filePath);
                    }
                    $docxType = $request->input('docx_type', 'incentive');
                    $filename = $docxType === 'incentive' 
                        ? 'Incentive_Application_Form.docx' 
                        : ($docxType === 'recommendation'
                            ? 'Recommendation_Letter_Form.docx'
                            : 'Terminal_Report_Form.docx');
                    
                    $userAgent = request()->header('User-Agent');
                    $isIOS = preg_match('/iPhone|iPad|iPod/i', $userAgent);
                    $contentDisposition = $isIOS ? 'inline' : 'attachment';
                    
                    return response()->download($absolutePath, $filename, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'Content-Disposition' => $contentDisposition . '; filename="' . $filename . '"'
                    ]);
                } else {
                    Log::warning('Pre-generated DOCX file not found', [
                        'file_path' => $filePath,
                        'docx_type' => $request->input('docx_type', 'incentive'),
                        'storage_path' => Storage::disk('local')->path($filePath)
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found at path: ' . $filePath
                    ], 404);
                }
            }
            
            $reqId = $request->input('request_id');
            $docxType = $request->input('docx_type', 'incentive');
            $storeForSubmit = $request->input('store_for_submit', false);
            
            $isPreview = !$reqId;
            
            if ($isPreview) {
                // Deterministic cache path for previews to avoid regenerating the same DOCX repeatedly
                // Hash will be computed below once data is merged
                $uploadPath = null; // set after computing unique hash
            } else {
                $userRequest = \App\Models\Request::find($reqId);
                if (!$userRequest) {
                    throw new \Exception('Request not found for DOCX generation');
                }
                $reqCode = $userRequest->request_code;
                $userId = $userRequest->user_id;
                $uploadPath = "requests/{$userId}/{$reqCode}";
                Log::info('Generating Publication DOCX for saved request', ['request_id' => $reqId, 'request_code' => $reqCode]);
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
                'faculty_name' => 'Sample Faculty',
                'center_manager' => 'Sample Manager',
                'dean_name' => 'Sample Dean',
                'date' => date('F j, Y'),
                'title' => 'Sample Title',
                'author' => 'Sample Author',
                'duration' => 'Sample Duration',
                'abstract' => 'Sample Abstract',
                'introduction' => 'Sample Introduction',
                'methodology' => 'Sample Methodology',
                'rnd' => 'Sample R&D',
                'car' => 'Sample CAR',
                'references' => 'Sample References',
                'appendices' => 'Sample Appendices',
                'rec_faculty_name' => 'Sample Faculty',
                'rec_publication_details' => 'Sample Publication Details',
                'rec_indexing_details' => 'Sample Indexing Details',
                'rec_dean_name' => 'Sample Dean'
            ];
            
            // Merge fallback data with form data
            $data = array_merge($fallbackData, $request->all());
            
            // Normalize data for consistent hashing (remove system fields and sort keys)
            $normalizedData = $data;
            // Remove system fields that don't affect document content
            $systemFields = ['_token', 'docx_type', 'store_for_submit', 'request_id', 'save_draft'];
            foreach ($systemFields as $field) {
                unset($normalizedData[$field]);
            }
            // Sort keys for consistent hash regardless of field order
            ksort($normalizedData);
            
            // Removed verbose logging for better performance
            
            // Create stable hash from normalized data
            $hashSource = json_encode([
                'type' => $docxType,
                'data' => $normalizedData
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $uniqueHash = substr(hash('sha256', $hashSource), 0, 16);
            $filename = null;
            $fullPath = null;
            
            // If preview, set a stable cache directory based on the hash and short-circuit if exists
            if ($isPreview) {
                $cacheBase = "temp/docx_cache/{$docxType}_{$uniqueHash}";
                $uploadPath = $cacheBase;
                // Determine expected output filename based on type
                $expectedFile = $docxType === 'incentive'
                    ? "{$cacheBase}/Incentive_Application_Form.docx"
                    : ($docxType === 'recommendation'
                        ? "{$cacheBase}/Recommendation_Letter_Form.docx"
                        : "{$cacheBase}/Terminal_Report_Form.docx");
                $expectedAbsolute = Storage::disk('local')->path($expectedFile);
                $lockFile = $expectedAbsolute . '.lock';
                
                // Use Storage::exists() for better performance and atomicity
                if (Storage::disk('local')->exists($expectedFile)) {
                    // Serve cached file immediately
                    $serveName = $docxType === 'incentive'
                        ? 'Incentive_Application_Form.docx'
                        : ($docxType === 'recommendation'
                            ? 'Recommendation_Letter_Form.docx'
                            : 'Terminal_Report_Form.docx');
                    $userAgent = request()->header('User-Agent');
                    $isIOS = preg_match('/iPhone|iPad|iPod/i', $userAgent);
                    $contentDisposition = $isIOS ? 'inline' : 'attachment';
                    return response()->download($expectedAbsolute, $serveName, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'Content-Disposition' => $contentDisposition . '; filename="' . $serveName . '"'
                    ]);
                }
                
                // If file doesn't exist, check if another process is generating it
                // Wait up to 5 seconds for the file to be generated by another request
                $maxWait = 5; // seconds
                $waitInterval = 0.1; // 100ms
                $waited = 0;
                while (file_exists($lockFile) && $waited < $maxWait) {
                    usleep($waitInterval * 1000000); // Convert to microseconds
                    $waited += $waitInterval;
                    // Check again if file was created
                    if (Storage::disk('local')->exists($expectedFile)) {
                        $serveName = $docxType === 'incentive'
                            ? 'Incentive_Application_Form.docx'
                            : ($docxType === 'recommendation'
                                ? 'Recommendation_Letter_Form.docx'
                                : 'Terminal_Report_Form.docx');
                        $userAgent = request()->header('User-Agent');
                        $isIOS = preg_match('/iPhone|iPad|iPod/i', $userAgent);
                        $contentDisposition = $isIOS ? 'inline' : 'attachment';
                        return response()->download($expectedAbsolute, $serveName, [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'Content-Disposition' => $contentDisposition . '; filename="' . $serveName . '"'
                        ]);
                    }
                }
                
                // Create lock file to indicate we're generating
                // Ensure the cache directory exists before creating the lock file
                if (!Storage::disk('local')->exists($cacheBase)) {
                    Storage::disk('local')->makeDirectory($cacheBase, 0777, true);
                }
                if (!file_exists($lockFile)) {
                    file_put_contents($lockFile, getmypid());
                }
            }
            
            switch ($docxType) {
                case 'incentive':
                    $filtered = $this->mapIncentiveFields($data);
                    $fullPath = $this->generateIncentiveDocxFromHtml($filtered, $uploadPath, false); // No PDF conversion for preview
                    $filename = 'Incentive_Application_Form.docx';
                    break;
                    
                case 'recommendation':
                    $filtered = $this->mapRecommendationFields($data);
                    $fullPath = $this->generateRecommendationDocxFromHtml($filtered, $uploadPath, false); // No PDF conversion for preview
                    $filename = 'Recommendation_Letter_Form.docx';
                    break;
                    
                case 'terminal':
                    $filtered = $this->mapTerminalFields($data);
                    $fullPath = $this->generateTerminalDocxFromHtml($filtered, $uploadPath, false); // No PDF conversion for preview
                    $filename = 'Terminal_Report_Form.docx';
                    break;
                    
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            
            $absolutePath = Storage::disk('local')->path($fullPath);
            if (!file_exists($absolutePath)) {
                throw new \Exception('Generated file not found at: ' . $absolutePath);
            }
            
            // Clean up lock file if it exists (for preview cache)
            if ($isPreview) {
                $lockFile = $absolutePath . '.lock';
                if (file_exists($lockFile)) {
                    @unlink($lockFile);
                }
            }
            
            // Verify file exists before returning
            $verifyPath = Storage::disk('local')->path($fullPath);
            $fileExists = file_exists($verifyPath);
            
            Log::info('Publication DOCX generated and found, ready to serve', [
                'type' => $docxType, 
                'path' => $fullPath,
                'absolute_path' => $verifyPath,
                'file_exists' => $fileExists,
                'isPreview' => $isPreview, 
                'storeForSubmit' => $storeForSubmit
            ]);
            
            // If storing for submit, return file path instead of downloading (works for both preview and saved requests)
            if ($storeForSubmit) {
                if (!$fileExists) {
                    Log::error('Generated file does not exist at path', [
                        'path' => $fullPath,
                        'absolute_path' => $verifyPath
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Generated file not found at path: ' . $fullPath
                    ], 500);
                }
                
                return response()->json([
                    'success' => true,
                    'filePath' => $fullPath,
                    'filename' => $filename
                ]);
            }
            
            // Otherwise, download the file
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
     * Note: This is only called when workflow_state is NOT pending_user_signature
     * When workflow_state is pending_user_signature, the user must sign first
     */
    private function notifyFirstSignatory(\App\Models\Request $request)
    {
        try {
            // Don't notify if user needs to sign first
            if ($request->workflow_state === 'pending_user_signature') {
                Log::info('Skipping first signatory notification - user must sign first', [
                    'requestId' => $request->id,
                    'workflow_state' => $request->workflow_state
                ]);
                return;
            }
            
            // Extract signatories from form data
            $signatories = $this->extractSignatories($request->form_data);
            
            // Find the Research Manager (center_manager) from the signatories
            $researchManager = null;
            foreach ($signatories as $signatory) {
                if ($signatory['role'] === 'center_manager') {
                    $researchManager = $signatory;
                    break;
                }
            }
            
            if ($researchManager) {
                $user = \App\Models\User::where('name', $researchManager['name'])->first();
                if ($user && $user->email) {
                    Mail::to($user->email)->queue(new \App\Mail\SignatoryNotification($request, $researchManager['role'], $researchManager['name']));
                    
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