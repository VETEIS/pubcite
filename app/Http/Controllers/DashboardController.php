<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
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
                    'year' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])->count(),
                ],
                'citation' => [
                    'week' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                    'month' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                    'quarter' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
                    'year' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])->count(),
                ],
            ];
            // Calculate status counts based on current filters
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

            // --- Real Chart Data Aggregation ---
            // Only use type and period filters for chart data, ignore status
            $chartType = $type;
            $chartPeriod = $period;
            $chartNow = $now;
            $chartStartMonth = $chartNow->copy()->subMonths(11)->startOfMonth();
            $chartEndMonth = $chartNow->copy()->endOfMonth();
            $chartBaseQuery = \App\Models\Request::query();
            if ($chartType && in_array($chartType, ['Publication', 'Citation'])) {
                $chartBaseQuery->where('type', $chartType);
            }
            if ($chartPeriod) {
                if ($chartPeriod === 'week') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfWeek(), $chartNow->copy()->endOfWeek()]);
                } elseif ($chartPeriod === 'month') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfMonth(), $chartNow->copy()->endOfMonth()]);
                } elseif ($chartPeriod === 'quarter') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfQuarter(), $chartNow->copy()->endOfQuarter()]);
                }
            } else {
                $chartBaseQuery->whereBetween('requested_at', [$chartStartMonth, $chartEndMonth]);
            }
            $driver = config('database.default');
            if ($driver === 'pgsql') {
                $monthExpr = "TO_CHAR(requested_at, 'YYYY-MM')";
            } else {
                $monthExpr = "DATE_FORMAT(requested_at, '%Y-%m')";
            }
            $rawCounts = \App\Models\Request::selectRaw("type, $monthExpr as month, COUNT(*) as count")
                ->whereBetween('requested_at', [$chartStartMonth, $chartEndMonth])
                ->when($chartType && in_array($chartType, ['Publication', 'Citation']), function($q) use ($chartType) {
                    $q->where('type', $chartType);
                })
                ->groupBy('type', 'month')
                ->orderBy('month')
                ->get();
            $months = [];
            for ($i = 0; $i < 12; $i++) {
                $months[] = $chartNow->copy()->subMonths(11 - $i)->format('Y-m');
            }
            $monthlyCounts = [
                'Publication' => array_fill_keys($months, 0),
                'Citation' => array_fill_keys($months, 0),
            ];
            foreach ($rawCounts as $row) {
                $monthlyCounts[$row->type][$row->month] = $row->count;
            }
            // Status counts for current type/period filter (not status)
            $statusCounts = [
                'pending' => 0,
                'endorsed' => 0,
                'rejected' => 0,
            ];
            $statusRaw = $chartBaseQuery->selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
            foreach ($statusRaw as $row) {
                $statusCounts[$row->status] = $row->count;
            }
            // --- End Real Chart Data Aggregation ---

            // Fetch 5 most recent applications for sidebar
            $recentApplications = \App\Models\Request::with('user')->orderByDesc('requested_at')->limit(5)->get();
            // Fetch latest 10 activity logs for Activity Log card
            $activityLogs = \App\Models\ActivityLog::with('user')->orderByDesc('created_at')->limit(10)->get();
            return view('admin.dashboard', compact('requests', 'stats', 'status', 'search', 'filterCounts', 'type', 'period', 'rangeDescription', 'recentApplications', 'monthlyCounts', 'statusCounts', 'months', 'activityLogs'));
        }
        $requests = \App\Models\Request::where('user_id', $user->id)->orderByDesc('requested_at')->get();
        return view('dashboard', compact('requests'));
    }

    public function getData()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $query = \App\Models\Request::with('user')->orderByDesc('requested_at');
        $status = request('status');
        $search = request('search');
        $type = request('type');
        $period = request('period');
        $now = now();
        if ($status && in_array($status, ['pending', 'endorsed', 'rejected'])) {
            $query->where('status', $status);
        }
        if ($type && in_array($type, ['Publication', 'Citation'])) {
            $query->where('type', $type);
        }
        if ($period) {
            if ($period === 'week') {
                $query->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            } elseif ($period === 'quarter') {
                $query->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
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
        $allRequests = $query->get();
        // Add file/document data for each request
        $requestsWithFiles = $allRequests->map(function($req) {
            $files = [
                'pdfs' => [],
                'docx' => [],
            ];
            // Extract from pdf_path JSON if present
            if (!empty($req->pdf_path)) {
                $pdfPathData = json_decode($req->pdf_path, true);
                // PDFs
                if (!empty($pdfPathData['pdfs']) && is_array($pdfPathData['pdfs'])) {
                    foreach ($pdfPathData['pdfs'] as $key => $file) {
                        $files['pdfs'][] = [
                            'name' => ucfirst(str_replace('_', ' ', $key)),
                            'file_name' => $file['original_name'] ?? basename($file['path'] ?? ''),
                            'path' => isset($file['path']) ? asset('storage/' . $file['path']) : null,
                            'missing' => empty($file['path']),
                        ];
                    }
                }
                // DOCX
                if (!empty($pdfPathData['docxs']) && is_array($pdfPathData['docxs'])) {
                    foreach ($pdfPathData['docxs'] as $key => $path) {
                        $files['docx'][] = [
                            'name' => ucfirst($key),
                            'file_name' => ucfirst($key) . '_Form.docx',
                            'path' => $path ? asset('storage/' . $path) : null,
                            'missing' => empty($path),
                        ];
                    }
                }
            }
            $arr = $req->toArray();
            $arr['files'] = $files;
            return $arr;
        });
        $stats = [
            'publication' => [
                'week' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                'month' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                'quarter' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
                'year' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])->count(),
            ],
            'citation' => [
                'week' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                'month' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                'quarter' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
                'year' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])->count(),
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
        // --- Real Chart Data Aggregation ---
        // Only use type and period filters for chart data, ignore status
        $chartType = $type;
        $chartPeriod = $period;
        $chartNow = $now;
        $chartStartMonth = $chartNow->copy()->subMonths(11)->startOfMonth();
        $chartEndMonth = $chartNow->copy()->endOfMonth();
        $chartBaseQuery = \App\Models\Request::query();
        if ($chartType && in_array($chartType, ['Publication', 'Citation'])) {
            $chartBaseQuery->where('type', $chartType);
        }
        if ($chartPeriod) {
            if ($chartPeriod === 'week') {
                $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfWeek(), $chartNow->copy()->endOfWeek()]);
            } elseif ($chartPeriod === 'month') {
                $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfMonth(), $chartNow->copy()->endOfMonth()]);
            } elseif ($chartPeriod === 'quarter') {
                $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfQuarter(), $chartNow->copy()->endOfQuarter()]);
            }
        } else {
            $chartBaseQuery->whereBetween('requested_at', [$chartStartMonth, $chartEndMonth]);
        }
        $driver = config('database.default');
        if ($driver === 'pgsql') {
            $monthExpr = "TO_CHAR(requested_at, 'YYYY-MM')";
        } else {
            $monthExpr = "DATE_FORMAT(requested_at, '%Y-%m')";
        }
        $rawCounts = \App\Models\Request::selectRaw("type, $monthExpr as month, COUNT(*) as count")
            ->whereBetween('requested_at', [$chartStartMonth, $chartEndMonth])
            ->when($chartType && in_array($chartType, ['Publication', 'Citation']), function($q) use ($chartType) {
                $q->where('type', $chartType);
            })
            ->groupBy('type', 'month')
            ->orderBy('month')
            ->get();
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $months[] = $chartNow->copy()->subMonths(11 - $i)->format('Y-m');
        }
        $monthlyCounts = [
            'Publication' => array_fill_keys($months, 0),
            'Citation' => array_fill_keys($months, 0),
        ];
        foreach ($rawCounts as $row) {
            $monthlyCounts[$row->type][$row->month] = $row->count;
        }
        // Status counts for current type/period filter (not status)
        $statusCounts = [
            'pending' => 0,
            'endorsed' => 0,
            'rejected' => 0,
        ];
        $statusRaw = $chartBaseQuery->selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
        foreach ($statusRaw as $row) {
            $statusCounts[$row->status] = $row->count;
        }
        // --- End Real Chart Data Aggregation ---
        return response()->json([
            'months' => $months,
            'monthlyCounts' => $monthlyCounts,
            'statusCounts' => $statusCounts,
            'type' => $type,
        ]);
    }

    public function streamUpdates()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Set headers for Server-Sent Events
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');

        // Keep connection alive and send updates
        $lastCheck = now();
        $maxExecutionTime = 300; // 5 minutes max
        $startTime = time();
        $lastUpdateTime = null;
        
        while (true) {
            // Check if we've exceeded max execution time
            if (time() - $startTime > $maxExecutionTime) {
                echo "data: " . json_encode(['type' => 'timeout']) . "\n\n";
                break;
            }
            
            // Check for new requests or status changes
            try {
                $newRequests = \App\Models\Request::where('created_at', '>', $lastCheck)->count();
                $statusChanges = \App\Models\ActivityLog::where('created_at', '>', $lastCheck)
                    ->whereIn('action', ['created', 'status_changed', 'deleted'])
                    ->count();
                
                // Only send update if there are actual changes AND we haven't sent an update in the last 5 seconds
                if (($newRequests > 0 || $statusChanges > 0) && 
                    (!$lastUpdateTime || now()->diffInSeconds($lastUpdateTime) > 5)) {
                    
                    // Get updated data
                    $now = now();
                    $stats = [
                        'publication' => [
                            'week' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                            'month' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                            'quarter' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
                            'year' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])->count(),
                        ],
                        'citation' => [
                            'week' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                            'month' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
                            'quarter' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])->count(),
                            'year' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])->count(),
                        ],
                    ];
                    
                    $recentApplications = \App\Models\Request::with('user')->orderByDesc('requested_at')->limit(5)->get();
                    $activityLogs = \App\Models\ActivityLog::with('user')->orderByDesc('created_at')->limit(10)->get();
                    
                    $updateData = [
                        'stats' => $stats,
                        'recentApplications' => $recentApplications,
                        'activityLogs' => $activityLogs,
                        'timestamp' => now()->toISOString(),
                        'hasChanges' => true,
                    ];
                    
                    echo "data: " . json_encode($updateData) . "\n\n";
                    ob_flush();
                    flush();
                    
                    $lastUpdateTime = now();
                }
                
                $lastCheck = now();
                
            } catch (\Exception $e) {
                // Log error and send error message
                \Illuminate\Support\Facades\Log::error('SSE Stream Error: ' . $e->getMessage());
                echo "data: " . json_encode(['error' => 'Database error']) . "\n\n";
                break;
            }
            
            // Sleep for 2 seconds before next check
            sleep(2);
            
            // Check if connection is still alive
            if (connection_aborted()) {
                break;
            }
        }
        
        exit();
    }
} 