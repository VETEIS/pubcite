<x-app-layout>
    <div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
        @include('admin.partials.sidebar')
        <div class="flex-1 flex items-center justify-center h-full m-0">
            <div class="w-full h-full max-w-3xl mx-auto rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-6 flex flex-col items-stretch">
                <h1 class="text-2xl font-bold text-maroon-900 mb-4">Settings</h1>
                @if(session('success'))
                    <div class="mb-3 text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2 text-sm">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deputy Director Name</label>
                            <input type="text" name="official_deputy_director_name" value="{{ old('official_deputy_director_name', $official_deputy_director_name) }}" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deputy Director Title</label>
                            <input type="text" name="official_deputy_director_title" value="{{ old('official_deputy_director_title', $official_deputy_director_title) }}" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">RDD Director Name</label>
                            <input type="text" name="official_rdd_director_name" value="{{ old('official_rdd_director_name', $official_rdd_director_name) }}" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">RDD Director Title</label>
                            <input type="text" name="official_rdd_director_title" value="{{ old('official_rdd_director_title', $official_rdd_director_title) }}" class="w-full border rounded-lg px-3 py-2 focus:border-maroon-500 focus:ring-maroon-500" required>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-maroon-700 text-white rounded-xl shadow hover:bg-maroon-800 transition font-semibold text-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 