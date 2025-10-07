<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SignatoryController extends Controller
{
    public function index(Request $request)
    {
        // Any authenticated user may query signatories (applicants need this)
        if (!Auth::check()) {
            return response()->json([]);
        }
        
        $type = $request->query('type');
        $q = trim((string) $request->query('q', ''));
        
        // Create cache key based on parameters
        $cacheKey = "signatories_{$type}_{$q}";
        
        // Return cached result if available (cache for 5 minutes)
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult !== null) {
            return response()->json($cachedResult);
        }
        
        $query = User::query()->where('role', 'signatory');
        
        if ($type) {
            $query->where('signatory_type', $type);
        }
        
        if ($q !== '') {
            if (config('database.default') === 'pgsql') {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'ilike', "%$q%")
                        ->orWhere('email', 'ilike', "%$q%");
                });
            } else {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%");
                });
            }
        }
        
        $items = $query->orderBy('name')->limit(20)->get(['id','name','email','signatory_type']);
        
        // Cache the result for 5 minutes
        Cache::put($cacheKey, $items, 300);
        
        return response()->json($items);
    }
    
    public function validate(Request $request)
    {
        // Any authenticated user may validate signatories
        if (!Auth::check()) {
            return response()->json(['valid' => false]);
        }
        
        $name = trim((string) $request->query('name', ''));
        $type = $request->query('type');
        
        if (empty($name)) {
            return response()->json(['valid' => false]);
        }
        
        $query = User::query()->where('role', 'signatory')
            ->where('name', $name);
        
        if ($type) {
            $query->where('signatory_type', $type);
        }
        
        $exists = $query->exists();
        
        return response()->json(['valid' => $exists]);
    }
} 