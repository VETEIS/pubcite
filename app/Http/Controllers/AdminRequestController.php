<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403);
        }
        $query = \App\Models\Request::with('user')->orderByDesc('requested_at');
        $status = request('status');
        $search = request('search');
        $type = request('type');
        $period = request('period');
        $now = now();
        $rangeDescription = '';
        if ($status && in_array($status, ['pending', 'endorsed', 'rejected'])) {
            $query->where('status', $status);
        }
        if ($type && in_array($type, ['Publication', 'Citation'])) {
            $query->where('type', $type);
        }
        if ($period) {
            if ($period === 'week') {
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $query->whereBetween('requested_at', [$start, $end]);
                $rangeDescription = 'This week: ' . $start->format('M j') . ' – ' . $end->format('M j');
            } elseif ($period === 'month') {
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $query->whereBetween('requested_at', [$start, $end]);
                $rangeDescription = 'This month: ' . $start->format('M j') . ' – ' . $end->format('M j');
            } elseif ($period === 'quarter') {
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                $query->whereBetween('requested_at', [$start, $end]);
                $rangeDescription = 'This quarter: ' . $start->format('M j') . ' – ' . $end->format('M j');
            }
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                if (config('database.default') === 'pgsql') {
                    $q->where('request_code', 'ilike', "%$search%")
                      ->orWhere('type', 'ilike', "%$search%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'ilike', "%$search%")
                             ->orWhere('email', 'ilike', "%$search%") ;
                      });
                } else {
                    $q->where('request_code', 'like', "%$search%")
                      ->orWhere('type', 'like', "%$search%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%$search%")
                             ->orWhere('email', 'like', "%$search%") ;
                      });
                }
            });
        }
        $requests = $query->paginate(15)->withQueryString();
        $stats = [
            'publication' => [
                'week' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                'month' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                'quarter' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
            ],
            'citation' => [
                'week' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                'month' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                'quarter' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
            ],
        ];
        $statusQuery = \App\Models\Request::query();
        if ($type && in_array($type, ['Publication', 'Citation'])) {
            $statusQuery->where('type', $type);
        }
        if ($period) {
            if ($period === 'week') {
                $statusQuery->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            } elseif ($period === 'month') {
                $statusQuery->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            } elseif ($period === 'quarter') {
                $statusQuery->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
            }
        }
        $filterCounts = [
            'pending' => $statusQuery->where('status', 'pending')->count(),
            'endorsed' => $statusQuery->where('status', 'endorsed')->count(),
            'rejected' => $statusQuery->where('status', 'rejected')->count(),
        ];
        return view('admin.requests', compact('requests', 'stats', 'status', 'search', 'filterCounts', 'type', 'period', 'rangeDescription'));
    }

    public function getRequestData($requestId)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403);
        }

        try {
            $request = \App\Models\Request::with('user')->findOrFail($requestId);
            
            // Get all files from the request directory
            $requestFiles = $this->getRequestFiles($request);
            
            // Extract signatory information
            $signatories = $this->extractSignatories($request->form_data);
            
            return response()->json([
                'id' => $request->id,
                'request_code' => $request->request_code,
                'type' => $request->type,
                'status' => $request->status,
                'requested_at' => $request->requested_at,
                'form_data' => $request->form_data,
                'pdf_path' => $request->pdf_path,
                'docx_path' => $request->docx_path,
                'user' => $request->user ? [
                    'name' => $request->user->name,
                    'email' => $request->user->email,
                ] : null,
                'files' => $requestFiles,
                'signatories' => $signatories,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching request data', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch request data',
                'message' => 'An error occurred while retrieving the request information.'
            ], 500);
        }
    }

    private function getRequestFiles($request)
    {
        $files = [];
        
        try {
            // Parse the pdf_path JSON to get actual file locations
            $pdfPathData = json_decode($request->pdf_path, true);
            
            if (!$pdfPathData) {
                return $files; // Return empty array if no file data
            }
            
            // Handle PDF files
            if (isset($pdfPathData['pdfs']) && is_array($pdfPathData['pdfs'])) {
                foreach ($pdfPathData['pdfs'] as $key => $fileInfo) {
                    if (isset($fileInfo['path']) && is_string($fileInfo['path'])) {
                        $fullPath = storage_path('app/public/' . $fileInfo['path']);
                        if (file_exists($fullPath) && is_readable($fullPath)) {
                            $files[] = [
                                'name' => $fileInfo['original_name'] ?? ucfirst(str_replace('_', ' ', $key)) . '.pdf',
                                'path' => $fileInfo['path'],
                                'type' => 'pdf',
                                'size' => $this->formatFileSize(filesize($fullPath)),
                                'full_path' => $fullPath,
                                'key' => $key
                            ];
                        }
                    }
                }
            }
            
            // Handle DOCX files
            if (isset($pdfPathData['docxs']) && is_array($pdfPathData['docxs'])) {
                foreach ($pdfPathData['docxs'] as $key => $docxPath) {
                    if ($docxPath && is_string($docxPath)) {
                        // Try both public and private disk locations
                        $fullPathPublic = storage_path('app/public/' . $docxPath);
                        $fullPathPrivate = storage_path('app/' . $docxPath);
                        
                        if (file_exists($fullPathPublic) && is_readable($fullPathPublic)) {
                            $files[] = [
                                'name' => ucfirst(str_replace('_', ' ', $key)) . '_Form.docx',
                                'path' => $docxPath,
                                'type' => 'docx',
                                'size' => $this->formatFileSize(filesize($fullPathPublic)),
                                'full_path' => $fullPathPublic,
                                'key' => $key
                            ];
                        } elseif (file_exists($fullPathPrivate) && is_readable($fullPathPrivate)) {
                            $files[] = [
                                'name' => ucfirst(str_replace('_', ' ', $key)) . '_Form.docx',
                                'path' => $docxPath,
                                'type' => 'docx',
                                'size' => $this->formatFileSize(filesize($fullPathPrivate)),
                                'full_path' => $fullPathPrivate,
                                'key' => $key
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error processing request files', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return $files;
    }

    private function extractSignatories($formData)
    {
        if (!$formData || !is_array($formData)) {
            return [];
        }

        $exclude = ['applicant_name', 'user_name', 'submitted_by', 'requester_name'];
        $signatories = [];

        foreach ($formData as $key => $value) {
            if (!$value || trim($value) === '') continue;
            $lowerKey = strtolower($key);
            if (strpos($lowerKey, 'name') !== false && !in_array($lowerKey, $exclude)) {
                $signatories[] = [
                    'field' => $this->formatFieldName($key),
                    'name' => trim($value)
                ];
            }
        }
        return $signatories;
    }

    private function determineRole($fieldName)
    {
        $lowerField = strtolower($fieldName);
        
        if (strpos($lowerField, 'dean') !== false) {
            return 'Dean';
        } elseif (strpos($lowerField, 'faculty') !== false) {
            return 'Faculty';
        } elseif (strpos($lowerField, 'manager') !== false) {
            return 'Manager';
        } elseif (strpos($lowerField, 'head') !== false) {
            return 'Department Head';
        } else {
            return 'Signatory';
        }
    }

    private function formatFieldName($key)
    {
        if (!is_string($key)) return 'Unknown Field';
        return ucwords(str_replace('_', ' ', $key));
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function downloadZip($requestId)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403);
        }

        try {
            $request = \App\Models\Request::findOrFail($requestId);
            
            // Parse the pdf_path JSON to get actual file locations
            $pdfPathData = json_decode($request->pdf_path, true);
            
            if (!$pdfPathData) {
                abort(404, 'No files found for this request');
            }

            $zipName = 'request-' . $request->request_code . '-' . date('Y-m-d') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipName);
            
            // Create temp directory if it doesn't exist
            if (!is_dir(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                abort(500, 'Could not create ZIP file');
            }

            $addedFiles = 0;
            $errors = [];

            // Add PDF files
            if (isset($pdfPathData['pdfs']) && is_array($pdfPathData['pdfs'])) {
                foreach ($pdfPathData['pdfs'] as $key => $fileInfo) {
                    if (isset($fileInfo['path']) && is_string($fileInfo['path'])) {
                        $fullPath = storage_path('app/public/' . $fileInfo['path']);
                        if (file_exists($fullPath) && is_readable($fullPath)) {
                            $fileName = $fileInfo['original_name'] ?? ucfirst(str_replace('_', ' ', $key)) . '.pdf';
                            if ($zip->addFile($fullPath, 'PDFs/' . $fileName)) {
                                $addedFiles++;
                            } else {
                                $errors[] = "Failed to add PDF file: {$fileName}";
                            }
                        } else {
                            $errors[] = "PDF file not found or not readable: {$fileInfo['path']}";
                        }
                    }
                }
            }

            // Add DOCX files
            if (isset($pdfPathData['docxs']) && is_array($pdfPathData['docxs'])) {
                foreach ($pdfPathData['docxs'] as $key => $docxPath) {
                    if ($docxPath && is_string($docxPath)) {
                        // Try both public and private disk locations
                        $fullPathPublic = storage_path('app/public/' . $docxPath);
                        $fullPathPrivate = storage_path('app/' . $docxPath);
                        
                        $fullPath = null;
                        if (file_exists($fullPathPublic) && is_readable($fullPathPublic)) {
                            $fullPath = $fullPathPublic;
                        } elseif (file_exists($fullPathPrivate) && is_readable($fullPathPrivate)) {
                            $fullPath = $fullPathPrivate;
                        }
                        
                        if ($fullPath) {
                            $fileName = ucfirst(str_replace('_', ' ', $key)) . '_Form.docx';
                            if ($zip->addFile($fullPath, 'DOCXs/' . $fileName)) {
                                $addedFiles++;
                            } else {
                                $errors[] = "Failed to add DOCX file: {$fileName}";
                            }
                        } else {
                            $errors[] = "DOCX file not found or not readable: {$docxPath}";
                        }
                    }
                }
            }

            $zip->close();

            if ($addedFiles === 0) {
                // Log errors for debugging
                if (!empty($errors)) {
                    \Illuminate\Support\Facades\Log::warning('ZIP download failed - no files added', [
                        'request_id' => $requestId,
                        'errors' => $errors
                    ]);
                }
                abort(404, 'No files found to include in ZIP');
            }

            // Log successful ZIP creation
            \Illuminate\Support\Facades\Log::info('ZIP download created successfully', [
                'request_id' => $requestId,
                'files_added' => $addedFiles,
                'zip_path' => $zipPath
            ]);

            return response()->download($zipPath)->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating ZIP download', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Failed to create ZIP file: ' . $e->getMessage());
        }
    }
} 