<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\NudgeNotification;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $user = User::find($user->id);
        
        Log::info('Dashboard access', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);
        
        if ($user->role === 'admin') {
            Log::info('Redirecting admin user to admin dashboard', ['user_id' => $user->id]);
            return redirect()->route('admin.dashboard');
        }
        
        Log::info('Routing to user dashboard', ['user_id' => $user->id]);
        $requests = \App\Models\Request::where('user_id', $user->id)
            ->where('status', '!=', 'draft') // Exclude drafts from user dashboard
            ->orderByDesc('requested_at')->get();
        $citations_request_enabled = \App\Models\Setting::get('citations_request_enabled', '1');
        return view('dashboard', compact('requests', 'citations_request_enabled'));
    }

    public function adminDashboard()
    {
        $user = Auth::user();
        
        $user = User::find($user->id);
        
        Log::info('Admin dashboard access', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);
        
        if ($user->role !== 'admin') {
            Log::info('Non-admin user attempted to access admin dashboard', ['user_id' => $user->id]);
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        
        Log::info('Routing to admin dashboard', ['user_id' => $user->id]);
        
        try {
            $query = \App\Models\Request::with('user');
            $status = request('status');
            $search = request('search');
            $type = request('type');
            $period = request('period');
            $sortBy = request('sort', 'requested_at');
            $sortOrder = request('order', 'desc');
            $now = now();
            $rangeDescription = '';
            
            // Exclude drafts from dashboard by default - only show submitted requests
            $query->where('status', '!=', 'draft');
            
            // Include both pending (in workflow) and completed requests
            // No workflow_state filter - show all non-draft requests
            
            if (in_array($sortBy, ['requested_at', 'request_code', 'type', 'status'])) {
                if ($sortBy === 'requested_at') {
                    $query->orderBy('requested_at', $sortOrder);
                } elseif ($sortBy === 'request_code') {
                    $query->orderBy('request_code', $sortOrder);
                } elseif ($sortBy === 'type') {
                    $query->orderBy('type', $sortOrder);
                } elseif ($sortBy === 'status') {
                    $query->orderBy('status', $sortOrder);
                }
            } else {
                $query->orderByDesc('requested_at');
            }
            
            if ($status && in_array($status, ['pending', 'endorsed', 'rejected'])) {
                $query->where('status', $status);
            }
            if ($type && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations'])) {
                $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                $query->where('type', $dbType);
            }
            if ($period) {
                if ($period === 'week' || $period === 'This Week') {
                    $start = $now->copy()->startOfWeek();
                    $end = $now->copy()->endOfWeek();
                    $query->whereBetween('requested_at', [$start, $end]);
                    $rangeDescription = 'This week: ' . $start->format('M j') . ' – ' . $end->format('M j');
                } elseif ($period === 'month' || $period === 'This Month') {
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                    $query->whereBetween('requested_at', [$start, $end]);
                    $rangeDescription = 'This month: ' . $start->format('M j') . ' – ' . $end->format('M j');
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $start = $now->copy()->startOfQuarter();
                    $end = $now->copy()->endOfQuarter();
                    $query->whereBetween('requested_at', [$start, $end]);
                    $rangeDescription = 'This quarter: ' . $start->format('M j') . ' – ' . $end->format('M j');
                } elseif ($period === 'year' || $period === 'This Year') {
                    $start = $now->copy()->startOfYear();
                    $end = $now->copy()->endOfYear();
                    $query->whereBetween('requested_at', [$start, $end]);
                    $rangeDescription = 'This year: ' . $start->format('M j') . ' – ' . $end->format('M j');
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
            $requests = $query->paginate(10)->withQueryString();
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
            // Calculate filter counts (include both pending and completed requests)
            $statusQuery = \App\Models\Request::query()->where('status', '!=', 'draft');
            if ($type && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations'])) {
                $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                $statusQuery->where('type', $dbType);
            }
            if ($period) {
                if ($period === 'week' || $period === 'This Week') {
                    $statusQuery->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($period === 'month' || $period === 'This Month') {
                    $statusQuery->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $statusQuery->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
                } elseif ($period === 'year' || $period === 'This Year') {
                    $statusQuery->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                }
            }
            // Calculate filter counts (include both pending and completed requests)
            // Pending = requests still in workflow (workflow_state != 'completed')
            // Endorsed = requests that completed workflow (status = 'endorsed' AND workflow_state = 'completed')
            // Rejected = requests with status = 'rejected'
            $filterCounts = [
                'pending' => (clone $statusQuery)->where('workflow_state', '!=', 'completed')->count(),
                'endorsed' => (clone $statusQuery)->where('status', 'endorsed')->where('workflow_state', 'completed')->count(),
                'rejected' => (clone $statusQuery)->where('status', 'rejected')->count(),
            ];
            // Chart Data Aggregation
            $chartType = $type;
            $chartPeriod = $period;
            $chartNow = $now;
            $chartStartMonth = $chartNow->copy()->subMonths(11)->startOfMonth();
            $chartEndMonth = $chartNow->copy()->endOfMonth();
            // Include both pending and completed requests in charts
            $chartBaseQuery = \App\Models\Request::query()->where('status', '!=', 'draft');
            if ($chartType && in_array($chartType, ['Publication', 'Citation', 'Publications', 'Citations'])) {
                $dbType = $chartType === 'Publications' ? 'Publication' : ($chartType === 'Citations' ? 'Citation' : $chartType);
                $chartBaseQuery->where('type', $dbType);
            }
            if ($chartPeriod) {
                if ($chartPeriod === 'week' || $chartPeriod === 'This Week') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfWeek(), $chartNow->copy()->endOfWeek()]);
                } elseif ($chartPeriod === 'month' || $chartPeriod === 'This Month') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfMonth(), $chartNow->copy()->endOfMonth()]);
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfQuarter(), $chartNow->copy()->endOfQuarter()]);
                } elseif ($period === 'year' || $period === 'This Year') {
                    $chartBaseQuery->whereBetween('requested_at', [$chartNow->copy()->startOfYear(), $chartNow->copy()->endOfYear()]);
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
                ->where('status', '!=', 'draft') // Exclude drafts from chart data
                // Include both pending and completed requests
                ->when($type && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations']), function($q) use ($type) {
                    $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                    $q->where('type', $dbType);
                })
                ->when($period, function($q) use ($period, $now) {
                    if ($period === 'week' || $period === 'This Week') {
                        $q->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    } elseif ($period === 'month' || $period === 'This Month') {
                        $q->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                    } elseif ($period === 'quarter' || $period === 'This Quarter') {
                        $q->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
                    } elseif ($period === 'year' || $period === 'This Year') {
                        $q->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                    }
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($sq) use ($search) {
                        if (config('database.default') === 'pgsql') {
                            $sq->where('request_code', 'ilike', "%$search%")
                              ->orWhere('type', 'ilike', "%$search%")
                              ->orWhereHas('user', function($uq) use ($search) {
                                  $uq->where('name', 'ilike', "%$search%")
                                     ->orWhere('email', 'ilike', "%$search%") ;
                              });
                        } else {
                            $sq->where('request_code', 'like', "%$search%")
                              ->orWhere('type', 'like', "%$search%")
                              ->orWhereHas('user', function($uq) use ($search) {
                                  $uq->where('name', 'like', "%$search%")
                                     ->orWhere('email', 'like', "%$search%") ;
                              });
                        }
                    });
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
            
            // Calculate status counts from the same filtered dataset as the table
            // Use the same query logic as the table to ensure consistency
            $statusCountsQuery = \App\Models\Request::query()->where('status', '!=', 'draft');
            if ($type && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations'])) {
                $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                $statusCountsQuery->where('type', $dbType);
            }
            if ($period) {
                if ($period === 'week' || $period === 'This Week') {
                    $statusCountsQuery->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($period === 'month' || $period === 'This Month') {
                    $statusCountsQuery->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $statusCountsQuery->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
                } elseif ($period === 'year' || $period === 'This Year') {
                    $statusCountsQuery->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                }
            }
            if ($status && in_array($status, ['pending', 'endorsed', 'rejected'])) {
                // Apply the same status filter logic as the table
                if ($status === 'pending') {
                    $statusCountsQuery->where('workflow_state', '!=', 'completed');
                } elseif ($status === 'endorsed') {
                    $statusCountsQuery->where('status', 'endorsed')->where('workflow_state', 'completed');
                } else {
                    $statusCountsQuery->where('status', $status);
                }
            }
            
            // Calculate status counts using the same logic as the table
            $statusCounts = [
                'pending' => (clone $statusCountsQuery)->where('workflow_state', '!=', 'completed')->count(),
                'endorsed' => (clone $statusCountsQuery)->where('status', 'endorsed')->where('workflow_state', 'completed')->count(),
                'rejected' => (clone $statusCountsQuery)->where('status', 'rejected')->count(),
            ];
            $recentApplications = \App\Models\Request::with('user')->where('status', '!=', 'draft')->orderByDesc('requested_at')->limit(5)->get();
            $activityLogs = \App\Models\ActivityLog::with('user')
                ->whereNotIn('action', ['created']) // Exclude submission requests
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
            
            // Extract college data with academic rank breakdown from requests
            $collegeCounts = [];
            $collegeRankBreakdown = [];
            $allRequestsForCollege = \App\Models\Request::where('status', '!=', 'draft')->get();
            foreach ($allRequestsForCollege as $req) {
                $formData = is_string($req->form_data) ? json_decode($req->form_data, true) : $req->form_data;
                if (is_array($formData) && isset($formData['college']) && !empty($formData['college'])) {
                    $college = $formData['college'];
                    $academicRank = $formData['academicrank'] ?? $formData['rank'] ?? 'Unknown';
                    
                    // Count total per college
                    $collegeCounts[$college] = ($collegeCounts[$college] ?? 0) + 1;
                    
                    // Count per college and academic rank
                    if (!isset($collegeRankBreakdown[$college])) {
                        $collegeRankBreakdown[$college] = [];
                    }
                    $collegeRankBreakdown[$college][$academicRank] = ($collegeRankBreakdown[$college][$academicRank] ?? 0) + 1;
                }
            }
            arsort($collegeCounts); // Sort by count descending
            
            return view('admin.dashboard', compact('requests', 'stats', 'status', 'search', 'filterCounts', 'type', 'period', 'rangeDescription', 'recentApplications', 'monthlyCounts', 'statusCounts', 'months', 'activityLogs', 'collegeCounts', 'collegeRankBreakdown'));
        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->view('errors.500', [], 500);
        }
    }

    public function nudge(\App\Models\Request $request)
    {
        $user = Auth::user();
        if ($request->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if ($request->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be nudged.');
        }
        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'request_id' => $request->id,
                'action' => 'nudged',
                'details' => [
                    'request_code' => $request->request_code,
                    'type' => $request->type,
                    'by_name' => $user->name,
                    'by_email' => $user->email,
                ],
                'created_at' => now(),
            ]);
            
            Log::info('Activity log created successfully for nudge', [
                'request_id' => $request->id,
                'request_code' => $request->request_code,
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create activity log for nudge: ' . $e->getMessage(), [
                'request_id' => $request->id,
                'request_code' => $request->request_code,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        // Determine current stage signatory based on workflow_state
        $workflowState = $request->workflow_state ?? 'pending_research_manager';
        $currentSignatory = null;
        $signatoryEmail = null;
        $signatoryName = null;
        $signatoryUserId = null;
        
        switch ($workflowState) {
            case 'pending_research_manager':
                // Find Center Manager from form data
                $form = is_array($request->form_data) ? $request->form_data : (json_decode($request->form_data ?? '[]', true) ?: []);
                $centerManagerName = $form['center_manager'] ?? $form['center_manager_name'] ?? null;
                if ($centerManagerName) {
                    $currentSignatory = \App\Models\User::where('name', trim($centerManagerName))->where('signatory_type', 'center_manager')->first();
                    if ($currentSignatory) {
                        $signatoryName = trim($centerManagerName);
                        $signatoryEmail = $currentSignatory->email;
                        $signatoryUserId = $currentSignatory->id;
                    }
                }
                break;
                
            case 'pending_faculty':
                // Find the Faculty from form data
                $form = is_array($request->form_data) ? $request->form_data : (json_decode($request->form_data ?? '[]', true) ?: []);
                $facultyName = $form['facultyname'] ?? $form['faculty_name'] ?? $form['rec_faculty_name'] ?? null;
                if ($facultyName) {
                    $currentSignatory = \App\Models\User::where('name', trim($facultyName))->where('signatory_type', 'faculty')->first();
                    if ($currentSignatory) {
                        $signatoryName = trim($facultyName);
                        $signatoryEmail = $currentSignatory->email;
                        $signatoryUserId = $currentSignatory->id;
                    }
                }
                break;
                
            case 'pending_dean':
                // Find the Dean from form data
                $form = is_array($request->form_data) ? $request->form_data : (json_decode($request->form_data ?? '[]', true) ?: []);
                $deanName = $form['collegedean'] ?? $form['college_dean'] ?? $form['dean'] ?? $form['dean_name'] ?? $form['rec_dean_name'] ?? null;
                if ($deanName) {
                    $currentSignatory = \App\Models\User::where('name', trim($deanName))->where('signatory_type', 'college_dean')->first();
                    if ($currentSignatory) {
                        $signatoryName = trim($deanName);
                        $signatoryEmail = $currentSignatory->email;
                        $signatoryUserId = $currentSignatory->id;
                    }
                }
                break;
                
            case 'pending_deputy_director':
                // Get Deputy Director from settings
                $signatoryEmail = \App\Models\Setting::get('deputy_director_email');
                $signatoryName = \App\Models\Setting::get('official_deputy_director_name', 'Deputy Director');
                // Try to find user by email
                if ($signatoryEmail) {
                    $currentSignatory = \App\Models\User::where('email', $signatoryEmail)->first();
                    if ($currentSignatory) {
                        $signatoryUserId = $currentSignatory->id;
                    }
                }
                break;
                
            case 'pending_director':
                // Get Director from settings
                $signatoryEmail = \App\Models\Setting::get('rdd_director_email');
                $signatoryName = \App\Models\Setting::get('official_rdd_director_name', 'RDD Director');
                // Try to find user by email
                if ($signatoryEmail) {
                    $currentSignatory = \App\Models\User::where('email', $signatoryEmail)->first();
                    if ($currentSignatory) {
                        $signatoryUserId = $currentSignatory->id;
                    }
                }
                break;
        }
        
        // Send nudge to current stage signatory
        if ($signatoryEmail) {
            
            try {
                Mail::to($signatoryEmail)->send(new NudgeNotification($request, $user));
                Log::info('Nudge email sent to current stage signatory', [
                    'request_id' => $request->id,
                    'workflow_state' => $workflowState,
                    'signatory_email' => $signatoryEmail,
                    'signatory_name' => $signatoryName
                ]);
            } catch (\Exception $e) {
                Log::error('Nudge email failed: ' . $e->getMessage(), [
                    'request_id' => $request->id,
                    'signatory_email' => $signatoryEmail
                ]);
            }
            
            return back()->with('success', 'Nudge sent to ' . ($signatoryName ?? 'the current stage signatory') . '.');
        } else {
            // Fallback: send to admins if no signatory found
            Log::warning('No current stage signatory found for nudge, falling back to admins', [
                'request_id' => $request->id,
                'workflow_state' => $workflowState
            ]);
            
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new NudgeNotification($request, $user));
                } catch (\Exception $e) {
                    Log::error('Nudge email failed: ' . $e->getMessage());
                }
            }
            return back()->with('success', 'Nudge sent to admins (no signatory found for current stage).');
        }
    }

    public function getData()
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        
            $status = request('status');
            $search = request('search');
            $type = request('type');
            $period = request('period');
            $now = now();
            
            $tableQuery = \App\Models\Request::with('user')->orderByDesc('requested_at');
            
            // Exclude drafts from dashboard by default - only show submitted requests
            $tableQuery->where('status', '!=', 'draft');
            
            // Include both pending (in workflow) and completed requests - match the table display
            // No workflow_state filter - show all non-draft requests
            
            if ($status && $status !== 'null' && in_array($status, ['pending', 'endorsed', 'rejected'])) {
                // For pending: workflow_state != 'completed'
                // For endorsed: status = 'endorsed' AND workflow_state = 'completed'
                // For rejected: status = 'rejected'
                if ($status === 'pending') {
                    $tableQuery->where('workflow_state', '!=', 'completed');
                } elseif ($status === 'endorsed') {
                    $tableQuery->where('status', 'endorsed')->where('workflow_state', 'completed');
                } else {
                    $tableQuery->where('status', $status);
                }
            }
            
            if ($type && $type !== 'null' && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations'])) {
                $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                $tableQuery->where('type', $dbType);
            }
            if ($period && $period !== 'null') {
                if ($period === 'week' || $period === 'This Week') {
                    $tableQuery->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($period === 'month' || $period === 'This Month') {
                    $tableQuery->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $tableQuery->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
                } elseif ($period === 'year' || $period === 'This Year') {
                    $tableQuery->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                }
            }
            
            if ($search && $search !== 'null') {
                $tableQuery->where(function($q) use ($search) {
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
            
            $allRequests = $tableQuery->get();
            
            // Calculate status counts from the same filtered dataset as the table
            // This ensures charts match the table exactly
            $statusCounts = [
                'pending' => $allRequests->where('workflow_state', '!=', 'completed')->count(),
                'endorsed' => $allRequests->where('status', 'endorsed')->where('workflow_state', 'completed')->count(),
                'rejected' => $allRequests->where('status', 'rejected')->count(),
            ];
            
            $driver = config('database.default');
            if ($driver === 'pgsql') {
                $monthExpr = "TO_CHAR(requested_at, 'YYYY-MM')";
            } else {
                $monthExpr = "DATE_FORMAT(requested_at, '%Y-%m')";
            }
            
            $months = [];
            
            if ($period) {
                if ($period === 'week' || $period === 'This Week') {
                    if ($driver === 'pgsql') {
                        $monthExpr = "TO_CHAR(requested_at, 'YYYY-MM-DD')";
                    } else {
                        $monthExpr = "DATE_FORMAT(requested_at, '%Y-%m-%d')";
                    }
                    $chartStart = $now->copy()->startOfWeek();
                    $chartEnd = $now->copy()->endOfWeek();
                    for ($i = 0; $i < 7; $i++) {
                        $months[] = $chartStart->copy()->addDays($i)->format('Y-m-d');
                    }
                } elseif ($period === 'month' || $period === 'This Month') {
                    if ($driver === 'pgsql') {
                        $monthExpr = "CASE 
                            WHEN EXTRACT(DAY FROM requested_at) <= 5 THEN '1-5'
                            WHEN EXTRACT(DAY FROM requested_at) <= 10 THEN '6-10'
                            WHEN EXTRACT(DAY FROM requested_at) <= 15 THEN '11-15'
                            WHEN EXTRACT(DAY FROM requested_at) <= 20 THEN '16-20'
                            WHEN EXTRACT(DAY FROM requested_at) <= 25 THEN '21-25'
                            ELSE '26-31'
                        END";
                    } else {
                        $monthExpr = "CASE 
                            WHEN DAY(requested_at) <= 5 THEN '1-5'
                            WHEN DAY(requested_at) <= 10 THEN '6-10'
                            WHEN DAY(requested_at) <= 15 THEN '11-15'
                            WHEN DAY(requested_at) <= 20 THEN '16-20'
                            WHEN DAY(requested_at) <= 25 THEN '21-25'
                            ELSE '26-31'
                        END";
                    }
                    $chartStart = $now->copy()->startOfMonth();
                    $chartEnd = $now->copy()->endOfMonth();
                    $months = ['1-5', '6-10', '11-15', '16-20', '21-25', '26-31'];
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $chartStart = $now->copy()->startOfQuarter();
                    $chartEnd = $now->copy()->endOfQuarter();
                    for ($i = 0; $i < 3; $i++) {
                        $months[] = $chartStart->copy()->addMonths($i)->format('Y-m');
                    }
                } elseif ($period === 'year' || $period === 'This Year') {
                    $chartStart = $now->copy()->startOfYear();
                    $chartEnd = $now->copy()->endOfYear();
                    for ($i = 0; $i < 12; $i++) {
                        $months[] = $chartStart->copy()->addMonths($i)->format('Y-m');
                    }
                } else {
                    $chartStart = $now->copy()->subMonths(11)->startOfMonth();
                    $chartEnd = $now->copy()->endOfMonth();
                    for ($i = 0; $i < 12; $i++) {
                        $months[] = $now->copy()->subMonths(11 - $i)->format('Y-m');
                    }
                }
            } else {
                $chartStart = $now->copy()->subMonths(11)->startOfMonth();
                $chartEnd = $now->copy()->endOfMonth();
                for ($i = 0; $i < 12; $i++) {
                    $months[] = $now->copy()->subMonths(11 - $i)->format('Y-m');
                }
            }
            
            // Use the same base query as the table to ensure charts match table data
            $chartBaseQuery = \App\Models\Request::query()
                ->where('status', '!=', 'draft'); // Exclude drafts
            
            // Apply the same filters as the table
            if ($type && $type !== 'null' && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations'])) {
                $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                $chartBaseQuery->where('type', $dbType);
            }
            if ($period && $period !== 'null') {
                if ($period === 'week' || $period === 'This Week') {
                    $chartBaseQuery->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($period === 'month' || $period === 'This Month') {
                    $chartBaseQuery->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                } elseif ($period === 'quarter' || $period === 'This Quarter') {
                    $chartBaseQuery->whereBetween('requested_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
                } elseif ($period === 'year' || $period === 'This Year') {
                    $chartBaseQuery->whereBetween('requested_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                }
            }
            if ($search && $search !== 'null') {
                $chartBaseQuery->where(function($q) use ($search) {
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
            if ($status && $status !== 'null' && in_array($status, ['pending', 'endorsed', 'rejected'])) {
                // Apply the same status filter logic as the table
                if ($status === 'pending') {
                    $chartBaseQuery->where('workflow_state', '!=', 'completed');
                } elseif ($status === 'endorsed') {
                    $chartBaseQuery->where('status', 'endorsed')->where('workflow_state', 'completed');
                } else {
                    $chartBaseQuery->where('status', $status);
                }
            }
            
            $rawCounts = (clone $chartBaseQuery)
                ->selectRaw("type, $monthExpr as month, COUNT(*) as count")
                ->groupBy('type', 'month')
                ->orderBy('month')
                ->get();

            $dateDetails = [];
            if ($period === 'month' || $period === 'This Month') {
                // Use the same base query as the table for date details (chartBaseQuery already has all filters)
                $dateDetailsQuery = (clone $chartBaseQuery);
                $dateDetails = $dateDetailsQuery->selectRaw("DATE(requested_at) as date, COUNT(*) as count")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->groupBy(function($item) {
                        $day = (int) date('j', strtotime($item->date));
                        if ($day <= 5) return '1-5';
                        if ($day <= 10) return '6-10';
                        if ($day <= 15) return '11-15';
                        if ($day <= 20) return '16-20';
                        if ($day <= 25) return '21-25';
                        return '26-31';
                    })
                    ->map(function($group) {
                        return $group->map(function($item) {
                            return [
                                'date' => $item->date,
                                'count' => $item->count
                            ];
                        });
                    });
            }
            
            $monthlyCounts = [
                'Publication' => array_fill_keys($months, 0),
                'Citation' => array_fill_keys($months, 0),
            ];
            foreach ($rawCounts as $row) {
                $monthlyCounts[$row->type][$row->month] = $row->count;
            }
            
            // Get paginated requests for table (limit to 10 for real-time updates)
            $paginatedRequests = $tableQuery->limit(10)->get();
            
            // Format requests for frontend (include signature progress)
            $formattedRequests = $paginatedRequests->map(function($request) {
                // Load signatures count for progress calculation
                $request->loadCount('signatures');
                return [
                    'id' => $request->id,
                    'request_code' => $request->request_code,
                    'type' => $request->type,
                    'status' => $request->status,
                    'workflow_state' => $request->workflow_state,
                    'requested_at' => $request->requested_at->format('M d, Y H:i'),
                    'user_name' => $request->user ? $request->user->name : 'Unknown',
                    'user_email' => $request->user ? $request->user->email : 'Unknown',
                    'signature_progress' => $request->getSignatureProgress(),
                    'workflow_stage' => $request->getWorkflowStageName(),
                ];
            });
            
            // Extract college data with academic rank breakdown from filtered requests
            $collegeCounts = [];
            $collegeRankBreakdown = [];
            foreach ($allRequests as $req) {
                $formData = is_string($req->form_data) ? json_decode($req->form_data, true) : $req->form_data;
                if (is_array($formData) && isset($formData['college']) && !empty($formData['college'])) {
                    $college = $formData['college'];
                    $academicRank = $formData['academicrank'] ?? $formData['rank'] ?? 'Unknown';
                    
                    // Count total per college
                    $collegeCounts[$college] = ($collegeCounts[$college] ?? 0) + 1;
                    
                    // Count per college and academic rank
                    if (!isset($collegeRankBreakdown[$college])) {
                        $collegeRankBreakdown[$college] = [];
                    }
                    $collegeRankBreakdown[$college][$academicRank] = ($collegeRankBreakdown[$college][$academicRank] ?? 0) + 1;
                }
            }
            arsort($collegeCounts); // Sort by count descending
            
            return response()->json([
                'months' => $months,
                'monthlyCounts' => $monthlyCounts,
                'statusCounts' => $statusCounts,
                'collegeCounts' => $collegeCounts,
                'collegeRankBreakdown' => $collegeRankBreakdown,
                'type' => $type,
                'period' => $period,
                'totalRecords' => $allRequests->count(),
                'dateDetails' => $dateDetails,
                'filtered_count' => $allRequests->count(),
                'total_count' => \App\Models\Request::where('status', '!=', 'draft')->count(),
                'requests' => $formattedRequests, // Add request table data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'months' => [],
                'monthlyCounts' => ['Publication' => [], 'Citation' => []],
                'statusCounts' => ['pending' => 0, 'endorsed' => 0, 'rejected' => 0],
                'type' => null,
                'period' => null,
                'totalRecords' => 0,
            ]);
        }
    }

    // streamUpdates removed: admin dashboard no longer uses real-time SSE

    public function exportActivityLogs()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        try {
            // Get all activity logs (not just the 10 shown on dashboard)
            $activityLogs = ActivityLog::with('user', 'userRequest')
                ->whereNotIn('action', ['created']) // Exclude submission requests
                ->orderByDesc('created_at')
                ->get();

            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Activity Logs');

            // Set headers
            $headers = [
                'A1' => 'Date & Time',
                'B1' => 'Action',
                'C1' => 'Description',
                'D1' => 'User',
                'E1' => 'Request Code',
                'F1' => 'Details',
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Style headers
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B1538'], // Maroon color
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(50);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(40);

            // Add data rows
            $row = 2;
            foreach ($activityLogs as $log) {
                // Format description based on action type
                $description = $this->formatActivityDescription($log);
                
                // Get user name
                $userName = $log->user ? $log->user->name : 'System';
                if ($log->user && $log->user->role === 'admin') {
                    $userName .= ' (Admin)';
                }

                // Get request code
                $requestCode = $log->userRequest ? $log->userRequest->request_code : ($log->details['request_code'] ?? 'N/A');

                // Format details as JSON string
                $details = json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                // Format date
                $dateTime = $log->created_at->setTimezone('Asia/Manila')->format('Y-m-d H:i:s');

                // Format action label
                $actionLabel = ucfirst(str_replace('_', ' ', $log->action));

                $sheet->setCellValue('A' . $row, $dateTime);
                $sheet->setCellValue('B' . $row, $actionLabel);
                $sheet->setCellValue('C' . $row, $description);
                $sheet->setCellValue('D' . $row, $userName);
                $sheet->setCellValue('E' . $row, $requestCode);
                $sheet->setCellValue('F' . $row, $details);

                // Style data rows
                $dataStyle = [
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ];
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($dataStyle);
                $sheet->getRowDimension($row)->setRowHeight(-1); // Auto height

                $row++;
            }

            // Freeze first row
            $sheet->freezePane('A2');

            // Create writer and save to temporary file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'activity_logs_' . now()->format('Y-m-d_His') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'activity_logs_');
            $writer->save($tempFile);

            // Return download response
            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Failed to export activity logs: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to export activity logs: ' . $e->getMessage());
        }
    }

    private function formatActivityDescription($log): string
    {
        $details = $log->details ?? [];
        $action = $log->action;

        switch ($action) {
            case 'signed':
                $roleLabel = $details['signatory_role_label'] ?? ucfirst(str_replace('_', ' ', $details['signatory_role'] ?? ''));
                $requestCode = $details['request_code'] ?? '';
                $signatoryName = $details['signatory_name'] ?? '';
                return "Request {$requestCode} signed by {$signatoryName} ({$roleLabel})";

            case 'workflow_completed':
                $requestCode = $details['request_code'] ?? '';
                $finalSignatory = $details['final_signatory_name'] ?? '';
                return "Request {$requestCode} workflow completed by {$finalSignatory}";

            case 'deleted':
                $requestCode = $details['request_code'] ?? '';
                return "Request {$requestCode} deleted";

            case 'file_downloaded':
                $requestCode = $details['request_code'] ?? '';
                $filename = $details['filename'] ?? '';
                return "Downloaded {$filename} from request {$requestCode}";

            case 'user_created':
                $userName = $details['created_user_name'] ?? '';
                $userRole = $details['created_user_role'] ?? '';
                return "Created user {$userName} ({$userRole})";

            case 'user_updated':
                $userName = $details['updated_user_name'] ?? '';
                $changes = $details['changes'] ?? [];
                $changeList = [];
                if (isset($changes['name'])) $changeList[] = 'name';
                if (isset($changes['email'])) $changeList[] = 'email';
                if (isset($changes['role'])) $changeList[] = 'role';
                if (isset($changes['password'])) $changeList[] = 'password';
                $changeText = !empty($changeList) ? ' (' . implode(', ', $changeList) . ')' : '';
                return "Updated user {$userName}{$changeText}";

            case 'user_deleted':
                $userName = $details['deleted_user_name'] ?? '';
                $userRole = $details['deleted_user_role'] ?? '';
                return "Deleted user {$userName} ({$userRole})";

            case 'settings_updated':
                $category = $details['category'] ?? 'settings';
                $categoryLabels = [
                    'form_dropdowns' => 'Form Dropdowns',
                    'publication_counts' => 'Publication Counts',
                    'official_info' => 'Official Information',
                    'application_controls' => 'Application Controls',
                    'calendar' => 'Calendar',
                ];
                $categoryLabel = $categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category));
                return "Updated {$categoryLabel} settings";

            case 'signatory_account_created':
                $accountType = $details['account_type_label'] ?? ucfirst(str_replace('_', ' ', $details['account_type'] ?? ''));
                $userName = $details['user_name'] ?? '';
                return "Created {$accountType} account for {$userName}";

            case 'signatory_account_deleted':
                $accountType = $details['account_type_label'] ?? ucfirst(str_replace('_', ' ', $details['account_type'] ?? ''));
                $userName = $details['deleted_user_name'] ?? '';
                return "Deleted {$accountType} account for {$userName}";

            case 'nudged':
                $requestCode = $details['request_code'] ?? '';
                return "Nudge for request {$requestCode}";

            default:
                return ucfirst(str_replace('_', ' ', $action));
        }
    }
} 