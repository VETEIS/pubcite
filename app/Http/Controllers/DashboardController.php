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
            if ($status && in_array($status, ['pending', 'endorsed', 'rejected'])) {
                $query->where('status', $status);
            }
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('request_code', 'like', "%$search%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%$search%")
                             ->orWhere('email', 'like', "%$search%") ;
                      });
                });
            }
            if ($type && in_array($type, ['Publication', 'Citation'])) {
                $query->where('type', $type);
            }
            if ($period) {
                $now = now();
                if ($period === 'today') {
                    $query->whereDate('requested_at', $now->toDateString());
                } elseif ($period === 'week') {
                    $query->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($period === 'month') {
                    $query->whereMonth('requested_at', $now->month)->whereYear('requested_at', $now->year);
                }
            }
            $allRequests = $query->get();
            $now = now();
            $stats = [
                'publication' => [
                    'today' => \App\Models\Request::where('type', 'Publication')->whereDate('requested_at', $now->toDateString())->count(),
                    'yesterday' => \App\Models\Request::where('type', 'Publication')->whereDate('requested_at', $now->copy()->subDay()->toDateString())->count(),
                    'week' => \App\Models\Request::where('type', 'Publication')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                    'month' => \App\Models\Request::where('type', 'Publication')->whereMonth('requested_at', $now->month)->whereYear('requested_at', $now->year)->count(),
                    'year' => \App\Models\Request::where('type', 'Publication')->whereYear('requested_at', $now->year)->count(),
                ],
                'citation' => [
                    'today' => \App\Models\Request::where('type', 'Citation')->whereDate('requested_at', $now->toDateString())->count(),
                    'yesterday' => \App\Models\Request::where('type', 'Citation')->whereDate('requested_at', $now->copy()->subDay()->toDateString())->count(),
                    'week' => \App\Models\Request::where('type', 'Citation')->whereBetween('requested_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                    'month' => \App\Models\Request::where('type', 'Citation')->whereMonth('requested_at', $now->month)->whereYear('requested_at', $now->year)->count(),
                    'year' => \App\Models\Request::where('type', 'Citation')->whereYear('requested_at', $now->year)->count(),
                ],
            ];
            $filterCounts = [
                'pending' => \App\Models\Request::where('status', 'pending')->count(),
                'endorsed' => \App\Models\Request::where('status', 'endorsed')->count(),
                'rejected' => \App\Models\Request::where('status', 'rejected')->count(),
            ];
            return view('admin.dashboard', compact('allRequests', 'stats', 'status', 'search', 'filterCounts', 'type', 'period'));
        }
        $requests = \App\Models\Request::where('user_id', $user->id)->orderByDesc('requested_at')->get();
        return view('dashboard', compact('requests'));
    }
} 