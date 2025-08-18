<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Floating Notifications -->
    @if(session('success'))
    <div id="success-notification" class="fixed top-20 right-4 z-[60] bg-green-600 text-white px-4 py-2 rounded shadow-lg backdrop-blur border border-green-500/20 transform transition-all duration-300 opacity-100 translate-x-0">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div id="error-notification" class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow-lg backdrop-blur border border-red-500/20 transform transition-all duration-300 opacity-100 translate-x-0">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                <input type="text" name="college" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter your college">
            </div>
        </div>
    </div>

    <!-- II. Publication Details -->
    <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <h4 class="text-lg font-semibold text-burgundy-800 mb-4 border-b-2 border-burgundy-200 pb-2">II. Publication Details</h4>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bibliographic Entry (APA Format)</label>
            <textarea name="bibentry" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter the bibliographic entry"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ISSN (P-ISSN/E-ISSN)</label>
                <input type="text" name="issn" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" required placeholder="Enter ISSN">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DOI</label>
                <input type="text" name="doi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-500 focus:border-transparent" placeholder="Enter DOI">
            </div>
        </div>
        <div class="mb-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Indexed in</label>
            <div class="flex flex-wrap gap-4 mt-1">
                <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="Scopus" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> Scopus</label>
                <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="Web of Science" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> Web of Science</label>
                <label class="flex items-center text-sm"><input type="radio" name="indexed_in" value="ACI" class="mr-2 focus:ring-burgundy-500 text-burgundy-600"> ACI</label>
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
                        
                        <x-signatory-select name="facultyname" type="faculty" placeholder="Search faculty..." />
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
                        <x-signatory-select name="centermanager" type="center_manager" placeholder="Search center manager..." />
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
                        <x-signatory-select name="collegedean" type="college_dean" placeholder="Search college dean..." />
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
                        <p class="text-sm text-gray-700 text-center">{{ \App\Models\Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD') }}</p>
                        <div class="border-b-2 border-gray-400 h-2 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">{{ \App\Models\Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit') }}</p>
                    </div>
                    <div>
                        <div class="border-b-2 border-gray-400 h-8 mb-2"></div>
                        <p class="text-sm text-gray-600 text-center">Date</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
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

<script>
    // Auto-hide notifications after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successNotification = document.getElementById('success-notification');
        const errorNotification = document.getElementById('error-notification');
        
        if (successNotification) {
            setTimeout(() => {
                successNotification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(successNotification)) {
                        document.body.removeChild(successNotification);
                    }
                }, 300);
            }, 5000);
        }
        
        if (errorNotification) {
            setTimeout(() => {
                errorNotification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(errorNotification)) {
                        document.body.removeChild(errorNotification);
                    }
                }, 300);
            }, 5000);
        }
    });
</script> 