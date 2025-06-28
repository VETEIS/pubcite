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
use PhpOffice\PhpWord\TemplateProcessor;

class PublicationsController extends Controller
{
    public function create()
    {
        return view('publications.request');
    }

    public function adminUpdate(Request $httpRequest, \App\Models\Request $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,endorsed,rejected',
        ]);
        $request->status = $httpRequest->input('status');
        $request->save();
        return back()->with('success', 'Request status updated successfully.');
    }

    public function generateIncentiveDocx(Request $request)
    {
        try {
            $data = $request->all();
            $docxType = $request->input('docx_type', 'incentive');
            
            Log::info('DOCX generation - Received data:', ['type' => $docxType, 'data' => $data]);
            
            // Generate the DOCX file based on type
            $uploadPath = 'generated';
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
            
            // Get the full path for download
            $fullPath = storage_path('app/' . $outputPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('Generated file not found');
            }
            
            Log::info('DOCX generated successfully', ['type' => $docxType, 'path' => $fullPath]);
            
            // Return the file for download
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
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
            $fullPath = storage_path('app/' . $uploadPath);
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
            $outputPath = $uploadPath . '/Incentive_Application_Form.docx';
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
            $templateProcessor->setValue('published', $data['publisher'] ?? '');
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
            $fullPath = storage_path('app/' . $uploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            $templatePath = storage_path('app/templates/Recommendation_Letter_Form.docx');
            $outputPath = $uploadPath . '/Recommendation_Letter_Form.docx';
            $fullOutputPath = storage_path('app/' . $outputPath);
            $templateProcessor = new TemplateProcessor($templatePath);
            // Set all relevant fields for recommendation letter (matching template)
            $templateProcessor->setValue('collegeheader', $data['collegeheader'] ?? '');
            $templateProcessor->setValue('date', now()->format('Y-m-d'));
            $templateProcessor->setValue('facultyname', $data['facultyname'] ?? '');
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
            $fullPath = storage_path('app/' . $uploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            $templatePath = storage_path('app/templates/Terminal_Report_Form.docx');
            $outputPath = $uploadPath . '/Terminal_Report_Form.docx';
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
                
                // Process each element
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
            return redirect()->back()->with('error', 'Unauthorized.');
        }
        $request = \App\Models\Request::find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }
        $request->delete();
        return redirect()->back()->with('success', 'Request deleted successfully.');
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
            'collegeheader' => 'required|string',
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
            
            // File uploads
            'article_pdf' => 'required|file|mimes:pdf',
            'cover_pdf' => 'required|file|mimes:pdf',
            'acceptance_pdf' => 'required|file|mimes:pdf',
            'peer_review_pdf' => 'required|file|mimes:pdf',
            'terminal_report_pdf' => 'required|file|mimes:pdf',
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
            $requestCode = 'REQ-' . now()->format('Ymd-His');
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
            $attachments[$field] = [
                    'path' => $file->store($uploadPath),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

            // Generate DOCX files using HTML approach
            $docxPaths = [];
            $docxPaths['incentive'] = $this->generateIncentiveDocxFromHtml($data, $uploadPath);
            $docxPaths['recommendation'] = $this->generateRecommendationDocxFromHtml($data, $uploadPath);
            $docxPaths['terminal'] = $this->generateTerminalDocxFromHtml($data, $uploadPath);

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

            Log::info('Publication request submitted successfully', [
                'requestId' => $userRequest->id,
                'requestCode' => $requestCode
            ]);

            return redirect()->route('publications.request')->with('success', 'Publication request submitted successfully! Request Code: ' . $requestCode);

        } catch (\Exception $e) {
            Log::error('Error submitting publication request: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error submitting request. Please try again.');
        }
    }
} 