<!-- Modern Incentive Application Form for Citations -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="py-6 bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-xl border border-maroon-200">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-maroon-800 mb-1">University of Southeastern Philippines</h2>
            <h3 class="text-xl font-semibold text-maroon-700">Application Form for Research Citation Incentive</h3>
        </div>
    </div>

    <!-- I. Personal Profile -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-maroon-600 to-maroon-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900">I. Personal Profile</h4>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Name of the Applicant <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                       placeholder="Enter your full name"
                       value="{{ old('name', $request->form_data['name'] ?? '') }}">
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Academic Rank</label>
                <input type="text" name="rank" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                       placeholder="e.g., Assistant Professor"
                       value="{{ old('rank', $request->form_data['rank'] ?? '') }}">
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    College <span class="text-red-500">*</span>
                </label>
                <input type="text" name="college" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                       placeholder="Enter your college"
                       value="{{ old('college', $request->form_data['college'] ?? '') }}">
            </div>
        </div>
    </div>

    <!-- II. Details of the Citation -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-maroon-600 to-maroon-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900">II. Details of the Citation</h4>
        </div>
        
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Bibliographic Entry (APA Format) <span class="text-red-500">*</span>
                </label>
                <textarea name="bibentry" rows="3" required 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                          placeholder="Enter the bibliographic entry">{{ old('bibentry', $request->form_data['bibentry'] ?? '') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        P-ISSN/E-ISSN <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="issn" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                           required placeholder="Enter ISSN"
                           value="{{ old('issn', $request->form_data['issn'] ?? '') }}">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">DOI (for e-journal)</label>
                    <input type="text" name="doi" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Enter DOI"
                           value="{{ old('doi', $request->form_data['doi'] ?? '') }}">
                </div>
            </div>
            
            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-700">
                    Indexed in <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Scopus Card -->
                    <label class="relative cursor-pointer group">
                        <input type="checkbox" name="scopus" value="1" 
                               class="sr-only peer"
                               {{ old('scopus', $request->form_data['scopus'] ?? '') ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 group-hover:border-maroon-300 peer-checked:border-maroon-500 peer-checked:bg-maroon-50 peer-checked:shadow-md">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-full flex items-center justify-center group-hover:from-maroon-200 group-hover:to-maroon-300 peer-checked:from-maroon-500 peer-checked:to-maroon-600 transition-all duration-200">
                                    <svg class="w-4 h-4 text-maroon-600 peer-checked:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-maroon-600 peer-checked:text-maroon-700 transition-colors">Scopus</span>
                            </div>
                        </div>
                    </label>

                    <!-- Web of Science Card -->
                    <label class="relative cursor-pointer group">
                        <input type="checkbox" name="wos" value="1" 
                               class="sr-only peer"
                               {{ old('wos', $request->form_data['wos'] ?? '') ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 group-hover:border-maroon-300 peer-checked:border-maroon-500 peer-checked:bg-maroon-50 peer-checked:shadow-md">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-full flex items-center justify-center group-hover:from-maroon-200 group-hover:to-maroon-300 peer-checked:from-maroon-500 peer-checked:to-maroon-600 transition-all duration-200">
                                    <svg class="w-4 h-4 text-maroon-600 peer-checked:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-maroon-600 peer-checked:text-maroon-700 transition-colors">Web of Science</span>
                            </div>
                        </div>
                    </label>

                    <!-- ACI Card -->
                    <label class="relative cursor-pointer group">
                        <input type="checkbox" name="aci" value="1" 
                               class="sr-only peer"
                               {{ old('aci', $request->form_data['aci'] ?? '') ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 group-hover:border-maroon-300 peer-checked:border-maroon-500 peer-checked:bg-maroon-50 peer-checked:shadow-md">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-full flex items-center justify-center group-hover:from-maroon-200 group-hover:to-maroon-300 peer-checked:from-maroon-500 peer-checked:to-maroon-600 transition-all duration-200">
                                    <svg class="w-4 h-4 text-maroon-600 peer-checked:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-maroon-600 peer-checked:text-maroon-700 transition-colors">ACI</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Additional Citation Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Title of Cited Work <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="citedtitle" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Enter the title of the cited work"
                           value="{{ old('citedtitle', $request->form_data['citedtitle'] ?? '') }}">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Journal of Cited Work <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="citedjournal" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                           placeholder="Enter the journal name"
                           value="{{ old('citedjournal', $request->form_data['citedjournal'] ?? '') }}">
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Bibliographic Entry of Cited Work <span class="text-red-500">*</span>
                </label>
                <textarea name="citedbibentry" rows="2" required 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all duration-200" 
                          placeholder="Enter the bibliographic entry of the cited work">{{ old('citedbibentry', $request->form_data['citedbibentry'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- III. Required Files -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-blue-800">III. Required Files</h4>
        </div>
        
        <div class="space-y-4">
            <div class="bg-white/50 backdrop-blur-sm rounded-lg p-4 border border-blue-200">
                <h5 class="text-sm font-semibold text-blue-800 mb-3">Please prepare the following documents:</h5>
                <ul class="text-sm text-blue-700 space-y-2">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                        </svg>
                        <span>Recommendation Letter (PDF)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                        </svg>
                        <span>Published Article (PDF)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                        </svg>
                        <span>Peer Reviewed Document (PDF)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                        </svg>
                        <span>Terminal Report (PDF)</span>
                    </li>
                </ul>
                <p class="text-xs text-blue-600 mt-3">Note: These files will be uploaded in the Upload Documents section.</p>
            </div>
        </div>
    </div>

    <!-- IV. Declaration -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-maroon-600 to-maroon-700 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900">IV. Declaration</h4>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <p class="text-sm text-gray-700 leading-relaxed">
                I hereby declare that all the details in this application form are accurate. I have not hidden any relevant information as must necessarily brought to the attention of the University. I will satisfy all the terms and conditions prescribed in the guidelines of the University for research paper citation.
            </p>
        </div>
        
        <!-- Signature Section -->
        <div class="space-y-8">
            <!-- Faculty Signature -->
            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-md font-semibold text-maroon-700 mb-4">Signed by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column (Signatory) -->
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                            <x-signatory-select name="faculty_name" type="faculty" placeholder="Search faculty..." />
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Signature over Printed Name</p>
                    </div>

                    <!-- Right Column (Date) -->
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
            </div>

            <!-- Noted by Section -->
            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-md font-semibold text-maroon-700 mb-4">Noted by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Center Manager -->
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                            <x-signatory-select name="center_manager" type="center_manager" placeholder="Search center manager..." />
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Research Center Manager</p>
                    </div>
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
                
                <!-- College Dean -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                            <x-signatory-select name="dean_name" type="college_dean" placeholder="Search college dean..." />
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">College Dean</p>
                    </div>
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
            </div>

            <!-- Approved by Section -->
            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-md font-semibold text-maroon-700 mb-4">Approved by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                            <p class="text-sm text-gray-700 text-center">{{ \App\Models\Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD') }}</p>
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">{{ \App\Models\Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit') }}</p>
                    </div>
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                            <p class="text-sm text-gray-700 text-center">{{ \App\Models\Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD') }}</p>
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">{{ \App\Models\Setting::get('official_rdd_director_title', 'Director, Research and Development Division') }}</p>
                    </div>
                    <div class="space-y-2">
                        <div class="h-12 flex items-center justify-center">
                        </div>
                        <div class="border-b-2 border-gray-400 h-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>