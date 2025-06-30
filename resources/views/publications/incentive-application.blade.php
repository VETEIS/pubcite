<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
    @if(session('error'))
        <div class="mb-2 p-2 bg-red-100 text-red-700 rounded text-xs">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-2 p-2 bg-green-100 text-green-700 rounded text-xs">{{ session('success') }}</div>
    @endif
    
    <!-- Compact Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-burgundy-800 mb-2">University of Southeastern Philippines</h2>
        <h3 class="text-xl font-semibold text-burgundy-700">Application Form for Research Publication Incentive</h3>
    </div>

    <!-- I. Personal Profile -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">I. Personal Profile</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name of the Applicant</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter your full name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Rank</label>
                <input type="text" name="academicrank" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="e.g., Assistant Professor">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                <select name="employmentstatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent">
                    <option value="">Select employment status</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Temporary">Temporary</option>
                    <option value="Contractual">Contractual</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                <input type="text" name="college" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter your college">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Campus</label>
                <input type="text" name="campus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter your campus">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Field of Specialization</label>
                <input type="text" name="field" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter your field of specialization">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. of Years in the University</label>
                <input type="number" name="years" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter number of years">
            </div>
        </div>
    </div>

    <!-- II. Publication Details -->
    <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">II. Publication Details</h4>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Paper Title</label>
            <textarea name="papertitle" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter the title of the paper"></textarea>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Co-authors</label>
            <input type="text" name="coauthors" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter co-authors (if any)">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Journal Title</label>
                <input type="text" name="journaltitle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" required placeholder="Enter the journal title">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vol/Issue/Year</label>
                <input type="text" name="version" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" required placeholder="e.g., Vol. 1, Issue 1, 2024">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">P-ISSN</label>
                <input type="text" name="pissn" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter P-ISSN">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-ISSN</label>
                <input type="text" name="eissn" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter E-ISSN">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DOI</label>
                <input type="text" name="doi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter DOI">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                <input type="text" name="publisher" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" required placeholder="Enter publisher name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CiteScore</label>
                <input type="text" name="citescore" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter CiteScore">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publication Type</label>
                <div class="flex flex-wrap gap-4 mt-1">
                    <label class="flex items-center text-sm"><input type="radio" name="type" value="Regional" required class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> Regional</label>
                    <label class="flex items-center text-sm"><input type="radio" name="type" value="National" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> National</label>
                    <label class="flex items-center text-sm"><input type="radio" name="type" value="International" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> International</label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Indexed in</label>
                <div class="flex flex-wrap gap-4 mt-1">
                    <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="Scopus" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> Scopus</label>
                    <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="Web of Science" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> Web of Science</label>
                    <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="ACI" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> ACI</label>
                    <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="PubMed" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> PubMed</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Assistance Details -->
    <div class="bg-gray-50 p-3 rounded-lg">
        <h4 class="font-semibold text-maroon-800 text-sm mb-2">III. Assistance Request</h4>
        <div>
            <label class="block text-xs font-medium mb-0.5">Particulars / Amount:</label>
            <textarea name="particulars" class="border border-gray-300 w-full text-xs px-2 py-1 rounded focus:border-maroon-700 focus:ring-maroon-700 placeholder-maroon-300" rows="2" required></textarea>
        </div>
    </div>

    <!-- IV. Declaration -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">IV. Declaration</h4>
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <p class="text-sm text-gray-700 leading-relaxed">
                I hereby declare that all the details in this application form are accurate. I have not hidden any relevant information as must necessarily brought to the attention of the University. I will satisfy all the terms and conditions prescribed in the guidelines of the University for research paper publication.
            </p>
        </div>
        <!-- Signature Section -->
        <div class="space-y-8">
            <!-- Faculty Signature -->
            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-md font-semibold text-burgundy-700 mb-3">Signed by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <input type="text" name="facultyname" class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent" placeholder="Enter faculty name" required>
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
            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-md font-semibold text-burgundy-700 mb-3">Noted by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <input type="text" name="centermanager" class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent" placeholder="Enter center manager name">
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Research Center Manager</p>
                    </div>
                    <div>
                        <div class="border-b-2 border-gray-400 h-12 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <input type="text" name="collegedean" class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent" placeholder="Enter dean name">
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">College Dean</p>
                    </div>
                    <div>
                        <div class="border-b-2 border-gray-400 h-12 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
            </div>
            <!-- Approved by Section -->
            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-md font-semibold text-burgundy-700 mb-3">Approved by:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-700 text-center">RANDY A. TUDY, PhD</p>
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Deputy Director, Publication Unit</p>
                    </div>
                    <div>
                        <div class="border-b-2 border-gray-400 h-8 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
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