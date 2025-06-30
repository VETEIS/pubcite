<x-app-layout>
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
        <div class="w-full max-w-4xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 relative">
                <!-- Header -->
                <div class="flex flex-col items-center text-center mb-8">
                    <x-application-logo class="h-16 w-16 mb-4" />
                    <h2 class="text-3xl font-bold text-maroon-800 mb-2">Manage Your Requests</h2>
                    <p class="text-lg text-gray-600">Manage your incentive requests and track your submissions</p>
                </div>
                <!-- Actions -->
                <div class="flex flex-col md:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('publications.request') }}" class="flex-1 bg-maroon-700 hover:bg-maroon-800 text-white font-semibold py-3 px-6 rounded-lg shadow transition text-center flex flex-col items-center">
                        <svg class="h-7 w-7 mb-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Publications
                    </a>
                    <a href="{{ route('citations.request') }}" class="flex-1 bg-burgundy-700 hover:bg-burgundy-800 text-white font-semibold py-3 px-6 rounded-lg shadow transition text-center flex flex-col items-center">
                        <svg class="h-7 w-7 mb-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Citations
                    </a>
                </div>
                <!-- Requirements/Links Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-gray-50 rounded-lg p-4 flex items-start gap-3">
                        <svg class="h-6 w-6 text-maroon-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Incentive Application Form</p>
                            <p class="text-xs text-gray-500">Submit your publication or citation incentive application</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 flex items-start gap-3">
                        <svg class="h-6 w-6 text-maroon-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Recommendation Letter from the Dean</p>
                            <p class="text-xs text-gray-500">Required for all applications</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 flex items-start gap-3">
                        <svg class="h-6 w-6 text-maroon-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Terminal Report Template</p>
                            <p class="text-xs text-gray-500">Template for terminal report submission</p>
                        </div>
                    </div>
                </div>
                <!-- Requests Table -->
                <div class="mt-10">
                    <h3 class="text-2xl font-semibold text-maroon-800 mb-4">Previously Submitted Requests</h3>
                    @if($requests->isEmpty())
                        <p class="text-gray-500">You have not submitted any requests yet.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Request</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Code</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type of Request</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($requests as $request)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->requested_at)->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $request->request_code }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $request->type }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($request->status === 'endorsed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Endorsed</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
