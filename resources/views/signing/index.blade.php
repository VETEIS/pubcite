<x-app-layout>
    <div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
        <div class="flex-1 flex items-center justify-center h-full m-0">
            <div class="w-full h-full max-w-5xl mx-auto rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-6 flex flex-col items-stretch">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-maroon-900">For Signing</h1>
                </div>
                
                <!-- Floating Notifications -->
                @if(session('success'))
                <div id="success-notification" class="fixed top-20 right-4 z-[60] bg-green-600 text-white px-4 py-2 rounded shadow-lg backdrop-blur border border-green-500/20 transform transition-all duration-300 opacity-100 translate-x-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif
                
                @if(session('error'))
                <div id="error-notification" class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow-lg backdrop-blur border border-red-500/20 transform transition-all duration-300 opacity-100 translate-x-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-1 min-h-0">
                    <!-- Left: Signature upload -->
                    <div class="bg-white/70 backdrop-blur border border-white/40 rounded-xl shadow p-4">
                        <h2 class="text-sm font-bold text-maroon-900 mb-2">Digital Signature (PNG)</h2>
                        <div class="flex flex-col items-center gap-3">
                            @if($signatureUrl)
                                <img src="{{ $signatureUrl }}" alt="Signature" class="max-h-24 object-contain border rounded bg-white p-2">
                            @else
                                <div class="text-xs text-gray-500">No signature uploaded</div>
                            @endif
                            <form method="POST" action="{{ route('signing.signature') }}" enctype="multipart/form-data" class="w-full">
                                @csrf
                                <input type="file" name="signature" accept="image/png" class="w-full text-xs border rounded p-1" required>
                                <button type="submit" class="mt-2 w-full inline-flex items-center justify-center px-3 py-1.5 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 text-xs font-semibold">Upload</button>
                            </form>
                            <p class="text-[11px] text-gray-500">Use a transparent PNG for best results. Max 2MB.</p>
                        </div>
                    </div>
                    <!-- Right: Requests list -->
                    <div class="lg:col-span-2 bg-white/70 backdrop-blur border border-white/40 rounded-xl shadow p-4 flex flex-col min-h-0">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-sm font-bold text-maroon-900">Requests requiring your signature</h2>
                        </div>
                        <div class="overflow-y-auto min-h-0">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-white/50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-bold text-maroon-900">Date</th>
                                        <th class="px-3 py-2 text-left font-bold text-maroon-900">Code</th>
                                        <th class="px-3 py-2 text-left font-bold text-maroon-900">Type</th>
                                        <th class="px-3 py-2 text-left font-bold text-maroon-900">As</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($requests as $req)
                                        <tr class="hover:bg-white/40">
                                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($req['requested_at'])->format('M d, Y H:i') }}</td>
                                            <td class="px-3 py-2 font-bold text-maroon-900">{{ $req['request_code'] }}</td>
                                            <td class="px-3 py-2">{{ $req['type'] }}</td>
                                            <td class="px-3 py-2">{{ str_replace('_',' ', ucfirst($req['matched_role'])) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-8 text-center text-gray-500">No requests found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-hide notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successNotification = document.getElementById('success-notification');
            const errorNotification = document.getElementById('error-notification');
            
            if (successNotification) {
                setTimeout(() => {
                    successNotification.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => {
                        if (document.body.contains(successNotification)) {
                            document.body.removeChild(successNotification);
                        }
                    }, 300);
                }, 5000);
            }
            
            if (errorNotification) {
                setTimeout(() => {
                    errorNotification.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => {
                        if (document.body.contains(errorNotification)) {
                            document.body.removeChild(errorNotification);
                        }
                    }, 300);
                }, 5000);
            }
        });
    </script>
</x-app-layout> 