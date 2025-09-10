<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\NudgeNotification;
use App\Models\User;

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
            $statusQuery = \App\Models\Request::query();
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
            $filterCounts = [
                'pending' => $statusQuery->where('status', 'pending')->count(),
                'endorsed' => $statusQuery->where('status', 'endorsed')->count(),
                'rejected' => $statusQuery->where('status', 'rejected')->count(),
            ];
            // Chart Data Aggregation
            $chartType = $type;
            $chartPeriod = $period;
            $chartNow = $now;
            $chartStartMonth = $chartNow->copy()->subMonths(11)->startOfMonth();
            $chartEndMonth = $chartNow->copy()->endOfMonth();
            $chartBaseQuery = \App\Models\Request::query();
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
            $statusCounts = [
                'pending' => 0,
                'endorsed' => 0,
                'rejected' => 0,
            ];
            $statusRaw = $chartBaseQuery->selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
            foreach ($statusRaw as $row) {
                $statusCounts[$row->status] = $row->count;
            }
            $recentApplications = \App\Models\Request::with('user')->where('status', '!=', 'draft')->orderByDesc('requested_at')->limit(5)->get();
            $activityLogs = \App\Models\ActivityLog::with('user')
                ->whereNotIn('action', ['created']) // Exclude submission requests
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
            return view('admin.dashboard', compact('requests', 'stats', 'status', 'search', 'filterCounts', 'type', 'period', 'rangeDescription', 'recentApplications', 'monthlyCounts', 'statusCounts', 'months', 'activityLogs'));
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
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\AdminNotification::create([
                'user_id' => $admin->id,
                'request_id' => $request->id,
                'type' => 'nudge',
                'title' => 'Nudge: ' . $request->request_code,
                'message' => $user->name . ' nudged a pending ' . strtolower($request->type) . ' request.',
                'data' => [
                    'request_code' => $request->request_code,
                    'type' => $request->type,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ],
            ]);
            try {
                Mail::to($admin->email)->send(new NudgeNotification($request, $user));
            } catch (\Exception $e) {
                Log::error('Nudge email failed: ' . $e->getMessage());
            }
        }
        return back()->with('success', 'Nudge sent to admins.');
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
            
            if ($status && $status !== 'null' && in_array($status, ['pending', 'endorsed', 'rejected'])) {
                $tableQuery->where('status', $status);
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
            $statusCounts = [
                'pending' => $allRequests->where('status', 'pending')->count(),
                'endorsed' => $allRequests->where('status', 'endorsed')->count(),
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
            
            $rawCounts = \App\Models\Request::selectRaw("type, $monthExpr as month, COUNT(*) as count")
                ->where('status', '!=', 'draft') // Exclude drafts from chart data
                ->when($type && $type !== 'null' && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations']), function($q) use ($type) {
                    $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                    $q->where('type', $dbType);
                })
                ->when($period && $period !== 'null', function($q) use ($period, $now) {
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
                ->when($search && $search !== 'null', function($q) use ($search) {
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
                ->when($status && $status !== 'null' && in_array($status, ['pending', 'endorsed', 'rejected']), function($q) use ($status) {
                    $q->where('status', $status);
                })
                ->groupBy('type', 'month')
                ->orderBy('month')
                ->get();

            $dateDetails = [];
            if ($period === 'month' || $period === 'This Month') {
                $dateDetails = \App\Models\Request::selectRaw("DATE(requested_at) as date, COUNT(*) as count")
                    ->where('status', '!=', 'draft') // Exclude drafts from date details
                    ->when($type && $type !== 'null' && in_array($type, ['Publication', 'Citation', 'Publications', 'Citations']), function($q) use ($type) {
                        $dbType = $type === 'Publications' ? 'Publication' : ($type === 'Citations' ? 'Citation' : $type);
                        $q->where('type', $dbType);
                    })
                    ->when($period && $period !== 'null', function($q) use ($period, $now) {
                        if ($period === 'month' || $period === 'This Month') {
                            $q->whereBetween('requested_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                        }
                    })
                    ->when($search && $search !== 'null', function($q) use ($search) {
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
                    ->when($status && $status !== 'null' && in_array($status, ['pending', 'endorsed', 'rejected']), function($q) use ($status) {
                        $q->where('status', $status);
                    })
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
            
            return response()->json([
                'months' => $months,
                'monthlyCounts' => $monthlyCounts,
                'statusCounts' => $statusCounts,
                'type' => $type,
                'period' => $period,
                'totalRecords' => $allRequests->count(),
                'dateDetails' => $dateDetails,
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

    public function streamUpdates()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');

        $lastCheck = now();
        $maxExecutionTime = 300;
        $startTime = time();
        $lastUpdateTime = null;
        
        while (true) {
            if (time() - $startTime > $maxExecutionTime) {
                echo "data: " . json_encode(['type' => 'timeout']) . "\n\n";
                break;
            }
            
            try {
                $newRequests = 0;
                $statusChanges = 0;
                
                try {
                    $newRequests = \App\Models\Request::where('requested_at', '>', $lastCheck)->count();
                } catch (\Exception $e) {
                    Log::error('SSE Stream Error: ' . $e->getMessage());
                    $newRequests = 0;
                }
                
                try {
                    $statusChanges = \App\Models\ActivityLog::where('created_at', '>', $lastCheck)
                        ->whereIn('action', ['created', 'status_changed', 'deleted', 'nudged'])
                        ->count();
                } catch (\Exception $e) {
                    Log::error('SSE Stream Error: ' . $e->getMessage());
                    $statusChanges = 0;
                }
                
                if (($newRequests > 0 || $statusChanges > 0) && 
                    (!$lastUpdateTime || now()->diffInSeconds($lastUpdateTime) > 5)) {
                    
                    $now = now();
                    $stats = [
                        'publication' => [
                            'week' => 0, 'month' => 0, 'quarter' => 0, 'year' => 0
                        ],
                        'citation' => [
                            'week' => 0, 'month' => 0, 'quarter' => 0, 'year' => 0
                        ],
                    ];
                    
                    try {
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
                    } catch (\Exception $e) {
                        Log::error('SSE Stream Error: ' . $e->getMessage());
                    }
                    
                    $recentApplications = collect();
                    $activityLogs = collect();
                    
                    try {
                        $recentApplications = \App\Models\Request::with('user')->where('status', '!=', 'draft')->orderByDesc('requested_at')->limit(5)->get();
                    } catch (\Exception $e) {
                        Log::error('SSE Stream Error: ' . $e->getMessage());
                    }
                    
                    try {
                        $activityLogs = \App\Models\ActivityLog::with('user')
                ->whereNotIn('action', ['created']) // Exclude submission requests
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
                    } catch (\Exception $e) {
                        Log::error('SSE Stream Error: ' . $e->getMessage());
                    }
                    
                    $updateData = [
                        'stats' => $stats,
                        'recentApplications' => $recentApplications,
                        'activityLogs' => $activityLogs,
                        'hasChanges' => true
                    ];
                    
                    echo "data: " . json_encode($updateData) . "\n\n";
                    $lastUpdateTime = now();
                }
                
                $lastCheck = now();
                
            } catch (\Exception $e) {
                Log::error('SSE Stream Error: ' . $e->getMessage());
            }
            
            if (!$lastUpdateTime || now()->diffInSeconds($lastUpdateTime) > 30) {
                echo "data: " . json_encode(['type' => 'keepalive']) . "\n\n";
                $lastUpdateTime = now();
            }
            
            sleep(2);
        }
    }
} 