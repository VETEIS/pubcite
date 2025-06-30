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
                <label for="applicant_name" class="block text-sm font-medium text-gray-700 mb-1">Name of the Applicant</label>
                <input type="text" name="applicant_name" id="applicant_name" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter your full name">
            </div>
            
            <div>
                <label for="academic_rank" class="block text-sm font-medium text-gray-700 mb-1">Academic Rank</label>
                <input type="text" name="academic_rank" id="academic_rank" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="e.g., Assistant Professor">
            </div>
            
            <div>
                <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                <select name="employment_status" id="employment_status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent">
                    <option value="">Select employment status</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Temporary">Temporary</option>
                    <option value="Contractual">Contractual</option>
                </select>
            </div>
            
            <div>
                <label for="college" class="block text-sm font-medium text-gray-700 mb-1">College</label>
                <input type="text" name="college" id="college" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter your college">
            </div>
            
            <div>
                <label for="campus" class="block text-sm font-medium text-gray-700 mb-1">Campus</label>
                <input type="text" name="campus" id="campus" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter your campus">
            </div>
            
            <div>
                <label for="field_specialization" class="block text-sm font-medium text-gray-700 mb-1">Field of Specialization</label>
                <input type="text" name="field_specialization" id="field_specialization" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter your field of specialization">
            </div>
            
            <div>
                <label for="years_university" class="block text-sm font-medium text-gray-700 mb-1">No. of Years in the University</label>
                <input type="number" name="years_university" id="years_university" min="0" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                       placeholder="Enter number of years">
            </div>
        </div>
    </div>
    
    <!-- II. Details of the Citation -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">II. Details of the Citation</h4>
        
        <!-- Citing Paper Section -->
        <div class="mb-6">
            <h5 class="text-md font-semibold text-burgundy-700 mb-3">Citing Paper</h5>
            <div class="space-y-4">
                <div>
                    <label for="citing_title" class="block text-sm font-medium text-gray-700 mb-1">Title of the citing paper</label>
                    <textarea name="citing_title" id="citing_title" rows="2" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                              placeholder="Enter the title of the citing paper"></textarea>
                </div>
                
                <div>
                    <label for="citing_authors" class="block text-sm font-medium text-gray-700 mb-1">Authors</label>
                    <input type="text" name="citing_authors" id="citing_authors" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter authors of the citing paper">
                </div>
                
                <div>
                    <label for="citing_journal" class="block text-sm font-medium text-gray-700 mb-1">Title of the Journal</label>
                    <input type="text" name="citing_journal" id="citing_journal" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter the journal title">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="citing_volume" class="block text-sm font-medium text-gray-700 mb-1">Vol./Issue No./Year</label>
                        <input type="text" name="citing_volume" id="citing_volume" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                               placeholder="e.g., Vol. 1, Issue 1, 2024">
                    </div>
                    
                    <div>
                        <label for="citing_pissn" class="block text-sm font-medium text-gray-700 mb-1">P-ISSN</label>
                        <input type="text" name="citing_pissn" id="citing_pissn" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                               placeholder="Enter P-ISSN">
                    </div>
                    
                    <div>
                        <label for="citing_eissn" class="block text-sm font-medium text-gray-700 mb-1">E-ISSN</label>
                        <input type="text" name="citing_eissn" id="citing_eissn" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                               placeholder="Enter E-ISSN">
                    </div>
                </div>
                
                <div>
                    <label for="citing_doi" class="block text-sm font-medium text-gray-700 mb-1">DOI (for e-journal)</label>
                    <input type="text" name="citing_doi" id="citing_doi" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter DOI">
                </div>
                
                <div>
                    <label for="citing_publisher" class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                    <input type="text" name="citing_publisher" id="citing_publisher" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter publisher name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Indexed in</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="indexed_scopus" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                            <span class="text-sm text-gray-700">Scopus</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="indexed_wos" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                            <span class="text-sm text-gray-700">Web of Science</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="indexed_aci" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                            <span class="text-sm text-gray-700">ACI</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="indexed_pubmed" class="mr-2 text-burgundy-600 focus:ring-burgundy-500">
                            <span class="text-sm text-gray-700">PubMed</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="citing_citescore" class="block text-sm font-medium text-gray-700 mb-1">Scopus CiteScore (if applicable)</label>
                    <input type="text" name="citing_citescore" id="citing_citescore" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter CiteScore">
                </div>
            </div>
        </div>
        
        <!-- Cited Paper Section -->
        <div class="mb-6">
            <h5 class="text-md font-semibold text-burgundy-700 mb-3">Cited Paper</h5>
            <div class="space-y-4">
                <div>
                    <label for="cited_title" class="block text-sm font-medium text-gray-700 mb-1">Title of the cited paper</label>
                    <textarea name="cited_title" id="cited_title" rows="2" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                              placeholder="Enter the title of the cited paper"></textarea>
                </div>
                
                <div>
                    <label for="cited_coauthors" class="block text-sm font-medium text-gray-700 mb-1">Co-authors (if any)</label>
                    <input type="text" name="cited_coauthors" id="cited_coauthors" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter co-authors of the cited paper">
                </div>
                
                <div>
                    <label for="cited_journal" class="block text-sm font-medium text-gray-700 mb-1">Title of the Journal</label>
                    <input type="text" name="cited_journal" id="cited_journal" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="Enter the journal title of the cited paper">
                </div>
                
                <div>
                    <label for="cited_volume" class="block text-sm font-medium text-gray-700 mb-1">Vol./Issue No./Year</label>
                    <input type="text" name="cited_volume" id="cited_volume" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                           placeholder="e.g., Vol. 1, Issue 1, 2024">
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
                        <input type="text" name="faculty_name" id="faculty_name" required 
                        class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent"
                        placeholder="Enter faculty name">
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Signature over Printed Name of the Faculty</p>
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
                            <input type="text" name="center_manager" id="center_manager" 
                                   class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent"
                                   placeholder="Enter center manager name">
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
                            <input type="text" name="dean_name" id="dean_name" required 
                                   class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent"
                                   placeholder="Enter dean name">
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
                            <p class="text-sm text-gray-700 text-center">RANDY A. TUDY, PhD</p>
                            <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Deputy Director, Publication Unit</p>
                        </div>
                        <div>
                            <div class="border-b-2 border-gray-400 h-8 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Date</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="h-1"></div>
                            <p class="text-sm text-gray-700 text-center">MERLINA H. JURUENA, PhD</p>
                            <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                            <p class="text-sm text-gray-600 text-center">Director, Research and Development Division</p>
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