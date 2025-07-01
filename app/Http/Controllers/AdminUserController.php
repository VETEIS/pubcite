<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role') && in_array($request->role, ['user', 'admin'])) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $currentRole = $request->role;
        $currentSearch = $request->search;
        return view('admin.users', compact('users', 'currentRole', 'currentSearch'));
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
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot edit your own role.');
        }
        return view('admin.user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot change your own role.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email',Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'admin'])],
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
} 