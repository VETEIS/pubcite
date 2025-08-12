<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return response()->json($items);
    }
} 