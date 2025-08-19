<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="max-w-4xl mx-auto">
        <!-- College Header Field -->
        <div class="mb-6">
            <label for="rec_collegeheader" class="block text-sm font-medium text-gray-700 mb-1">College</label>
            <input type="text" name="rec_collegeheader" id="rec_collegeheader" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent"
                   placeholder="e.g., College of Information and Computing">
        </div>
        
        <!-- Letter Header -->
        <div class="mb-8">
            <div class="text-left mb-4">
                <p class="text-sm text-gray-600 mb-1">{{ date('F d, Y') }}</p>
                <input type="hidden" name="rec_date" value="{{ date('F d, Y') }}">
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
                    <x-signatory-select name="rec_facultyname" type="faculty" width="w-48" placeholder="Search faculty..." />
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-4">
                for possible granting of publication incentives.
            </p>
            
            <!-- Summary Table -->
            <div class="mb-6">
                <table class="w-full border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-2 text-left font-medium">Details of the Publication or Bibliographic Entry</th>
                            <th class="border border-gray-300 p-2 text-left font-medium">Indexing</th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-2 text-left font-normal text-xs">(Author, Year of Pub., Article Title, Journal Name, Vol/Issue No., DOI)</th>
                            <th class="border border-gray-300 p-2 text-left font-normal text-xs">(Scopus / WoS / ACI)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 p-2 align-top">
                                <textarea name="details" id="details" rows="4" 
                                          class="w-full px-2 py-1 border-none focus:outline-none focus:ring-0 resize-none"
                                          placeholder="Enter bibliographic details of the publication"></textarea>
                            </td>
                            <td class="border border-gray-300 p-2 align-top">
                                <textarea name="indexing" id="indexing" rows="4" 
                                          class="w-full px-2 py-1 border-none focus:outline-none focus:ring-0 resize-none"
                                          placeholder="Enter indexing information (Scopus / WoS / ACI)"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                <x-signatory-select name="dean" type="college_dean" placeholder="Search college dean..." />
                <div class="border-b-2 border-gray-400 w-48 h-2 mb-2 mx-auto"></div>
                <p class="text-sm text-gray-700 text-center">Dean</p>
            </div>
        </div>
    </div>
</div> 