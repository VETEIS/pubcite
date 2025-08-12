<x-app-layout>
<div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
            
    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center h-full m-0">
        <div class="w-full h-full max-w-3xl mx-auto rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-6 flex flex-col items-stretch">
            <h2 class="text-2xl font-bold text-maroon-800 mb-6">Edit User</h2>
            @if($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 border border-red-200 rounded p-3">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="role-select" required class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="signatory" {{ old('role', $user->role) == 'signatory' ? 'selected' : '' }}>Signatory</option>
                    </select>
                </div>
                <div id="signatory-type-group" class="{{ old('role', $user->role) == 'signatory' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Signatory Type</label>
                    <select name="signatory_type" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500">
                        <option value="">Select type</option>
                        <option value="faculty" {{ old('signatory_type', $user->signatory_type) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="center_manager" {{ old('signatory_type', $user->signatory_type) == 'center_manager' ? 'selected' : '' }}>Research Center Manager</option>
                        <option value="college_dean" {{ old('signatory_type', $user->signatory_type) == 'college_dean' ? 'selected' : '' }}>College Dean</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-xs text-gray-400">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" />
                    </div>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-xl shadow hover:bg-gray-300 transition font-semibold text-sm">Back</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-maroon-700 text-white rounded-xl shadow hover:bg-maroon-800 transition font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
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