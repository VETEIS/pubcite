<div class="w-full space-y-4">
    @if(session('error'))
        <div class="mb-2 p-2 bg-red-100 text-red-700 rounded text-xs">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-2 p-2 bg-green-100 text-green-700 rounded text-xs">{{ session('success') }}</div>
    @endif
    
    <!-- Compact Header -->
    <div class="text-center mb-3">
        <h2 class="text-sm font-bold mb-1">University of Southeastern Philippines</h2>
        <input type="text" name="collegeheader" class="border-b border-gray-400 w-2/3 text-center text-xs rounded-lg focus:border-maroon-500 focus:ring-maroon-500" placeholder="College/Unit/Department" required>
        <h3 class="text-xs font-semibold mt-1">Application Form for Research Publication Incentive</h3>
    </div>

    <!-- Personal Profile - Ultra Compact -->
    <div class="bg-gray-50 p-3 rounded-lg">
        <h4 class="font-semibold text-maroon-800 text-sm mb-2">I. Personal Profile</h4>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Name:</label>
                <input type="text" name="name" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Academic Rank:</label>
                <input type="text" name="academicrank" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Employment Status:</label>
                <input type="text" name="employmentstatus" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Years in University:</label>
                <input type="number" name="years" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" min="0" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">College:</label>
                <input type="text" name="college" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Campus:</label>
                <input type="text" name="campus" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Field of Specialization:</label>
                <input type="text" name="field" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
        </div>
    </div>

    <!-- Publication Details - Ultra Compact -->
    <div class="bg-gray-50 p-3 rounded-lg">
        <h4 class="font-semibold text-maroon-800 text-sm mb-2">II. Publication Details</h4>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Paper Title:</label>
                <textarea name="papertitle" class="border border-gray-300 w-full text-xs px-2 py-1 rounded focus:border-maroon-500 focus:ring-maroon-500" rows="2" required></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Co-authors:</label>
                <input type="text" name="coauthors" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500">
            </div>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Journal Title:</label>
                <input type="text" name="journaltitle" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Vol/Issue/Year:</label>
                <input type="text" name="version" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">P-ISSN:</label>
                <input type="text" name="pissn" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500">
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">E-ISSN:</label>
                <input type="text" name="eissn" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500">
            </div>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 mb-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">DOI:</label>
                <input type="text" name="doi" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500">
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Publisher:</label>
                <input type="text" name="publisher" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">CiteScore:</label>
                <input type="text" name="citescore" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium mb-0.5">Publication Type:</label>
                <div class="flex gap-3 mt-1">
                    <label class="flex items-center text-xs"><input type="radio" name="type" value="Regional" required class="mr-1 focus:ring-maroon-500"> Regional</label>
                    <label class="flex items-center text-xs"><input type="radio" name="type" value="National" class="mr-1 focus:ring-maroon-500"> National</label>
                    <label class="flex items-center text-xs"><input type="radio" name="type" value="International" class="mr-1 focus:ring-maroon-500"> International</label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Indexed in:</label>
                <div class="flex gap-3 mt-1">
                    <label class="flex items-center text-xs"><input type="radio" name="indexed_in" value="Scopus" class="mr-1 focus:ring-maroon-500"> Scopus</label>
                    <label class="flex items-center text-xs"><input type="radio" name="indexed_in" value="Web of Science" class="mr-1 focus:ring-maroon-500"> Web of Science</label>
                    <label class="flex items-center text-xs"><input type="radio" name="indexed_in" value="ACI" class="mr-1 focus:ring-maroon-500"> ACI</label>
                    <label class="flex items-center text-xs"><input type="radio" name="indexed_in" value="PubMed" class="mr-1 focus:ring-maroon-500"> PubMed</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Assistance Details -->
    <div class="bg-gray-50 p-3 rounded-lg">
        <h4 class="font-semibold text-maroon-800 text-sm mb-2">III. Assistance Request</h4>
        <div>
            <label class="block text-xs font-medium mb-0.5">Particulars / Amount:</label>
            <textarea name="particulars" class="border border-gray-300 w-full text-xs px-2 py-1 rounded focus:border-maroon-500 focus:ring-maroon-500" rows="2" required></textarea>
        </div>
    </div>

    <!-- Declaration and Signatures -->
    <div class="bg-gray-50 p-3 rounded-lg">
        <h4 class="font-semibold text-maroon-800 text-sm mb-2">IV. Declaration</h4>
        <p class="text-xs text-gray-600 mb-3">
            I hereby declare that all the details in this application form are accurate. I have not hidden any relevant information as must necessarily brought to the attention of the University. I will satisfy all the terms and conditions prescribed in the guidelines of the University for research paper publication.
        </p>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium mb-0.5">Faculty Name:</label>
                <input type="text" name="facultyname" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" placeholder="Faculty Name" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">Research Center Manager:</label>
                <input type="text" name="centermanager" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" placeholder="Center Manager Name">
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">College Dean:</label>
                <input type="text" name="collegedean" class="border border-gray-300 w-full text-xs px-2 py-1 rounded-lg focus:border-maroon-500 focus:ring-maroon-500" placeholder="College Dean Name">
            </div>
        </div>
    </div>
</div> 