@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <h2 class="text-2xl font-bold text-maroon-800 mb-6">Add New User</h2>
        @if($errors->any())
            <div class="mb-4 text-red-700 bg-red-100 border border-red-200 rounded p-3">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition font-semibold text-sm">Back</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-maroon-700 text-white rounded-lg shadow hover:bg-maroon-800 transition font-semibold text-sm">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection 