<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminNotification;

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
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'signatory_type' => $validated['role'] === 'signatory' ? ($validated['signatory_type'] ?? null) : null,
        ]);
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email',Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'admin', 'signatory'])],
            'password' => 'nullable|string|min:8|confirmed',
            'signatory_type' => ['nullable', Rule::in(['faculty','center_manager','college_dean'])],
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->signatory_type = $validated['role'] === 'signatory' ? ($validated['signatory_type'] ?? null) : null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $userAuth = Auth::user();
        if ($userAuth && $userAuth->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function listNotifications(Request $request)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $notifications = AdminNotification::where('user_id', $admin->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        $unreadCount = AdminNotification::where('user_id', $admin->id)->whereNull('read_at')->count();
        return response()->json([
            'unread' => $unreadCount,
            'items' => $notifications,
        ]);
    }

    public function markNotificationsRead(Request $request)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        AdminNotification::where('user_id', $admin->id)->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markNotificationAsRead($id)
    {
        $admin = Auth::user();
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification = AdminNotification::where('id', $id)
            ->where('user_id', $admin->id)
            ->first();
            
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
        
        $notification->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
} 