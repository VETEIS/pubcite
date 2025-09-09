<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-burgundy-800 mb-2">University of Southeastern Philippines</h2>
        <h3 class="text-xl font-semibold text-burgundy-700">Application Form for Research Citation Incentive</h3>
    </div>
    
    <!-- I. Personal Profile -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">I. Personal Profile</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name of the Applicant</label>
                <input type="text" name="name" id="name" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter your full name">
            </div>
            <div>
                <label for="rank" class="block text-sm font-medium text-gray-700 mb-1">Academic Rank</label>
                <input type="text" name="rank" id="rank" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="e.g., Assistant Professor">
            </div>
            <div>
                <label for="college" class="block text-sm font-medium text-gray-700 mb-1">College</label>
                <input type="text" name="college" id="college" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter your college">
            </div>
        </div>
    </div>
    
    <!-- II. Details of the Citation -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">II. Details of the Citation</h4>
        <div class="space-y-4">
            <div>
                <label for="bibentry" class="block text-sm font-medium text-gray-700 mb-1">Bibliographic Entry (APA Format)</label>
                <textarea name="bibentry" id="bibentry" rows="2" required 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                          placeholder="Enter the bibliographic entry"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="issn" class="block text-sm font-medium text-gray-700 mb-1">P-ISSN/E-ISSN</label>
                    <input type="text" name="issn" id="issn" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter ISSN">
                </div>
                <div>
                    <label for="doi" class="block text-sm font-medium text-gray-700 mb-1">DOI (for e-journal)</label>
                    <input type="text" name="doi" id="doi" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter DOI">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Indexed in</label>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="scopus" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                        <span class="text-sm text-gray-700">Scopus</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="wos" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                        <span class="text-sm text-gray-700">Web of Science</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="aci" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                        <span class="text-sm text-gray-700">ACI</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- III. Attachments -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">III. Attachments</h4>
        <div class="bg-burgundy-50 border border-burgundy-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-burgundy-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h5 class="text-sm font-medium text-burgundy-800">Required Documents</h5>
                    <p class="text-sm text-burgundy-700 mt-1">
                        Please upload the required documents in the <strong>Upload</strong> tab (Step 2). The following documents are required:
                    </p>
                    <ul class="text-sm text-burgundy-700 mt-2 space-y-2">
                        <li class="flex items-start">
                            <svg class="h-4 w-4 text-burgundy-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <span>Recommendation Letter approved by the College Dean</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 text-burgundy-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <span>Copy of the citing article (PDF copy)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 text-burgundy-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <span>Copy of the Cover and Table of Contents of the citing article's journal issue (PDF copy)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 text-burgundy-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <span>Copy of the cited article (PDF copy)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 text-burgundy-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <span>Copy of the Cover and Table of Contents of the cited article's journal issue (PDF copy)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- IV. Declaration -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">IV. Declaration</h4>
        
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-gray-700 leading-relaxed">
                I hereby declare that all the details in this application form are accurate. I have not hidden any relevant information as must necessarily brought to the attention of the University. I will satisfy all the terms and conditions prescribed in the guidelines of the University for research paper publication.
            </p>
        </div>
        
        <!-- Signature Section -->
        <div class="space-y-6">
            <!-- Faculty Signature -->
            <div class="border-t border-gray-200 pt-4">
                <h5 class="text-md font-semibold text-burgundy-700 mb-3">Signed by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="h-1"></div>
                        <x-signatory-select name="faculty_name" type="faculty" placeholder="Search faculty..." />
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">SignatureAAAAAA over Printed Name of the Faculty</p>
                    </div>
                    <div>
                        <div class="h-5"></div>
                            <p class="text-sm text-gray-600 text-center">{{ date('F d, Y') }}</p>
                            <input type="hidden" name="date" value="{{ date('F d, Y') }}">
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
            </div>
            
            <!-- Noted by Section -->
            <div class="border-t border-gray-200 pt-4">
                <h5 class="text-md font-semibold text-burgundy-700 mb-3">Noted by:</h5>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="h-1"></div>
                            <x-signatory-select name="center_manager" type="center_manager" placeholder="Search center manager..." />
                            <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Research Center Manager</p>
                        </div>
                        <div>
                            <div class="border-b-2 border-gray-400 h-12 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Date</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="h-1"></div>
                            <x-signatory-select name="dean_name" type="college_dean" placeholder="Search college dean..." />
                            <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">College Dean</p>
                        </div>
                        <div>
                            <div class="border-b-2 border-gray-400 h-12 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Date</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Approved by Section -->
            <div class="border-t border-gray-200 pt-4">
                <h5 class="text-md font-semibold text-burgundy-700 mb-3">Approved by:</h5>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="h-1"></div>
                            <p class="text-sm text-gray-700 text-center">{{ \App\Models\Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD') }}</p>
                            <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">{{ \App\Models\Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit') }}</p>
                        </div>
                        <div>
                            <div class="border-b-2 border-gray-400 h-8 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Date</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="h-1"></div>
                            <p class="text-sm text-gray-700 text-center">{{ \App\Models\Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD') }}</p>
                            <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">{{ \App\Models\Setting::get('official_rdd_director_title', 'Director, Research and Development Division') }}</p>
                        </div>
                        <div>
                            <div class="border-b-2 border-gray-400 h-8 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Date</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 