<!-- Modern Publication Recommendation Letter Form -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-xl border border-maroon-200">
        <h2 class="text-2xl font-bold text-maroon-800 mb-2">University of Southeastern Philippines</h2>
        <h3 class="text-lg font-semibold text-maroon-700">Recommendation Letter for Publication Incentive</h3>
    </div>
    
    <!-- Letter Form Section -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <!-- College Header Field -->
        <div class="mb-6">
            <label for="rec_collegeheader" class="block text-sm font-medium text-gray-700 mb-2">
                College <span class="text-red-500">*</span>
            </label>
            <input type="text" name="rec_collegeheader" id="rec_collegeheader" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200"
                   placeholder="e.g., College of Information and Computing">
        </div>
        
        <!-- Letter Header -->
        <div class="mb-8">
            <div class="text-left mb-4">
                <p class="text-sm text-gray-600 mb-1">{{ date('F d, Y') }}</p>
                <input type="hidden" name="date" value="{{ date('F d, Y') }}">
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-maroon-800 mb-1">{{ \App\Models\Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD') }}</h3>
                <p class="text-sm text-gray-700">{{ \App\Models\Setting::get('official_rdd_director_title', 'Director, Research and Development Division') }}</p>
                <p class="text-sm text-gray-700">This University</p>
            </div>
            
            <div class="mb-6">
                <p class="text-sm text-gray-700 mb-1"><strong>Thru:</strong></p>
                <p class="text-sm text-gray-700 mb-1">{{ \App\Models\Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD') }}</p>
                <p class="text-sm text-gray-700">{{ \App\Models\Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit') }}</p>
            </div>
        </div>
        
        <!-- Letter Body -->
        <div class="mb-8">
            <p class="text-sm text-gray-700 mb-4">Greetings in the name of research advancement!</p>
            
            <div class="flex items-baseline gap-2 text-sm text-gray-700 mb-1">
                <span>This is to endorse to your office the research publication of</span>
                <div class="inline-block">
                    <x-signatory-select name="rec_faculty_name" type="faculty" width="w-48" placeholder="Search faculty..." />
                </div>
            </div>
            
            <p class="text-sm text-gray-700 mb-4">
                for possible granting of publication incentives. The details of the published paper is summarized below:
            </p>
            
            <!-- Summary Table -->
            <div class="mb-6">
                <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border-b border-gray-300 p-4 text-left font-medium text-gray-900">Details of the Published Article (Bibliographic Entry)</th>
                                <th class="border-b border-gray-300 p-4 text-left font-medium text-gray-900">Indexing</th>
                            </tr>
                            <tr class="bg-gray-50">
                                <th class="border-b border-gray-300 p-4 text-left font-normal text-xs text-gray-600">(Author, Year of Pub., Article Title, Journal Name, Vol/Issue No., DOI)</th>
                                <th class="border-b border-gray-300 p-4 text-left font-normal text-xs text-gray-600">(Scopus / WoS / ACI)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-4 align-top">
                    <textarea name="rec_publication_details" id="rec_publication_details" rows="4" 
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent resize-none"
                              placeholder="Enter bibliographic details of the published article" required></textarea>
                                </td>
                                <td class="p-4 align-top">
                                    <textarea name="rec_indexing_details" id="rec_indexing_details" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent resize-none"
                                              placeholder="Enter indexing information (Scopus / WoS / ACI)" required></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="text-sm text-gray-700 mb-4">
                Attached also is the application form which includes further details of the published article.
            </p>
            
            <p class="text-sm text-gray-700 mb-4">
                As the Dean of the College, I believe that such output deserves proper acknowledgment as it helps the institution attain the vision of becoming a premier research university in the ASEAN and beyond.
            </p>
            
            <p class="text-sm text-gray-700 mb-4">Thank you very much.</p>
        </div>
        
        <!-- Letter Closing -->
        <div class="mb-8">
            <p class="text-sm text-gray-700 mb-6">Sincerely,</p>
            
            <div class="mb-4">
                <div class="h-1"></div>
                <x-signatory-select name="rec_dean_name" type="college_dean" placeholder="Search college dean..." />
                <div class="border-b-2 border-gray-400 w-48 h-2 mb-2 mx-auto"></div>
                <p class="text-sm text-gray-700 text-center">Dean</p>
            </div>
        </div>
    </div>
</div>
