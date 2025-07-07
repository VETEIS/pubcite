<x-app-layout>
<div class="min-h-[calc(100vh-4rem)] flex flex-col justify-center items-center">
    <div class="w-full max-w-7xl flex-1 flex flex-col justify-center items-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 rounded-lg relative w-full">
            <div class="w-full flex flex-col sm:flex-row gap-2 sm:gap-4 mb-6 relative">
                <!-- Notification Area: absolutely positioned, overlay -->
                <div class="absolute top-0 right-0 z-20 mt-2 mr-4">
                    @if(session('success'))
                        <div class="text-green-700 bg-green-100 border border-green-200 rounded px-3 py-1 text-xs font-medium shadow">{{ session('success') }}</div>
                    @elseif(session('error'))
                        <div class="text-red-700 bg-red-100 border border-red-200 rounded px-3 py-1 text-xs font-medium shadow">{{ session('error') }}</div>
                    @endif
                </div>
                <!-- Users Card Header -->
                <div class="flex-1 bg-white rounded-xl shadow border p-2 sm:p-4 flex items-center gap-2 sm:gap-4 min-w-0 w-full">
                    <div class="flex items-center gap-1 sm:gap-2 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-maroon-100 flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <span class="font-semibold text-maroon-800 text-sm sm:text-base truncate">Accounts</span>
                    </div>
                    <div class="flex flex-row gap-2 ml-auto items-center">
                        <div class="flex flex-col items-center justify-center min-w-0 flex-1 bg-maroon-50 border border-maroon-200 rounded-lg shadow-sm py-1.5 px-3">
                            <span class="text-base font-bold text-maroon-700">{{ ($adminCount ?? 0) + ($userCount ?? 0) }}</span>
                            <span class="text-xs text-maroon-800 font-semibold tracking-wide">Total</span>
                        </div>
                        <div class="flex flex-col items-center justify-center min-w-0 flex-1 bg-maroon-50 border border-maroon-200 rounded-lg shadow-sm py-1.5 px-3">
                            <span class="text-base font-bold text-maroon-700">{{ $adminCount ?? 0 }}</span>
                            <span class="text-xs text-maroon-800 font-semibold tracking-wide">Admins</span>
                        </div>
                        <div class="flex flex-col items-center justify-center min-w-0 flex-1 bg-maroon-50 border border-maroon-200 rounded-lg shadow-sm py-1.5 px-3">
                            <span class="text-base font-bold text-maroon-700">{{ $userCount ?? 0 }}</span>
                            <span class="text-xs text-maroon-800 font-semibold tracking-wide">Users</span>
                        </div>
                    </div>
                    @if($lastCreatedUser)
                    <span class="hidden sm:inline-block mx-2 text-gray-300">|</span>
                    <span class="text-xs text-gray-500 truncate flex-shrink-0 flex-grow-0" style="flex-basis:500px;max-width:500px;">
                        <span class="text-gray-400 uppercase tracking-wide">Last:</span>
                        <span class="font-semibold text-gray-700 truncate max-w-[200px] align-middle" title="{{ $lastCreatedUser->name }}">{{ $lastCreatedUser->name }}</span>
                        <span class="text-gray-400">&lt;</span><span class="truncate max-w-[200px] align-middle" title="{{ $lastCreatedUser->email }}">{{ $lastCreatedUser->email }}</span><span class="text-gray-400">&gt;</span>
                        <span class="text-gray-400 ml-1 align-middle">{{ $lastCreatedUser->created_at->format('M d, Y') }}</span>
                    </span>
                    @endif
                </div>
            </div>
            <div class="mt-8">
                <!-- Filters and Search -->
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                    <div class="flex gap-2 relative">
                        <div class="flex bg-gray-100 rounded-full p-1 gap-1">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-row gap-2 items-center w-full" id="user-search-form">
                                <input type="hidden" name="search" value="{{ $currentSearch ?? '' }}" />
                                <button type="submit" name="role" value="" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ empty($currentRole) ? 'bg-maroon-700 text-white border-maroon-700' : 'bg-white text-gray-700 hover:bg-maroon-600 hover:text-white border-gray-200' }}">All</button>
                                <button type="submit" name="role" value="user" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ $currentRole === 'user' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-700 hover:bg-yellow-600 hover:text-white border-gray-200' }}">User</button>
                                <button type="submit" name="role" value="admin" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ $currentRole === 'admin' ? 'bg-maroon-700 text-white border-maroon-700' : 'bg-white text-gray-700 hover:bg-maroon-600 hover:text-white border-gray-200' }}">Admin</button>
                                <div class="flex-1 flex items-center ml-2">
                                    <input type="text" name="search" value="{{ $currentSearch ?? '' }}" placeholder="Search name or email" class="w-full border rounded-lg px-3 py-1 focus:border-maroon-500 focus:ring-maroon-500 text-sm h-8" id="user-search-input" autocomplete="on" />
                                    <button type="submit" class="ml-1 p-2 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 transition-colors flex items-center justify-center h-8 w-8" title="Search">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="flex-1 flex justify-end">
                        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-1 bg-maroon-700 text-white rounded-lg shadow hover:bg-maroon-800 transition font-semibold text-sm">
                            + Add User
                        </a>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-0 max-h-[45vh] h-[45vh] overflow-y-auto">
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
</div>
</x-app-layout> 