<x-app-layout>
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <!-- Success Message -->
            <h2 class="text-2xl font-bold text-burgundy-800 mb-4">Citation Request Submitted Successfully!</h2>
            <p class="text-gray-600 mb-6">Your citation request has been submitted and is now under review.</p>
            
            <!-- Request Code -->
            <div class="bg-burgundy-50 border border-burgundy-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-burgundy-700 mb-2">Your Request Code:</p>
                <p class="text-xl font-mono font-bold text-burgundy-800">{{ $requestCode }}</p>
                <p class="text-xs text-burgundy-600 mt-2">Please save this code for future reference</p>
            </div>
            
            <!-- Next Steps -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <h3 class="font-semibold text-gray-800 mb-3">What happens next?</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <span class="w-2 h-2 bg-burgundy-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                        <span>Your request will be reviewed by the Publication Unit</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-2 h-2 bg-burgundy-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                        <span>You will receive updates on the status of your request</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-2 h-2 bg-burgundy-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                        <span>Once approved, you will be notified via email</span>
                    </li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('dashboard') }}" 
                   class="bg-burgundy-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-burgundy-800 transition-colors">
                    Go to Dashboard
                </a>
                <a href="{{ route('citations.request') }}" data-turbo="false"
                   class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    Submit Another Request
                </a>
            </div>
        </div>
    </div>
</div>
</x-app-layout> 