<x-app-layout>
<div class="min-h-[calc(100vh-4rem)] flex flex-col justify-center items-center">
    <div class="w-full max-w-7xl flex-1 flex flex-col justify-center items-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg p-6 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <h2 class="text-2xl font-bold text-maroon-800">Manage Users</h2>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-maroon-700 text-white rounded-lg shadow hover:bg-maroon-800 transition font-semibold text-sm">
                    + Add User
                </a>
            </div>
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-2 mb-4 w-full" id="user-search-form">
                <div class="relative w-32">
                    <select name="role" class="border rounded-lg px-3 pr-8 py-2 focus:border-maroon-500 focus:ring-maroon-500 w-full" onchange="document.getElementById('user-search-form').submit()">
                        <option value="">All</option>
                        <option value="user" {{ (isset($currentRole) && $currentRole === 'user') ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ (isset($currentRole) && $currentRole === 'admin') ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $currentSearch ?? '' }}" placeholder="Search name or email" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" id="user-search-input" autocomplete="on" />
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-maroon-700 text-white rounded-lg shadow hover:bg-maroon-800 transition font-semibold text-sm">Filter</button>
                </div>
            </form>
            <script>
            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
            const searchInput = document.getElementById('user-search-input');
            const searchForm = document.getElementById('user-search-form');
            if (searchInput && searchForm) {
                searchInput.addEventListener('input', debounce(function() {
                    searchForm.submit();
                }, 400));
            }
            </script>
            @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-200 rounded p-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 text-red-700 bg-red-100 border border-red-200 rounded p-3">{{ session('error') }}</div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 max-w-[160px] truncate" title="{{ $user->name }}">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-[180px] truncate" title="{{ $user->email }}">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-row gap-2 items-center justify-center">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h6v-6H3v6z" /></svg>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</div>
</x-app-layout> 