<!-- Modern Terminal Report Form -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-xl border border-maroon-200">
        <h2 class="text-2xl font-bold text-maroon-800 mb-2">University of Southeastern Philippines</h2>
        <h3 class="text-lg font-semibold text-maroon-700">Terminal Report for Research Publication</h3>
    </div>
    
    <!-- Terminal Report Form Section -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-maroon-600 to-maroon-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900">Terminal Report Template</h4>
        </div>
        
        <!-- Basic Information Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-2">
                <label for="title" class="block text-sm font-medium text-gray-700">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200"
                       placeholder="Enter the research title">
            </div>
            <div class="space-y-2">
                <label for="author" class="block text-sm font-medium text-gray-700">
                    Author/s <span class="text-red-500">*</span>
                </label>
                <input type="text" name="author" id="author" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200"
                       placeholder="Enter author names">
            </div>
            <div class="space-y-2">
                <label for="duration" class="block text-sm font-medium text-gray-700">
                    Duration <span class="text-red-500">*</span>
                </label>
                <input type="text" name="duration" id="duration" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200"
                       placeholder="e.g., 6 months, 1 year">
            </div>
        </div>
        
        <!-- Research Content Fields -->
        <div class="space-y-6">
            <div class="space-y-2">
                <label for="abstract" class="block text-sm font-medium text-gray-700">
                    Abstract <span class="text-red-500">*</span>
                </label>
                <textarea name="abstract" id="abstract" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Enter the research abstract"></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="introduction" class="block text-sm font-medium text-gray-700">
                    Introduction <span class="text-red-500">*</span>
                </label>
                <textarea name="introduction" id="introduction" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Background, GAP, RRL, Framework"></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="methodology" class="block text-sm font-medium text-gray-700">
                    Methodology <span class="text-red-500">*</span>
                </label>
                <textarea name="methodology" id="methodology" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Enter research methodology"></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="rnd" class="block text-sm font-medium text-gray-700">
                    Results & Discussion <span class="text-red-500">*</span>
                </label>
                <textarea name="rnd" id="rnd" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Enter results and discussion"></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="car" class="block text-sm font-medium text-gray-700">
                    Conclusion & Recommendations <span class="text-red-500">*</span>
                </label>
                <textarea name="car" id="car" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Enter conclusions and recommendations"></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="references" class="block text-sm font-medium text-gray-700">
                    References <span class="text-red-500">*</span>
                </label>
                <textarea name="references" id="references" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Enter references"></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="appendices" class="block text-sm font-medium text-gray-700">
                    Appendices
                </label>
                <textarea name="appendices" id="appendices" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200 resize-none"
                          placeholder="Enter appendices (optional)"></textarea>
            </div>
        </div>
    </div>
</div>
