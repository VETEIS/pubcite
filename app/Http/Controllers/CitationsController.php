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

class CitationsController extends Controller
{
    public function create()
    {
        return view('citations.request');
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

    public function generateCitationDocx(Request $request)
    {
        try {
            $data = $request->all();
            $docxType = $request->input('docx_type', 'incentive');
            
            Log::info('Citation DOCX generation - Received data:', ['type' => $docxType, 'data' => $data]);
            
            // Generate the DOCX file based on type
            $uploadPath = 'generated';
            $outputPath = null;
            $filename = null;
            
            switch ($docxType) {
                case 'incentive':
                    $outputPath = $this->generateCitationIncentiveDocxFromHtml($data, $uploadPath);
                    $filename = 'Cite_Incentive_Application.docx';
                    break;
                    
                case 'recommendation':
                    $outputPath = $this->generateCitationRecommendationDocxFromHtml($data, $uploadPath);
                    $filename = 'Cite_Recommendation_Letter.docx';
                    break;
                    
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            
            // Get the full path for download
            $fullPath = storage_path('app/' . $outputPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('Generated file not found');
            }
            
            Log::info('Citation DOCX generated successfully', ['type' => $docxType, 'path' => $fullPath]);
            
            // Return the file for download
            return response()->download($fullPath, $filename, [
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

    private function generateCitationIncentiveDocxFromHtml($data, $uploadPath)
    {
        try {
            Log::info('Starting generateCitationIncentiveDocxFromHtml', ['uploadPath' => $uploadPath]);
            // Ensure directory exists
            $fullPath = storage_path('app/' . $uploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            
            // Use TemplateProcessor for citation incentive template
            $templatePath = storage_path('app/templates/Cite_Incentive_Application.docx');
            $outputPath = $uploadPath . '/Cite_Incentive_Application.docx';
            $fullOutputPath = storage_path('app/' . $outputPath);
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Set values for citation incentive template using correct template variable names
            $templateProcessor->setValue('collegeheader', $data['collegeheader'] ?? '');
            $templateProcessor->setValue('name', $data['applicant_name'] ?? '');
            $templateProcessor->setValue('employment', $data['employment_status'] ?? '');
            $templateProcessor->setValue('rank', $data['academic_rank'] ?? '');
            $templateProcessor->setValue('campus', $data['campus'] ?? '');
            $templateProcessor->setValue('college', $data['college'] ?? '');
            $templateProcessor->setValue('years', $data['years_university'] ?? '');
            $templateProcessor->setValue('field', $data['field_specialization'] ?? '');
            
            // Citing paper details
            $templateProcessor->setValue('title', $data['citing_title'] ?? '');
            $templateProcessor->setValue('authors', $data['citing_authors'] ?? '');
            $templateProcessor->setValue('journal', $data['citing_journal'] ?? '');
            $templateProcessor->setValue('version', $data['citing_volume'] ?? '');
            $templateProcessor->setValue('pissn', $data['citing_pissn'] ?? '');
            $templateProcessor->setValue('eissn', $data['citing_eissn'] ?? '');
            $templateProcessor->setValue('doi', $data['citing_doi'] ?? '');
            $templateProcessor->setValue('citescore', $data['citing_citescore'] ?? '');
            
            // Indexing checkboxes
            $scopus = isset($data['indexed_scopus']) ? '☒' : '☐';
            $wos = isset($data['indexed_wos']) ? '☒' : '☐';
            $aci = isset($data['indexed_aci']) ? '☒' : '☐';
            $pubmed = isset($data['indexed_pubmed']) ? '☒' : '☐';
            
            $templateProcessor->setValue('scopus', $scopus);
            $templateProcessor->setValue('wos', $wos);
            $templateProcessor->setValue('aci', $aci);
            $templateProcessor->setValue('pubmed', $pubmed);
            
            // Cited paper details
            $templateProcessor->setValue('citedtitle', $data['cited_title'] ?? '');
            $templateProcessor->setValue('coauthors', $data['cited_coauthors'] ?? '');
            $templateProcessor->setValue('citedjournal', $data['cited_journal'] ?? '');
            $templateProcessor->setValue('citedversion', $data['cited_volume'] ?? '');
            
            // Signature fields
            $templateProcessor->setValue('facultyname', $data['faculty_name'] ?? '');
            $templateProcessor->setValue('centermanager', $data['center_manager'] ?? '');
            $templateProcessor->setValue('dean', $data['dean_name'] ?? '');
            $templateProcessor->setValue('date', $data['date'] ?? now()->format('F d, Y'));
            
            $templateProcessor->saveAs($fullOutputPath);
            Log::info('Citation DOCX creation completed', ['outputPath' => $fullOutputPath]);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Error in generateCitationIncentiveDocxFromHtml: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    private function generateCitationRecommendationDocxFromHtml($data, $uploadPath)
    {
        try {
            $fullPath = storage_path('app/' . $uploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            $templatePath = storage_path('app/templates/Cite_Recommendation_Letter.docx');
            $outputPath = $uploadPath . '/Cite_Recommendation_Letter.docx';
            $fullOutputPath = storage_path('app/' . $outputPath);
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Set all relevant fields for citation recommendation letter using correct template variable names
            $templateProcessor->setValue('collegeheader', $data['rec_collegeheader'] ?? '');
            $templateProcessor->setValue('date', $data['rec_date'] ?? now()->format('F d, Y'));
            $templateProcessor->setValue('facultyname', $data['rec_faculty_name'] ?? '');
            $templateProcessor->setValue('details', $data['rec_citing_details'] ?? '');
            $templateProcessor->setValue('indexing', $data['rec_indexing_details'] ?? '');
            $templateProcessor->setValue('dean', $data['rec_dean_name'] ?? '');
            
            $templateProcessor->saveAs($fullOutputPath);
            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Error in generateCitationRecommendationDocxFromHtml: ' . $e->getMessage());
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            $request = UserRequest::findOrFail($id);
            
            // Delete associated files
            if ($request->pdf_path) {
                $pdfData = is_array($request->pdf_path) ? $request->pdf_path : json_decode($request->pdf_path, true);
                if (isset($pdfData['pdfs'])) {
                    foreach ($pdfData['pdfs'] as $pdf) {
                        Storage::disk('public')->delete($pdf['path']);
                    }
                }
            }
            
            $request->delete();
            return back()->with('success', 'Request deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting request: ' . $e->getMessage());
            return back()->with('error', 'Error deleting request.');
        }
    }

    public function submitCitationRequest(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'college' => 'required|string|max:255',
                'papertitle' => 'required|string|max:500',
                'journaltitle' => 'required|string|max:255',
                'facultyname' => 'required|string|max:255',
                'collegedean' => 'required|string|max:255',
                'dean' => 'required|string|max:255',
                'article_pdf' => 'required|file|mimes:pdf|max:10240',
                'citation_report_pdf' => 'required|file|mimes:pdf|max:10240',
                'impact_factor_pdf' => 'required|file|mimes:pdf|max:10240',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Generate unique request code
            $requestCode = 'CITE-' . date('Y') . '-' . strtoupper(Str::random(8));

            // Store PDF files
            $pdfPaths = [];
            
            // Article PDF
            if ($request->hasFile('article_pdf')) {
                $articlePath = $request->file('article_pdf')->store('publications', 'public');
                $pdfPaths['article'] = [
                    'path' => $articlePath,
                    'original_name' => $request->file('article_pdf')->getClientOriginalName()
                ];
            }

            // Citation Report PDF
            if ($request->hasFile('citation_report_pdf')) {
                $citationReportPath = $request->file('citation_report_pdf')->store('publications', 'public');
                $pdfPaths['citation_report'] = [
                    'path' => $citationReportPath,
                    'original_name' => $request->file('citation_report_pdf')->getClientOriginalName()
                ];
            }

            // Impact Factor PDF
            if ($request->hasFile('impact_factor_pdf')) {
                $impactFactorPath = $request->file('impact_factor_pdf')->store('publications', 'public');
                $pdfPaths['impact_factor'] = [
                    'path' => $impactFactorPath,
                    'original_name' => $request->file('impact_factor_pdf')->getClientOriginalName()
                ];
            }

            // Generate DOCX files
            $docxPaths = [];
            
            // Generate Incentive Application DOCX
            try {
                $incentivePath = $this->generateCitationIncentiveDocxFromHtml($request->all(), 'generated');
                $docxPaths['incentive_application'] = $incentivePath;
            } catch (\Exception $e) {
                Log::error('Error generating incentive DOCX: ' . $e->getMessage());
            }

            // Generate Recommendation Letter DOCX
            try {
                $recommendationPath = $this->generateCitationRecommendationDocxFromHtml($request->all(), 'generated');
                $docxPaths['recommendation_letter'] = $recommendationPath;
            } catch (\Exception $e) {
                Log::error('Error generating recommendation DOCX: ' . $e->getMessage());
            }

            // Create the request record
            $userRequest = new UserRequest();
            $userRequest->user_id = Auth::id();
            $userRequest->request_code = $requestCode;
            $userRequest->type = 'Citation';
            $userRequest->status = 'pending';
            $userRequest->requested_at = now();
            $userRequest->pdf_path = json_encode(['pdfs' => $pdfPaths]);
            $userRequest->form_data = json_encode($request->except(['_token', 'article_pdf', 'citation_report_pdf', 'impact_factor_pdf']));
            
            // Store DOCX paths if generated
            if (!empty($docxPaths)) {
                $userRequest->docx_path = json_encode(['docxs' => $docxPaths]);
            }

            $userRequest->save();

            Log::info('Citation request submitted successfully', [
                'request_code' => $requestCode,
                'user_id' => Auth::id(),
                'pdf_count' => count($pdfPaths),
                'docx_count' => count($docxPaths)
            ]);

            return redirect()->route('citations.success')->with('request_code', $requestCode);

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
            $fullPath = storage_path('app/public/' . $filePath);
            
            if (!file_exists($fullPath)) {
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
                $fullPath = storage_path('app/public/' . $filePath);
                
                if (!file_exists($fullPath)) {
                    return response()->json(['error' => 'File not found on disk'], 404);
                }
                
                return response()->file($fullPath);
                
            } elseif ($type === 'docx') {
                $docxData = is_array($request->docx_path) ? $request->docx_path : json_decode($request->docx_path, true);
                
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
                'docx_path' => $request->docx_path,
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
            if ($request->docx_path) {
                $docxData = is_array($request->docx_path) ? $request->docx_path : json_decode($request->docx_path, true);
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
            if ($request->docx_path) {
                $docxData = is_array($request->docx_path) ? $request->docx_path : json_decode($request->docx_path, true);
                
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
} 