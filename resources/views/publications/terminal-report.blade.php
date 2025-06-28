<div class="w-full space-y-3">
    <h3 class="text-sm font-semibold text-maroon-800 mb-2">Terminal Report Template</h3>
    
    <div class="bg-gray-50 p-3 rounded-lg">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Title:</label>
                <input type="text" name="title" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Author/s:</label>
                <input type="text" name="author" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Duration:</label>
                <input type="text" name="duration" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Abstract:</label>
                <textarea name="abstract" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" required></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Introduction:</label>
                <textarea name="introduction" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" placeholder="Background, GAP, RRL, Framework" required></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Methodology:</label>
                <textarea name="methodology" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" required></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Results & Discussion:</label>
                <textarea name="rnd" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" required></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Conclusion & Recommendation:</label>
                <textarea name="car" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" required></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">References:</label>
                <textarea name="references" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" required></textarea>
            </div>
            
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium mb-0.5">Appendices:</label>
                <textarea name="appendices" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2"></textarea>
            </div>
        </div>
    </div>
</div> 