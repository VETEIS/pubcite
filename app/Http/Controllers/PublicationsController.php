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
        $request = new \stdClass();
        $request->id = null;
        $request->request_code = null;
        return view('publications.request', compact('request'));
    }

    public function adminUpdate(Request $httpRequest, \App\Models\Request $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,endorsed,rejected',
        ]);
        $oldStatus = $request->status;
        $request->status = $httpRequest->input('status');
        $request->save();

        // Activity log for status change
        if ($oldStatus !== $request->status) {
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
            $adminComment = $httpRequest->input('admin_comment', null);
            \Mail::to($request->user->email)->send(new \App\Mail\StatusChangeNotification($request, $request->user, $request->status, $adminComment));
        }

        return back()->with('success', 'Request status updated successfully.');
    }

    public function generateIncentiveDocx(Request $request)
    {
        try {
            $reqId = $request->input('request_id');
            $docxType = $request->input('docx_type', 'incentive');
            $data = $request->all();
            
            // Determine if this is a preview (no request_id) or post-submission (with request_id)
            $isPreview = !$reqId;
            
            if ($isPreview) {
                // Preview mode: use temp directory
                $userId = Auth::id();
                $tempCode = 'preview_' . time() . '_' . Str::random(8);
                $uploadPath = "temp/{$userId}/{$tempCode}";
                Log::info('Generating DOCX in preview mode', ['userId' => $userId, 'tempCode' => $tempCode]);
            } else {
                // Post-submission mode: use request directory
                $userRequest = \App\Models\Request::find($reqId);
                if (!$userRequest) {
                    throw new \Exception('Request not found for DOCX generation');
                }
                $reqCode = $userRequest->request_code;
                $userId = $userRequest->user_id;
                $uploadPath = "requests/{$userId}/{$reqCode}";
                Log::info('Generating DOCX for saved request', ['request_id' => $reqId, 'request_code' => $reqCode]);
            }
            
            $outputPath = null;
            $filename = null;
            
            switch ($docxType) {
                case 'incentive':
                    $outputPath = $this->generateIncentiveDocxFromHtml($data, $uploadPath);
                    $filename = 'Incentive_Application_Form.docx';
                    break;
                case 'recommendation':
                    $outputPath = $this->generateRecommendationDocxFromHtml($data, $uploadPath);
                    $filename = 'Recommendation_Letter_Form.docx';
                    break;
                case 'terminal':
                    $outputPath = $this->generateTerminalDocxFromHtml($data, $uploadPath);
                    $filename = 'Terminal_Report_Form.docx';
                    break;
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            
            $fullPath = storage_path('app/' . $outputPath);
            if (!file_exists($fullPath)) {
                throw new \Exception('Generated file not found');
            }
            
            // Only update database if this is not a preview (i.e., we have a valid request_id)
            if (!$isPreview && $userRequest) {
                $pdfPath = $userRequest->pdf_path ? json_decode($userRequest->pdf_path, true) : [];
                if (!isset($pdfPath['docxs'])) $pdfPath['docxs'] = [];
                $pdfPath['docxs'][$docxType] = preg_replace('/^public\//', '', $outputPath);
                $userRequest->pdf_path = json_encode($pdfPath);
                $userRequest->save();
                Log::info('Updated pdf_path for request', ['request_id' => $reqId, 'docxType' => $docxType, 'outputPath' => $outputPath]);
            } else {
                Log::info('Skipping database update for preview mode', ['docxType' => $docxType, 'outputPath' => $outputPath]);
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
            // Ensure directory exists
            $publicUploadPath = 'public/' . ltrim($uploadPath, '/');
            $fullPath = storage_path('app/' . $publicUploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            // Prepare checkboxes for type
            $type = $data['type'] ?? '';
            $typeChecked = [
                'regional' => $type === 'Regional' ? '☑' : '☐',
                'national' => $type === 'National' ? '☑' : '☐',
                'international' => $type === 'International' ? '☑' : '☐',
            ];
            // Prepare checkboxes for indexed in
            $selectedIndex = $data['indexed_in'] ?? '';
            $indexedChecked = [
                'scopus' => $selectedIndex === 'Scopus' ? '☑' : '☐',
                'wos' => $selectedIndex === 'Web of Science' ? '☑' : '☐',
                'aci' => $selectedIndex === 'ACI' ? '☑' : '☐',
                'pubmed' => $selectedIndex === 'PubMed' ? '☑' : '☐',
            ];
            // Use TemplateProcessor
            $templatePath = storage_path('app/templates/Incentive_Application_Form.docx');
            $outputPath = $publicUploadPath . '/Incentive_Application_Form.docx';
            $fullOutputPath = storage_path('app/' . $outputPath);
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('collegeheader', $data['collegeheader'] ?? '');
            $templateProcessor->setValue('name', $data['name'] ?? '');
            $templateProcessor->setValue('academicrank', $data['academicrank'] ?? '');
            $templateProcessor->setValue('employment', $data['employmentstatus'] ?? '');
            $templateProcessor->setValue('college', $data['college'] ?? '');
            $templateProcessor->setValue('campus', $data['campus'] ?? '');
            $templateProcessor->setValue('field', $data['field'] ?? '');
            $templateProcessor->setValue('years', $data['years'] ?? '');
            $templateProcessor->setValue('title', $data['papertitle'] ?? '');
            $templateProcessor->setValue('coauthor', $data['coauthors'] ?? '');
            $templateProcessor->setValue('journaltitle', $data['journaltitle'] ?? '');
            $templateProcessor->setValue('version', $data['version'] ?? '');
            $templateProcessor->setValue('pissn', $data['pissn'] ?? '');
            $templateProcessor->setValue('eissn', $data['eissn'] ?? '');
            $templateProcessor->setValue('doi', $data['doi'] ?? '');
            $templateProcessor->setValue('publisher', $data['publisher'] ?? '');
            $templateProcessor->setValue('citescore', $data['citescore'] ?? '');
            $templateProcessor->setValue('particulars', $data['particulars'] ?? '');
            $templateProcessor->setValue('facultyname', $data['facultyname'] ?? '');
            $templateProcessor->setValue('centermanager', $data['centermanager'] ?? '');
            $templateProcessor->setValue('collegedean', $data['collegedean'] ?? '');
            $templateProcessor->setValue('regional', $typeChecked['regional']);
            $templateProcessor->setValue('national', $typeChecked['national']);
            $templateProcessor->setValue('international', $typeChecked['international']);
            $templateProcessor->setValue('scopus', $indexedChecked['scopus']);
            $templateProcessor->setValue('wos', $indexedChecked['wos']);
            $templateProcessor->setValue('aci', $indexedChecked['aci']);
            $templateProcessor->setValue('pubmed', $indexedChecked['pubmed']);
            // Additional variables found in template
            $templateProcessor->setValue('date', now()->format('Y-m-d'));
            $templateProcessor->setValue('faculty', $data['facultyname'] ?? '');
            $templateProcessor->setValue('dean', $data['collegedean'] ?? '');
            $templateProcessor->setValue('references', '');
            $templateProcessor->setValue('appendices', '');
            $templateProcessor->saveAs($fullOutputPath);
            Log::info('DOCX creation completed', ['outputPath' => $fullOutputPath]);
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
            $publicUploadPath = 'public/' . ltrim($uploadPath, '/');
            $fullPath = storage_path('app/' . $publicUploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            $templatePath = storage_path('app/templates/Recommendation_Letter_Form.docx');
            $outputPath = $publicUploadPath . '/Recommendation_Letter_Form.docx';
            $fullOutputPath = storage_path('app/' . $outputPath);
            $templateProcessor = new TemplateProcessor($templatePath);
            // Set all relevant fields for recommendation letter (matching template)
            $templateProcessor->setValue('collegeheader', $data['rec_collegeheader'] ?? '');
            $templateProcessor->setValue('date', $data['rec_date'] ?? now()->format('Y-m-d'));
            $templateProcessor->setValue('facultyname', $data['rec_facultyname'] ?? '');
            $templateProcessor->setValue('details', $data['details'] ?? '');
            $templateProcessor->setValue('indexing', $data['indexing'] ?? '');
            $templateProcessor->setValue('dean', $data['dean'] ?? '');
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
            $publicUploadPath = 'public/' . ltrim($uploadPath, '/');
            $fullPath = storage_path('app/' . $publicUploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            $templatePath = storage_path('app/templates/Terminal_Report_Form.docx');
            $outputPath = $publicUploadPath . '/Terminal_Report_Form.docx';
            $fullOutputPath = storage_path('app/' . $outputPath);
            $templateProcessor = new TemplateProcessor($templatePath);
            // Set all relevant fields for terminal report (update these after running debug script)
            $templateProcessor->setValue('title', $data['title'] ?? '');
            $templateProcessor->setValue('author', $data['author'] ?? '');
            $templateProcessor->setValue('duration', $data['duration'] ?? '');
            $templateProcessor->setValue('abstract', $data['abstract'] ?? '');
            $templateProcessor->setValue('introduction', $data['introduction'] ?? '');
            $templateProcessor->setValue('methodology', $data['methodology'] ?? '');
            $templateProcessor->setValue('rnd', $data['rnd'] ?? '');
            $templateProcessor->setValue('car', $data['car'] ?? '');
            $templateProcessor->setValue('references', $data['references'] ?? '');
            $templateProcessor->setValue('appendices', $data['appendices'] ?? '');
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
            // Load the template
            $templatePath = storage_path('app/templates/' . $templateName);
            
            if (!file_exists($templatePath)) {
                throw new \Exception("Template file not found: {$templatePath}");
            }
            
            Log::info('Loading template', ['templatePath' => $templatePath]);
            
            // Load the existing document
            $phpWord = IOFactory::load($templatePath);
            
            // Get all sections
            $sections = $phpWord->getSections();
            
            foreach ($sections as $section) {
                // Get all elements in the section
                $elements = $section->getElements();
                
                foreach ($elements as $element) {
                    $this->replacePlaceholdersInElement($element, $data, $typeChecked, $indexedChecked);
                }
            }
            
            // Save the modified document
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
        // Handle different types of elements
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $this->replacePlaceholdersInText($element, $data, $typeChecked, $indexedChecked);
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            // Process text runs
            foreach ($element->getElements() as $textElement) {
                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                    $this->replacePlaceholdersInText($textElement, $data, $typeChecked, $indexedChecked);
                }
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            // Process tables
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
        
        // Replace all placeholders with actual data
        $replacements = [
            '{{collegeheader}}' => $data['collegeheader'] ?? '',
            '{{name}}' => $data['name'] ?? '',
            '{{academicrank}}' => $data['academicrank'] ?? '',
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
            '{{facultyname}}' => $data['facultyname'] ?? '',
            '{{centermanager}}' => $data['centermanager'] ?? '',
            '{{collegedean}}' => $data['collegedean'] ?? '',
            
            // Recommendation letter placeholders
            '{{rec_collegeheader}}' => $data['rec_collegeheader'] ?? '',
            '{{rec_date}}' => $data['rec_date'] ?? now()->format('Y-m-d'),
            '{{rec_facultyname}}' => $data['rec_facultyname'] ?? '',
            '{{details}}' => $data['details'] ?? '',
            '{{indexing}}' => $data['indexing'] ?? '',
            '{{dean}}' => $data['dean'] ?? '',
            
            // Terminal report placeholders
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
            
            // Checkbox replacements
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
        // This method is no longer used but kept for compatibility
        // The actual document creation is now done in the specific create*Document methods
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
        // Delete all associated files from storage
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
        foreach ($allFiles as $filePath) {
            \Storage::disk('public')->delete($filePath);
        }
        // Remove the per-request directory and all its contents
        if (isset($request->user_id) && isset($request->request_code)) {
            $dir = "requests/{$request->user_id}/{$request->request_code}";
            $fullDir = storage_path('app/public/' . $dir);
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
        // Store request details for permanent activity log record
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
        
        // Activity log for deletion (must be before delete)
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

    // NEW SIMPLIFIED APPROACH - Single step submission
    public function submitPublicationRequest(Request $request)
    {
        Log::info('Publication request submission started', [
            'user_id' => Auth::id(),
            'has_files' => $request->hasFile('article_pdf')
        ]);

        // Validate all form data
        $validator = Validator::make($request->all(), [
            // Incentive Application fields
            //'collegeheader' => 'required|string',
            'name' => 'required|string',
            'academicrank' => 'required|string',
            'employmentstatus' => 'required|string',
            'college' => 'required|string',
            'campus' => 'required|string',
            'field' => 'required|string',
            'years' => 'required|numeric',
            'papertitle' => 'required|string',
            'coauthors' => 'nullable|string',
            'journaltitle' => 'required|string',
            'version' => 'required|string',
            'pissn' => 'nullable|string',
            'eissn' => 'nullable|string',
            'doi' => 'nullable|string',
            'publisher' => 'required|string',
            'type' => 'required|string',
            'indexed_in' => 'required|string',
            'citescore' => 'nullable|string',
            'particulars' => 'required|string',
            'facultyname' => 'required|string',
            'centermanager' => 'nullable|string',
            'collegedean' => 'nullable|string',
            // Recommendation Letter fields
            'rec_collegeheader' => 'required|string',
            'rec_date' => 'required|string',
            'rec_facultyname' => 'required|string',
            'details' => 'required|string',
            'indexing' => 'required|string',
            'dean' => 'required|string',
            // Terminal Report fields
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
            // File uploads
            'article_pdf' => 'required|file|mimes:pdf|max:20480',
            'cover_pdf' => 'required|file|mimes:pdf|max:20480',
            'acceptance_pdf' => 'required|file|mimes:pdf|max:20480',
            'peer_review_pdf' => 'required|file|mimes:pdf|max:20480',
            'terminal_report_pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        if ($validator->fails()) {
            Log::info('Publication request validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        try {
            // Store uploaded files
            $userId = Auth::id();
            $requestCode = 'PUB-' . now()->format('Ymd-His');
            $uploadPath = "requests/{$userId}/{$requestCode}";
            
            Log::info('Processing file uploads', [
                'requestCode' => $requestCode,
                'uploadPath' => $uploadPath
            ]);
            
            $attachments = [];
            foreach ([
                'article_pdf',
                'cover_pdf',
                'acceptance_pdf',
                'peer_review_pdf',
                'terminal_report_pdf'
            ] as $field) {
                $file = $request->file($field);
                
                Log::info('Processing file upload', [
                    'field' => $field,
                    'file_exists' => $file ? 'YES' : 'NO',
                    'original_name' => $file ? $file->getClientOriginalName() : 'N/A',
                    'file_size' => $file ? $file->getSize() : 'N/A',
                    'mime_type' => $file ? $file->getMimeType() : 'N/A'
                ]);
                
                if (!$file) {
                    Log::error('File upload failed - file is null', ['field' => $field]);
                    throw new \Exception("File upload failed for field: {$field}");
                }
                
                // Use the public disk instead of the default local disk
                $storedPath = $file->storeAs($uploadPath, $file->getClientOriginalName(), 'public');
                // Remove leading 'public/' if present
                $cleanPath = preg_replace('/^public\//', '', $storedPath);
                $attachments[$field] = [
                    'path' => $cleanPath,
                    'original_name' => $file->getClientOriginalName(),
                ];
            }

            // Generate DOCX files using HTML approach
            $docxPaths = [];
            $docxPaths['incentive'] = preg_replace('/^public\//', '', $this->generateIncentiveDocxFromHtml($data, $uploadPath));
            $docxPaths['recommendation'] = preg_replace('/^public\//', '', $this->generateRecommendationDocxFromHtml($data, $uploadPath));
            $docxPaths['terminal'] = preg_replace('/^public\//', '', $this->generateTerminalDocxFromHtml($data, $uploadPath));

            Log::info('Creating database entry', [
                'requestCode' => $requestCode,
                'userId' => $userId
            ]);

            // Save to database
            $userRequest = UserRequest::create([
                'user_id' => $userId,
                'request_code' => $requestCode,
                'type' => 'Publication',
                'status' => 'pending',
                'requested_at' => now(),
                'form_data' => $data,
                'pdf_path' => json_encode([
                    'pdfs' => $attachments,
                    'docxs' => $docxPaths,
                ]),
            ]);

            // Activity log for creation
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

            Log::info('Publication request submitted successfully', [
                'requestId' => $userRequest->id,
                'requestCode' => $requestCode
            ]);

            // Send email notifications
            try {
                // Send notification to user
                Mail::to($userRequest->user->email)->send(new SubmissionNotification($userRequest, $userRequest->user, false));
                
                // Send notification to all admins
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
                // Don't fail the request if email fails
            }

            return redirect()->route('publications.request')->with('success', 'Publication request submitted successfully! Request Code: ' . $requestCode);

        } catch (\Exception $e) {
            Log::error('Error submitting publication request: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error submitting request. Please try again.');
        }
    }

    /**
     * Admin: Download any file (PDF or DOCX) for a request, with user name in filename.
     */
    public function adminDownloadFile(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Unauthorized');
            }

            $fileType = $httpRequest->query('type'); // 'pdf' or 'docx'
            $fileKey = $httpRequest->query('key');   // e.g. 'article_pdf', 'incentive', etc.

            if (!$fileType || !$fileKey) {
                abort(400, 'Missing file type or key');
            }

            // Decode the pdf_path JSON
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
                // Get user name for filename
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

            // Build the full file path
            $fullPath = storage_path('app/public/' . $storagePath);

            // Log for debugging
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

            // Get user name for filename
            $userName = $request->user->name ?? 'user';
            $userNameSlug = Str::slug($userName, '_');
            $downloadName = $userNameSlug . '_' . $originalName;

            // Set proper MIME type
            $mime = $ext === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            // Return file download response
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

    /**
     * Simple file serving method - alternative to adminDownloadFile
     */
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

            // Get file data
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

            // Check if file exists based on type
            if ($fileType === 'pdf') {
            if (!Storage::disk('public')->exists($filePath)) {
                    abort(404, 'PDF file not found on disk');
                }
                $fullPath = storage_path('app/public/' . $filePath);
            } else {
                if (!file_exists(storage_path('app/' . $filePath))) {
                    abort(404, 'DOCX file not found on disk');
                }
                $fullPath = storage_path('app/' . $filePath);
            }

            // Get user name for filename
            $userName = $request->user->name ?? 'user';
            $userNameSlug = Str::slug($userName, '_');
            $downloadName = $userNameSlug . '_' . $fileName;

            // Serve file using Storage facade
            return response()->download($fullPath, $downloadName);

        } catch (\Exception $e) {
            Log::error('File serving error', [
                'error' => $e->getMessage(),
                'request_id' => $request->id ?? 'unknown'
            ]);
            abort(500, 'File serving failed');
        }
    }

    /**
     * Debug method to check file paths and storage
     */
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
            'storage_base' => storage_path('app'),
        ];

        if ($paths) {
            $debug['pdfs'] = [];
            if (isset($paths['pdfs'])) {
                foreach ($paths['pdfs'] as $key => $fileInfo) {
                    $fullPath = storage_path('app/public/' . $fileInfo['path']);
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
                    $fullPath = storage_path('app/' . $storagePath);
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

    /**
     * Admin: Download all files for a request as a ZIP archive.
     */
    public function adminDownloadZip(Request $httpRequest, \App\Models\Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Unauthorized');
            }

            $pdfPath = $request->pdf_path;
            $paths = json_decode($pdfPath, true);
            
            // Debug logging
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
            
            // Create a temporary ZIP file
            $zipPath = storage_path('app/temp/' . $zipName);
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

            // Add PDF files
            if (isset($paths['pdfs'])) {
                foreach ($paths['pdfs'] as $key => $fileInfo) {
                    $filePath = storage_path('app/public/' . $fileInfo['path']);
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

            // Add DOCX files
            if (isset($paths['docxs'])) {
                foreach ($paths['docxs'] as $key => $storagePath) {
                    $filePath = storage_path('app/' . $storagePath);
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

            // If no files were added, return an error
            if (empty($addedFiles)) {
                abort(404, 'No files found on disk for this request. Files may have been deleted or moved.');
            }

            // Return the ZIP file for download
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
} 