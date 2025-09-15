<x-app-layout>
    <div x-data="{ 
        searchOpen: false
    }" class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        
        <!-- Hidden notification divs for global notification system -->
        @if(session('success'))
            <div id="success-notification" class="hidden">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="error-notification" class="hidden">{{ session('error') }}</div>
        @endif

        @include('admin.partials.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 ml-4 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                .flex-1::-webkit-scrollbar {
                    display: none;
                }
            </style>
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg h-full">
                <!-- Dashboard Header -->
                <div class="relative flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-7 h-7 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h6v-6H3v6z" />
                        </svg>
                        Edit User: {{ $user->name }}
                    </h1>
                    
                    <!-- Search and User Controls -->
                    <div class="flex items-center gap-3">
                        <!-- Search Input Field -->
                        <form method="GET" action="{{ route('dashboard') }}" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-900 shadow-sm w-48 bg-white text-gray-900 placeholder-gray-500" style="transition:all 0.2s;" autocomplete="on">
                            <button type="submit" class="absolute left-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 focus:outline-none" tabindex="-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                        
                        <!-- User Profile Button -->
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition-colors">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-maroon-600 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-red-800">Please fix the following errors:</h3>
                                </div>
                                <ul class="list-disc pl-5 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')
                            <!-- Name Field -->
                <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" id="name-input" value="{{ old('name', $user->name) }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                       placeholder="Enter full name" />
                </div>

                            <!-- Email Field -->
                <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                       placeholder="Enter email address" />
                </div>

                            <!-- Role Field -->
                <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">User Role</label>
                                <select name="role" id="role-select" required 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                                    <option value="">Select a role</option>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="signatory" {{ old('role', $user->role) == 'signatory' ? 'selected' : '' }}>Signatory</option>
                    </select>
                </div>
                            <!-- Signatory Type Field (Conditional) -->
                <div id="signatory-type-group" class="{{ old('role', $user->role) == 'signatory' ? '' : 'hidden' }}">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Signatory Type</label>
                                <select name="signatory_type" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                                    <option value="">Select signatory type</option>
                        <option value="faculty" {{ old('signatory_type', $user->signatory_type) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="center_manager" {{ old('signatory_type', $user->signatory_type) == 'center_manager' ? 'selected' : '' }}>Research Center Manager</option>
                        <option value="college_dean" {{ old('signatory_type', $user->signatory_type) == 'college_dean' ? 'selected' : '' }}>College Dean</option>
                    </select>
                </div>

                            <!-- Password Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        New Password
                                        <span class="text-xs font-normal text-gray-500">(leave blank to keep current)</span>
                                    </label>
                                    <input type="password" name="password" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                           placeholder="Enter new password" />
                    </div>
                    <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                           placeholder="Confirm new password" />
                    </div>
                </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.users.index') }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 hover:shadow-md transition-all duration-300 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-maroon-700 text-white rounded-lg shadow-sm hover:bg-maroon-800 hover:shadow-md transition-all duration-300 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
                </div>
            </main>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('role-select');
    const signatoryGroup = document.getElementById('signatory-type-group');
    const nameInput = document.getElementById('name-input');
        
    function toggleSignatory() {
        if (roleSelect && signatoryGroup) {
            signatoryGroup.classList.toggle('hidden', roleSelect.value !== 'signatory');
        }
    }
    
    function convertNameToUppercase() {
        if (roleSelect && nameInput && roleSelect.value === 'signatory') {
            nameInput.value = nameInput.value.toUpperCase();
        }
    }
        
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            toggleSignatory();
            convertNameToUppercase();
        });
        toggleSignatory();
    }
    
    if (nameInput) {
        nameInput.addEventListener('input', convertNameToUppercase);
    }
    
    // Refresh signatory cache after successful form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Add a small delay to allow the server to process the update
            setTimeout(() => {
                if (window.refreshSignatoryCache) {
                    window.refreshSignatoryCache();
                }
            }, 1000);
        });
    }
</script> 
</x-app-layout> 