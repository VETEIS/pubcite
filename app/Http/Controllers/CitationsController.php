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
use App\Mail\SubmissionNotification;
use App\Mail\StatusChangeNotification;

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
        $oldStatus = $request->status;
        $request->status = $httpRequest->input('status');
        $request->save();

        // Send status change email if status changed
        if ($oldStatus !== $request->status) {
            $adminComment = $httpRequest->input('admin_comment', null);
            Mail::to($request->user->email)->send(new StatusChangeNotification($request, $request->user, $request->status, $adminComment));
        }

        return back()->with('success', 'Request status updated successfully.');
    }

    public function generateCitationDocx(Request $request)
    {
        try {
            $data = $request->all();
            $docxType = $request->input('docx_type', 'incentive');
            $reqId = $request->input('request_id');
            
            // Determine if this is a preview (no request_id) or post-submission (with request_id)
            $isPreview = !$reqId;
            
            if ($isPreview) {
                // Preview mode: use temp directory
                $userId = Auth::id();
                $tempCode = 'preview_' . time() . '_' . Str::random(8);
                $uploadPath = "temp/{$userId}/{$tempCode}";
                Log::info('Generating Citation DOCX in preview mode', ['userId' => $userId, 'tempCode' => $tempCode]);
            } else {
                // Post-submission mode: use request directory
                $userRequest = \App\Models\Request::find($reqId);
                if (!$userRequest) {
                    throw new \Exception('Request not found for DOCX generation');
                }
                $requestCode = $userRequest->request_code;
                $userId = $userRequest->user_id;
                $uploadPath = "requests/{$userId}/{$requestCode}";
                Log::info('Generating Citation DOCX for saved request', ['request_id' => $reqId, 'request_code' => $requestCode]);
            }
            
            Log::info('Citation DOCX generation - Received data:', ['type' => $docxType, 'data' => $data, 'isPreview' => $isPreview]);
            
            // Generate a unique hash for the request data and docx type
            $hashSource = json_encode([
                'type' => $docxType,
                'data' => $data
            ]);
            $uniqueHash = substr(hash('sha256', $hashSource), 0, 16); // 16 chars is enough
            $filename = null;
            $outputPath = null;
            
            switch ($docxType) {
                case 'incentive':
                    $filename = "Cite_Incentive_Application_{$uniqueHash}.docx";
                    $outputPath = "$uploadPath/$filename";
                    break;
                    
                case 'recommendation':
                    $filename = "Cite_Recommendation_Letter_{$uniqueHash}.docx";
                    $outputPath = "$uploadPath/$filename";
                    break;
                    
                default:
                    throw new \Exception('Invalid document type: ' . $docxType);
            }
            
            $fullPath = storage_path('app/' . $outputPath);
            
            // If file does not exist, generate it
            if (!file_exists($fullPath)) {
                switch ($docxType) {
                    case 'incentive':
                        $this->generateCitationIncentiveDocxFromHtml($data, $uploadPath, $filename);
                        break;
                    case 'recommendation':
                        $this->generateCitationRecommendationDocxFromHtml($data, $uploadPath, $filename);
                        break;
                }
            }
            
            if (!file_exists($fullPath)) {
                throw new \Exception('Generated file not found');
            }
            
            Log::info('Citation DOCX generated or cached successfully', ['type' => $docxType, 'path' => $fullPath, 'isPreview' => $isPreview]);
            
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

    private function generateCitationIncentiveDocxFromHtml($data, $uploadPath, $filename = 'Cite_Incentive_Application.docx')
    {
        try {
            Log::info('Starting generateCitationIncentiveDocxFromHtml', ['uploadPath' => $uploadPath]);
            $publicUploadPath = 'public/' . ltrim($uploadPath, '/');
            $fullPath = storage_path('app/' . $publicUploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            
            $templatePath = storage_path('app/templates/Cite_Incentive_Application.docx');
            $outputPath = $publicUploadPath . '/' . $filename;
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

    private function generateCitationRecommendationDocxFromHtml($data, $uploadPath, $filename = 'Cite_Recommendation_Letter.docx')
    {
        try {
            $publicUploadPath = 'public/' . ltrim($uploadPath, '/');
            $fullPath = storage_path('app/' . $publicUploadPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            $templatePath = storage_path('app/templates/Cite_Recommendation_Letter.docx');
            $outputPath = $publicUploadPath . '/' . $filename;
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
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                return back()->with('error', 'Unauthorized.');
            }
            
            $request = UserRequest::findOrFail($id);
            
            // Delete associated files (PDFs and DOCXs)
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
            return back()->with('success', 'Request and files deleted successfully.');
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
                'applicant_name' => 'required|string|max:255',
                'college' => 'required|string|max:255',
                'citing_title' => 'required|string|max:500',
                'citing_journal' => 'required|string|max:255',
                'cited_title' => 'required|string|max:500',
                'faculty_name' => 'required|string|max:255',
                'dean_name' => 'required|string|max:255',
                'recommendation_letter' => 'required|file|mimes:pdf|max:20480',
                'citing_article' => 'required|file|mimes:pdf|max:20480',
                'citing_journal_cover' => 'required|file|mimes:pdf|max:20480',
                'cited_article' => 'required|file|mimes:pdf|max:20480',
                'cited_journal_cover' => 'required|file|mimes:pdf|max:20480',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Generate unique request code
            $requestCode = 'CITE-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $userId = Auth::id();
            $uploadPath = "requests/{$userId}/{$requestCode}";

            // Store PDF files in per-request folder
            $pdfPaths = [];
            $fields = [
                'recommendation_letter',
                'citing_article',
                'citing_journal_cover',
                'cited_article',
                'cited_journal_cover',
            ];
            foreach ($fields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $storedPath = $file->storeAs($uploadPath, $file->getClientOriginalName(), 'public');
                    // Remove leading 'public/' if present
                    $cleanPath = preg_replace('/^public\//', '', $storedPath);
                    $pdfPaths[$field] = [
                        'path' => $cleanPath,
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
            }

            // Generate DOCX files
            $docxPaths = [];
            try {
                $incentivePath = $this->generateCitationIncentiveDocxFromHtml($request->all(), $uploadPath);
                $docxPaths['incentive_application'] = preg_replace('/^public\//', '', $incentivePath);
            } catch (\Exception $e) {
                Log::error('Error generating incentive DOCX: ' . $e->getMessage());
            }
            try {
                $recommendationPath = $this->generateCitationRecommendationDocxFromHtml($request->all(), $uploadPath);
                $docxPaths['recommendation_letter'] = preg_replace('/^public\//', '', $recommendationPath);
            } catch (\Exception $e) {
                Log::error('Error generating recommendation DOCX: ' . $e->getMessage());
            }

            // Create the request record
            $userRequest = new UserRequest();
            $userRequest->user_id = $userId;
            $userRequest->request_code = $requestCode;
            $userRequest->type = 'Citation';
            $userRequest->status = 'pending';
            $userRequest->requested_at = now();
            $userRequest->pdf_path = json_encode(['pdfs' => $pdfPaths, 'docxs' => $docxPaths]);
            $userRequest->form_data = json_encode($request->except(['_token', ...$fields]));
            $userRequest->save();

            Log::info('Citation request submitted successfully', [
                'request_code' => $requestCode,
                'user_id' => $userId,
                'pdf_count' => count($pdfPaths),
                'docx_count' => count($docxPaths)
            ]);

            // Send email notifications (unchanged)
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

            return redirect()->route('citations.request')->with('success', 'Citation request submitted successfully! Request Code: ' . $requestCode);
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
} 