<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role') && in_array($request->role, ['user', 'admin', 'signatory'])) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                if (config('database.default') === 'pgsql') {
                    $q->where('name', 'ilike', "%$search%")
                      ->orWhere('email', 'ilike', "%$search%") ;
                } else {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%") ;
                }
            });
        }
        
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['id', 'name', 'email', 'role'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $users = $query->paginate(15)->withQueryString();
        $currentRole = $request->role;
        $currentSearch = $request->search;
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();
        $signatoryCount = User::where('role', 'signatory')->count();
        $lastCreatedUser = User::orderBy('created_at', 'desc')->first();
        return view('admin.users', compact('users', 'currentRole', 'currentSearch', 'adminCount', 'userCount', 'signatoryCount', 'lastCreatedUser'));
    }

    public function create()
    {
        return view('admin.user-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['user', 'admin', 'signatory'])],
            'signatory_type' => ['nullable', Rule::in(['faculty','center_manager','college_dean'])],
        ]);
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->signatory_type = $validated['role'] === 'signatory' ? ($validated['signatory_type'] ?? null) : null;
        $user->save();
        
        // Clear signatory cache when new signatory is created
        if ($user->role === 'signatory') {
            Cache::forget("signatories_faculty_");
            Cache::forget("signatories_dean_");
            Cache::forget("signatories_center_manager_");
            Cache::forget("signatories_college_dean_");
        }
        
        // Log activity
        try {
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'request_id' => null,
                'action' => 'user_created',
                'details' => [
                    'created_user_id' => $user->id,
                    'created_user_name' => $user->name,
                    'created_user_email' => $user->email,
                    'created_user_role' => $user->role,
                    'signatory_type' => $user->signatory_type,
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create activity log for user creation: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $userAuth = Auth::user();
        if ($userAuth && $userAuth->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot edit your own role.');
        }
        return view('admin.user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $userAuth = Auth::user();
        if ($userAuth && $userAuth->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot change your own role.');
        }
        // Different validation rules based on auth provider
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => ['required','email',Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'admin', 'signatory'])],
            'signatory_type' => ['nullable', Rule::in(['faculty','center_manager','college_dean'])],
        ];
        
        // Only add password validation for non-Google users
        // Only validate password if it's actually provided (not empty)
        if ($user->auth_provider !== 'google' && $request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validated = $request->validate($validationRules);
        
        // Track changes for activity log
        $changes = [];
        $originalRole = $user->getOriginal('role');
        $originalName = $user->getOriginal('name');
        $originalEmail = $user->getOriginal('email');
        $originalSignatoryType = $user->getOriginal('signatory_type');
        
        // Set role first so the mutator can check it
        $user->role = $validated['role'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->signatory_type = $validated['role'] === 'signatory' ? ($validated['signatory_type'] ?? null) : null;
        
        // Track what changed
        if ($originalRole !== $user->role) {
            $changes['role'] = ['from' => $originalRole, 'to' => $user->role];
        }
        if ($originalName !== $user->name) {
            $changes['name'] = ['from' => $originalName, 'to' => $user->name];
        }
        if ($originalEmail !== $user->email) {
            $changes['email'] = ['from' => $originalEmail, 'to' => $user->email];
        }
        if ($originalSignatoryType !== $user->signatory_type) {
            $changes['signatory_type'] = ['from' => $originalSignatoryType, 'to' => $user->signatory_type];
        }
        if ($request->filled('password')) {
            $changes['password'] = ['changed' => true];
        }
        
        // Only update password for non-Google users
        if ($user->auth_provider !== 'google' && !empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        
        // Clear signatory cache when role changes to/from signatory
        if ($user->role === 'signatory' || $originalRole === 'signatory') {
            Cache::forget("signatories_faculty_");
            Cache::forget("signatories_dean_");
            Cache::forget("signatories_center_manager_");
            Cache::forget("signatories_college_dean_");
        }
        
        // Log activity
        try {
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'request_id' => null,
                'action' => 'user_updated',
                'details' => [
                    'updated_user_id' => $user->id,
                    'updated_user_name' => $user->name,
                    'updated_user_email' => $user->email,
                    'changes' => $changes,
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create activity log for user update: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $userAuth = Auth::user();
        if ($userAuth && $userAuth->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }
        // Store user info before deletion for activity log
        $deletedUserId = $user->id;
        $deletedUserName = $user->name;
        $deletedUserEmail = $user->email;
        $deletedUserRole = $user->role;
        $deletedSignatoryType = $user->signatory_type;
        
        $wasSignatory = $user->role === 'signatory';
        $user->delete();
        
        // Clear signatory cache when signatory is deleted
        if ($wasSignatory) {
            Cache::forget("signatories_faculty_");
            Cache::forget("signatories_dean_");
            Cache::forget("signatories_center_manager_");
            Cache::forget("signatories_college_dean_");
        }
        
        // Log activity
        try {
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'request_id' => null,
                'action' => 'user_deleted',
                'details' => [
                    'deleted_user_id' => $deletedUserId,
                    'deleted_user_name' => $deletedUserName,
                    'deleted_user_email' => $deletedUserEmail,
                    'deleted_user_role' => $deletedUserRole,
                    'signatory_type' => $deletedSignatoryType,
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create activity log for user deletion: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

} 