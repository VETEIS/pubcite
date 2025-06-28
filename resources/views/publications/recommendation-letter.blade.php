<div class="w-full space-y-3">
    <h3 class="text-sm font-semibold text-maroon-800 mb-2">Recommendation Letter from the Dean</h3>
    
    <div class="bg-gray-50 p-3 rounded-lg">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">College/Unit/Department:</label>
                <input type="text" name="rec_collegeheader" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Date:</label>
                <input type="date" name="rec_date" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Faculty Name:</label>
                <input type="text" name="facultyname" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Indexing:</label>
                <input type="text" name="indexing" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" placeholder="Scopus/WoS/ACI" required>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Publication Details:</label>
                <textarea name="details" class="border border-gray-300 w-full text-xs px-2 py-1 rounded" rows="2" placeholder="Author, Year, Title, Journal, Vol/Issue, DOI" required></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Dean's Name:</label>
                <input type="text" name="dean" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg" required>
            </div>
        </div>
    </div>
</div> 