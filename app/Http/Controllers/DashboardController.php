<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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
            if ($search) {
                $query->where(function($q) use ($search) {
                    if (config('database.default') === 'pgsql') {
                        $q->where('request_code', 'ilike', "%$search%")
                          ->orWhereHas('user', function($uq) use ($search) {
                              $uq->where('name', 'ilike', "%$search%")
                                 ->orWhere('email', 'ilike', "%$search%") ;
                          });
                    } else {
                        $q->where('request_code', 'like', "%$search%")
                          ->orWhereHas('user', function($uq) use ($search) {
                              $uq->where('name', 'like', "%$search%")
                                 ->orWhere('email', 'like', "%$search%") ;
                          });
                    }
                });
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
            $allRequests = $query->get();
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
            return view('admin.dashboard', compact('allRequests', 'stats', 'status', 'search', 'filterCounts', 'type', 'period', 'rangeDescription'));
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
        if ($search) {
            $query->where(function($q) use ($search) {
                if (config('database.default') === 'pgsql') {
                    $q->where('request_code', 'ilike', "%$search%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'ilike', "%$search%")
                             ->orWhere('email', 'ilike', "%$search%") ;
                      });
                } else {
                    $q->where('request_code', 'like', "%$search%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%$search%")
                             ->orWhere('email', 'like', "%$search%") ;
                      });
                }
            });
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
        $allRequests = $query->get();
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
        return response()->json([
            'requests' => $allRequests,
            'stats' => $stats,
            'filterCounts' => $filterCounts,
        ]);
    }
} 