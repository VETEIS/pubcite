<x-app-layout>
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
    <div class="w-full max-w-3xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 relative">
            <div class="flex flex-col items-center text-center mb-8">
                <x-application-logo class="h-16 w-16 mb-4" />
                <h2 class="text-3xl font-bold text-maroon-800 mb-2">Publication Request</h2>
                <p class="text-lg text-gray-600">Fill out all required forms to submit your publication request</p>
            </div>
            <div x-data="{ tab: 'incentive' }">
                <div class="flex border-b mb-6">
                    <button type="button" class="px-4 py-2 text-sm font-semibold focus:outline-none border-b-2" :class="tab === 'incentive' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500'" @click="tab = 'incentive'">Incentive Application Form</button>
                    <button type="button" class="px-4 py-2 text-sm font-semibold focus:outline-none border-b-2" :class="tab === 'recommendation' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500'" @click="tab = 'recommendation'">Recommendation Letter from the Dean</button>
                    <button type="button" class="px-4 py-2 text-sm font-semibold focus:outline-none border-b-2" :class="tab === 'terminal' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500'" @click="tab = 'terminal'">Terminal Report Template</button>
                </div>
                <form method="POST" action="{{ route('publications.request.store') }}" class="space-y-6">
                    @csrf
                    <div x-show="tab === 'incentive'">
                        <h3 class="text-lg font-semibold mb-2">Incentive Application Form</h3>
                        <input type="text" name="incentive_field" class="w-full border rounded px-3 py-2" placeholder="Sample Incentive Field" required>
                    </div>
                    <div x-show="tab === 'recommendation'">
                        <h3 class="text-lg font-semibold mb-2">Recommendation Letter from the Dean</h3>
                        <input type="text" name="recommendation_field" class="w-full border rounded px-3 py-2" placeholder="Sample Recommendation Field" required>
                    </div>
                    <div x-show="tab === 'terminal'">
                        <h3 class="text-lg font-semibold mb-2">Terminal Report Template</h3>
                        <input type="text" name="terminal_field" class="w-full border rounded px-3 py-2" placeholder="Sample Terminal Report Field" required>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-maroon-700 hover:bg-maroon-800 text-white font-semibold py-2 px-6 rounded-lg shadow transition">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout> 