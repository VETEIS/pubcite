<x-app-layout>
<div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->routeIs('dashboard') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
                <svg class="w-7 h-7 {{ request()->routeIs('dashboard') ? 'text-maroon-800 fill-maroon-800' : 'text-maroon-800' }} group-hover:text-maroon-800 transition" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6" /></svg>
                <span class="text-xs mt-1 pl-2 {{ request()->routeIs('dashboard') ? 'font-extrabold text-maroon-800' : 'font-bold' }} group-hover:text-maroon-800">Overview</span>
                @if(request()->routeIs('dashboard'))
                    <div class="h-0.5 bg-maroon-800 rounded-full w-8 mx-auto mt-1"></div>
                @endif
            </a>
            
    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center h-full m-0">
        <div class="w-full h-full max-w-3xl mx-auto rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-6 flex flex-col items-stretch">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select name="role" id="role-select" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="signatory" {{ old('role') == 'signatory' ? 'selected' : '' }}>Signatory</option>
                </select>
                            </div>
            <div id="signatory-type-group" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Type</label>
                <select name="signatory_type" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500">
                    <option value="">Select type</option>
                    <option value="faculty" {{ old('signatory_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                    <option value="center_manager" {{ old('signatory_type') == 'center_manager' ? 'selected' : '' }}>Research Center Manager</option>
                    <option value="college_dean" {{ old('signatory_type') == 'college_dean' ? 'selected' : '' }}>College Dean</option>
                </select>
            </div>
            <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-xl shadow hover:bg-gray-300 transition font-semibold text-sm">Back</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-maroon-700 text-white rounded-xl shadow hover:bg-maroon-800 transition font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const roleSelect = document.getElementById('role-select');
    const signatoryGroup = document.getElementById('signatory-type-group');
    function toggleSignatory() {
        if (roleSelect && signatoryGroup) {
            signatoryGroup.classList.toggle('hidden', roleSelect.value !== 'signatory');
        }
    }
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleSignatory);
        toggleSignatory();
    }
</script>
</x-app-layout> 