<x-app-layout>
    <div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
                                                 </a>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center h-full m-0">
            <div class="w-full h-full flex-1 rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-4 flex flex-col items-stretch">
                <div class="relative flex items-center mb-4">
                    <h1 class="text-2xl font-bold text-maroon-900 flex items-center gap-2">
                        <svg class="w-7 h-7 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Accounts
                    </h1>
                </div>
                
                <!-- Notifications handled by global system via hidden placeholders at bottom -->
                <div class="relative w-full">
                            <div class="mt-4">
                                <!-- Filters and Search -->
                                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                                <div class="flex gap-2 relative">
                                    <div class="flex bg-gray-100 rounded-full p-1 gap-1">
                                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-row gap-2 items-center w-full" id="user-search-form">
                                            <input type="hidden" name="search" value="{{ $currentSearch ?? '' }}" />
                                            <button type="submit" name="role" value="" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ empty($currentRole) ? 'bg-maroon-700 text-white border-maroon-700' : 'bg-white text-gray-700 hover:bg-maroon-600 hover:text-white border-gray-200' }}">All</button>
                                            <button type="submit" name="role" value="user" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ $currentRole === 'user' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-700 hover:bg-yellow-600 hover:text-white border-gray-200' }}">User</button>
                                            <button type="submit" name="role" value="admin" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ $currentRole === 'admin' ? 'bg-maroon-700 text-white border-maroon-700' : 'bg-white text-gray-700 hover:bg-maroon-600 hover:text-white border-gray-200' }}">Admin</button>
<button type="submit" name="role" value="signatory" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none border {{ $currentRole === 'signatory' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-700 hover:bg-yellow-600 hover:text-white border-gray-200' }}">Signatory</button>
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
                                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-1.5 bg-maroon-700 text-white rounded-xl shadow hover:bg-maroon-800 transition font-semibold text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Add User
                                    </a>
                                </div>
                            </div>
                            <div class="bg-white/30 backdrop-blur border border-white/40 rounded-xl shadow p-2 overflow-y-auto flex-1 min-h-0">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-fixed divide-y divide-maroon-200 text-xs border border-white/40 rounded-t-xl overflow-hidden">
                                        <thead class="bg-white/30 backdrop-blur border-b border-white/40 rounded-t-xl sticky top-0 z-10">
                                            <tr>
                                                <th class="px-4 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Name</th>
                                                <th class="px-4 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Email</th>
                                                <th class="px-4 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Role</th>
                                                <th class="px-4 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Signatory Type</th>
                                                <th class="px-4 py-2 text-center font-bold text-maroon-900 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-maroon-100">
                                        @foreach($users as $user)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 max-w-[160px] truncate" title="{{ $user->name }}">{{ $user->name }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 max-w-[180px] truncate" title="{{ $user->email }}">{{ $user->email }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm">
                                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-maroon-100 text-maroon-800' : 'bg-gray-100 text-gray-700' }}">
                                                        {{ ucfirst($user->role) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                                    @if($user->role === 'signatory')
                                                        {{ str_replace('_',' ', ucfirst($user->signatory_type ?? '')) }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-center">
                                                    <div class="flex flex-row gap-2 items-center justify-center">
                                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg bg-maroon-700 text-white hover:bg-maroon-800 transition-colors" title="Edit">
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
                            <div class="mt-3">{{ $users->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(session('success'))
        <div id="success-notification" class="hidden">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div id="error-notification" class="hidden">{{ session('error') }}</div>
    @endif
</x-app-layout> 