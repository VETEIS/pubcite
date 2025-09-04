<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            
            $requestFiles = $this->getRequestFiles($request);
            
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
            $pdfPathData = json_decode($request->pdf_path, true);
            
            if (!$pdfPathData) {
                return $files;
            }
            
            if (isset($pdfPathData['pdfs']) && is_array($pdfPathData['pdfs'])) {
                foreach ($pdfPathData['pdfs'] as $key => $fileInfo) {
                    if (isset($fileInfo['path']) && is_string($fileInfo['path'])) {
                        $fullPath = Storage::disk('local')->path($fileInfo['path']);
                        if (!file_exists($fullPath)) {
                            $fullPath = storage_path('app/public/' . $fileInfo['path']);
                        }
                        
                        if (file_exists($fullPath) && is_readable($fullPath)) {
                            $secureFilename = base64_encode($request->id . '|' . ($fileInfo['original_name'] ?? ucfirst(str_replace('_', ' ', $key)) . '.pdf'));
                            $downloadUrl = route('admin.download.file', ['type' => 'pdf', 'filename' => $secureFilename]);
                            
                            $files[] = [
                                'name' => $fileInfo['original_name'] ?? ucfirst(str_replace('_', ' ', $key)) . '.pdf',
                                'path' => $fileInfo['path'],
                                'type' => 'pdf',
                                'size' => $this->formatFileSize(filesize($fullPath)),
                                'full_path' => $fullPath,
                                'key' => $key,
                                'download_url' => $downloadUrl
                            ];
                        }
                    }
                }
            }
            
            if (isset($pdfPathData['docxs']) && is_array($pdfPathData['docxs'])) {
                foreach ($pdfPathData['docxs'] as $key => $docxPath) {
                    if (!$docxPath || !is_string($docxPath)) continue;

                    $fullPath = null;
                    $isAbsolute = preg_match('/^(?:[A-Za-z]:\\\\|\\\\\\\\|\/)/', $docxPath) === 1 || str_starts_with($docxPath, storage_path());

                    if ($isAbsolute) {
                        $fullPath = $docxPath;
                    } else {
                        $fullPath = Storage::disk('local')->path($docxPath);
                        if (!file_exists($fullPath)) {
                            $fullPath = storage_path('app/public/' . $docxPath);
                        }
                    }

                    if ($fullPath && file_exists($fullPath) && is_readable($fullPath)) {
                        $fileName = $this->getDocxFileName($key);
                        $secureFilename = base64_encode($request->id . '|' . $fileName);
                        $downloadUrl = route('admin.download.file', ['type' => 'docx', 'filename' => $secureFilename]);
                        
                        $files[] = [
                            'name' => $fileName,
                            'path' => $docxPath,
                            'type' => 'docx',
                            'size' => $this->formatFileSize(filesize($fullPath)),
                            'full_path' => $fullPath,
                            'key' => $key,
                            'download_url' => $downloadUrl
                        ];
                    }
                }
            }
            
            if ($request->signed_document_path && Storage::disk('local')->exists($request->signed_document_path)) {
                $fullPath = Storage::disk('local')->path($request->signed_document_path);
                if (file_exists($fullPath) && is_readable($fullPath)) {
                    $secureFilename = base64_encode($request->id . '|' . basename($request->signed_document_path));
                    $downloadUrl = route('admin.download.file', ['type' => 'signed', 'filename' => $secureFilename]);
                    
                    $files[] = [
                        'name' => 'Signed Document - ' . basename($request->signed_document_path),
                        'path' => $request->signed_document_path,
                        'type' => 'signed',
                        'size' => $this->formatFileSize(filesize($fullPath)),
                        'full_path' => $fullPath,
                        'key' => 'signed',
                        'download_url' => $downloadUrl
                    ];
                }
            }
            
            if ($request->original_document_path && Storage::disk('local')->exists($request->original_document_path)) {
                $fullPath = Storage::disk('local')->path($request->original_document_path);
                if (file_exists($fullPath) && is_readable($fullPath)) {
                    $secureFilename = base64_encode($request->id . '|' . basename($request->original_document_path));
                    $downloadUrl = route('admin.download.file', ['type' => 'backup', 'filename' => $secureFilename]);
                    
                    $files[] = [
                        'name' => 'Original Document - ' . basename($request->original_document_path),
                        'path' => $request->original_document_path,
                        'type' => 'backup',
                        'size' => $this->formatFileSize(filesize($fullPath)),
                        'full_path' => $fullPath,
                        'key' => 'backup',
                        'download_url' => $downloadUrl
                    ];
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

    private function getDocxFileName($key): string
    {
        switch ($key) {
            case 'incentive':
                return 'Incentive_Application_Form.docx';
            case 'recommendation':
                return 'Recommendation_Letter_Form.docx';
            case 'terminal':
                return 'Terminal_Report_Form.docx';
            default:
                return ucfirst(str_replace('_', ' ', (is_string($key) ? $key : 'document'))) . '.docx';
        }
    }

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
                if (!empty($errors)) {
                    \Illuminate\Support\Facades\Log::warning('ZIP download failed - no files added', [
                        'request_id' => $requestId,
                        'errors' => $errors
                    ]);
                }
                abort(404, 'No files found to include in ZIP');
            }

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