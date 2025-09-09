<x-app-layout>
        <div x-data="publicationRequestData()" class="h-screen bg-gray-50 flex overflow-hidden">
        
        <!-- Hidden notification divs for global notification system -->
        @if(session('success'))
            <div id="success-notification" class="hidden">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="error-notification" class="hidden">{{ session('error') }}</div>
        @endif

        <!-- Error message overlay -->
        <div x-show="errorMessage" x-transition class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow" style="display:none;">
            <span x-text="errorMessage"></span>
        </div>
        
        <!-- Loading overlay -->
        <div x-show="loading" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center" style="display:none;">
            <div class="bg-white rounded-lg shadow-xl px-6 py-5 flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-maroon-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="text-maroon-900 font-semibold">Processing…</span>
            </div>
        </div>

        <!-- Sidebar -->
        @include('components.user-sidebar')

        <!-- Main Content -->
        <div class="flex-1 ml-4 h-screen overflow-y-auto">
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4">
                    <!-- Overview Header -->
                    <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <span>Publication Request</span>
                    </div>
                    
                <!-- Enhanced Search and User Controls -->
                <div class="flex items-center gap-4">
                    <!-- Save Draft Button -->
                    <button type="button" 
                            @click="saveDraft()"
                            :disabled="savingDraft"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        <svg x-show="!savingDraft" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <svg x-show="savingDraft" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span x-text="savingDraft ? 'Saving...' : 'Save Draft'"></span>
                    </button>
                    
                    <!-- Last Saved Indicator -->
                    <div x-show="lastSaved" class="text-sm text-gray-500">
                        Last saved: <span x-text="lastSaved"></span>
                    </div>
                    
                    @include('components.user-navbar')
                </div>
                </div>

                <!-- Modern Form Container -->
                <div class="bg-white/30 backdrop-blur-md border border-white/40 rounded-xl shadow-xl overflow-hidden">
                    <!-- Progress Header -->
                    <div class="bg-gradient-to-r from-maroon-800 to-maroon-900 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">1</span>
                                    </div>
                                    <span class="text-white font-medium">Details</span>
                                </div>
                                <div class="w-8 h-0.5 bg-white/30"></div>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">2</span>
                                    </div>
                                    <span class="text-white font-medium">Upload</span>
                                </div>
                                <div class="w-8 h-0.5 bg-white/30"></div>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">3</span>
                                    </div>
                                    <span class="text-white font-medium">Review</span>
                                </div>
                            </div>
                            <div class="text-white text-sm">
                                <span class="font-medium">Step 1 of 3</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="p-6">
                        <form 
                            id="publication-request-form"
                            method="POST" 
                            action="{{ route('publications.submit') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                @input="checkTabCompletion(); autoSave()"
                @change="checkTabCompletion(); autoSave()"
                x-init="setInterval(() => autoSave(), 30000)"
                            autocomplete="on"
                        >
                            @csrf
                            
                            <!-- Tab Navigation -->
                            <div class="flex border-b border-gray-200 mb-6">
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 relative"
                                    :class="[
                                        activeTab === 'incentive' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : 'border-transparent text-gray-500 hover:text-gray-700',
                                        !tabCompletion.incentive && activeTab !== 'incentive' ? 'ring-2 ring-red-500 ring-offset-2' : ''
                                    ]"
                                    @click="switchTab('incentive')">
                                    Incentive Application
                                    <div x-show="formLoaded && tabCompletion.incentive" class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 relative"
                                    :class="[
                                        activeTab === 'recommendation' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : 'border-transparent text-gray-500 hover:text-gray-700',
                                        !tabCompletion.recommendation && activeTab !== 'recommendation' && tabCompletion.incentive ? 'ring-2 ring-red-500 ring-offset-2' : '',
                                        !tabCompletion.incentive ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!tabCompletion.incentive"
                                    @click="switchTab('recommendation')">
                                    Recommendation Letter
                                    <div x-show="formLoaded && tabCompletion.recommendation" class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 relative"
                                    :class="[
                                        activeTab === 'terminal' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : 'border-transparent text-gray-500 hover:text-gray-700',
                                        !tabCompletion.terminal && activeTab !== 'terminal' && tabCompletion.recommendation ? 'ring-2 ring-red-500 ring-offset-2' : '',
                                        !(tabCompletion.incentive && tabCompletion.recommendation) ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!(tabCompletion.incentive && tabCompletion.recommendation)"
                                    @click="switchTab('terminal')">
                                    Terminal Report
                                    <div x-show="formLoaded && tabCompletion.terminal" class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 relative"
                                    :class="[
                                        activeTab === 'upload' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : 'border-transparent text-gray-500 hover:text-gray-700',
                                        !tabCompletion.upload && activeTab !== 'upload' && tabCompletion.terminal ? 'ring-2 ring-red-500 ring-offset-2' : '',
                                        !(tabCompletion.incentive && tabCompletion.recommendation && tabCompletion.terminal) ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!(tabCompletion.incentive && tabCompletion.recommendation && tabCompletion.terminal)"
                                    @click="switchTab('upload')">
                                    Upload Documents
                                    <div x-show="formLoaded && tabCompletion.upload" class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 relative"
                                    :class="[
                                        activeTab === 'review' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : 'border-transparent text-gray-500 hover:text-gray-700',
                                        !(tabCompletion.incentive && tabCompletion.recommendation && tabCompletion.terminal && tabCompletion.upload) ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!(tabCompletion.incentive && tabCompletion.recommendation && tabCompletion.terminal && tabCompletion.upload)"
                                    @click="switchTab('review')">
                                    Review & Submit
                                    <div x-show="formLoaded && tabCompletion.review" class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                                </button>
                            </div>

                            <!-- Tab Content -->
                            <div class="min-h-[500px]">
                                <!-- Incentive Application Tab -->
                                <div x-show="activeTab === 'incentive'" class="space-y-6">
                                    @include('publications.incentive-application-modern')
                                </div>

                                <!-- Recommendation Letter Tab -->
                                <div x-show="activeTab === 'recommendation'" class="space-y-6">
                                    @include('publications.recommendation-letter-modern')
                                </div>

                                <!-- Terminal Report Tab -->
                                <div x-show="activeTab === 'terminal'" class="space-y-6">
                                    @include('publications.terminal-report-modern')
                                </div>

                                <!-- Upload Documents Tab -->
                                <div x-show="activeTab === 'upload'" class="space-y-6">
                                    @include('publications.upload-documents-modern')
                                </div>

                                <!-- Review & Submit Tab -->
                                <div x-show="activeTab === 'review'" class="space-y-6">
                                    @include('publications.review-submit-modern')
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <button type="button" 
                                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                    @click="switchTab(activeTab === 'incentive' ? 'incentive' : (activeTab === 'recommendation' ? 'incentive' : (activeTab === 'terminal' ? 'recommendation' : (activeTab === 'upload' ? 'terminal' : 'upload'))))">
                                    Previous
                                </button>
                                
                                <div class="flex items-center gap-3">
                                    <button type="button" 
                                        class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-maroon-600 to-maroon-700 rounded-lg hover:from-maroon-700 hover:to-maroon-800 transition-all duration-200 shadow-lg"
                                        @click="switchTab(activeTab === 'incentive' ? 'recommendation' : (activeTab === 'recommendation' ? 'terminal' : (activeTab === 'terminal' ? 'upload' : (activeTab === 'upload' ? 'review' : 'review'))))"
                                        x-text="activeTab === 'review' ? 'Submit Request' : 'Next'">
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
                    // Populate form with draft data
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('DOM loaded, checking for draft data...');
                        console.log('Request object:', @json($request));
                        console.log('Form data exists:', @json(!empty($request->form_data)));
                        @if(!empty($request->form_data))
                            const draftData = @json($request->form_data);
                            console.log('Found draft data:', draftData);
                            console.log('Draft data keys:', Object.keys(draftData));
                            console.log('Draft data type:', typeof draftData);
                            
                            // Wait a bit for Alpine.js to initialize
                            setTimeout(() => {
                                console.log('Starting form population...');
                                let populatedCount = 0;
                                let notFoundCount = 0;
                                Object.keys(draftData).forEach(key => {
                                    const element = document.querySelector(`[name="${key}"]`);
                                    if (element) {
                                        if (element.type === 'checkbox' || element.type === 'radio') {
                                            if (draftData[key] === 'on' || draftData[key] === element.value || draftData[key] === '1') {
                                                element.checked = true;
                                                populatedCount++;
                                                console.log(`Checked ${key}: ${draftData[key]}`);
                                            }
                                        } else {
                                            element.value = draftData[key];
                                            
                                            // Special handling for signatory fields
                                            if (key === 'faculty_name' || key === 'center_manager' || key === 'dean_name') {
                                                // Find the parent Alpine.js component and set selectedName
                                                const alpineComponent = element.closest('[x-data*="signatorySelect"]');
                                                if (alpineComponent && alpineComponent._x_dataStack) {
                                                    const component = alpineComponent._x_dataStack[0];
                                                    if (component && component.selectedName !== undefined) {
                                                        component.selectedName = draftData[key];
                                                        component.query = draftData[key];
                                                        console.log(`Set Alpine.js selectedName for ${key}:`, draftData[key]);
                                                    }
                                                }
                                            }
                                            
                                            populatedCount++;
                                        }
                                        console.log(`Populated ${key}:`, draftData[key]);
                                    } else {
                                        console.log('Element not found for key:', key);
                                        notFoundCount++;
                                    }
                                });
                                console.log(`Form population complete. Populated ${populatedCount} fields, ${notFoundCount} not found.`);
                            }, 1000);
                        @else
                            console.log('No draft data found - request.form_data is empty');
                        @endif
                    });

        function publicationRequestData() {
            return {
                loading: false,
                errorMessage: null,
                errorTimer: null,
                activeTab: 'incentive',
                searchOpen: false,
                userMenuOpen: false,
                savingDraft: false,
                lastSaved: null,
                tabCompletion: {
                    incentive: false,
                    recommendation: false,
                    terminal: false,
                    upload: false,
                    review: false
                },
                formLoaded: false,
                showError(message) {
                    this.errorMessage = message;
                    if (this.errorTimer) clearTimeout(this.errorTimer);
                    this.errorTimer = setTimeout(() => {
                        this.errorMessage = null;
                    }, 3000);
                },
                 async saveDraft() {
                     this.savingDraft = true;
                     try {
                         const formData = new FormData();
                         const form = document.getElementById('publication-request-form');
                         
                         console.log('Saving draft - collecting form data for current tab:', this.activeTab);
                         
                         // Only collect fields from the current tab
                         let currentTabFields = [];
                         if (this.activeTab === 'incentive') {
                             currentTabFields = ['name', 'academicrank', 'college', 'bibentry', 'issn', 'doi', 'scopus', 'wos', 'aci', 'faculty_name', 'center_manager', 'dean_name', 'date'];
                         } else if (this.activeTab === 'recommendation') {
                             currentTabFields = ['rec_collegeheader', 'date', 'rec_faculty_name', 'rec_publication_details', 'rec_indexing_details', 'rec_dean_name'];
                         } else if (this.activeTab === 'terminal') {
                             currentTabFields = ['title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references', 'appendices'];
                         } else if (this.activeTab === 'upload') {
                             currentTabFields = ['article_pdf', 'acceptance_pdf', 'peer_review_pdf', 'terminal_report_pdf'];
                         }
                         
                         console.log('Current tab fields to collect:', currentTabFields);
                         
                         // Collect only current tab fields
                         currentTabFields.forEach(fieldName => {
                             const element = form.querySelector(`[name="${fieldName}"]`);
                             if (element) {
                                 if (element.type === 'file') {
                                     if (element.files && element.files.length > 0) {
                                         formData.append(element.name, element.files[0]);
                                         console.log(`Added file: ${element.name}`);
                                     }
                                 } else if (element.type === 'checkbox' || element.type === 'radio') {
                                     if (element.checked) {
                                         formData.append(element.name, element.value);
                                         console.log(`Added checkbox/radio: ${element.name} = ${element.value}`);
                                     }
                                 } else {
                                     formData.append(element.name, element.value || '');
                                     console.log(`Added field: ${element.name} = ${element.value || '(empty)'}`);
                                 }
                             }
                         });
                        
                        // Also collect signatory selections from Alpine.js data
                        console.log('Collecting signatory data...');
                        const signatoryInputs = form.querySelectorAll('input[name*="faculty"], input[name*="dean"], input[name*="manager"]');
                        signatoryInputs.forEach(input => {
                            if (input.value && input.value.trim() !== '') {
                                formData.append(input.name, input.value);
                                console.log(`Added signatory: ${input.name} = ${input.value}`);
                            }
                        });
                        
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('save_draft', '1');
                        
                        console.log('Sending draft save request...');
                        const response = await fetch('{{ route("publications.submit") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const responseText = await response.text();
                        console.log('Response status:', response.status);
                        console.log('Response text:', responseText);
                        
                        if (response.ok) {
                            try {
                                const jsonResponse = JSON.parse(responseText);
                                if (jsonResponse.success) {
                                    this.lastSaved = new Date().toLocaleTimeString();
                                    this.showError('Draft saved successfully!');
                                } else {
                                    this.showError(jsonResponse.message || 'Failed to save draft. Please try again.');
                                }
                            } catch (e) {
                                // If response is not JSON, treat as success
                                this.lastSaved = new Date().toLocaleTimeString();
                                this.showError('Draft saved successfully!');
                            }
                        } else {
                            try {
                                const jsonResponse = JSON.parse(responseText);
                                this.showError(jsonResponse.message || 'Failed to save draft. Please try again.');
                            } catch (e) {
                                this.showError('Failed to save draft. Please try again.');
                            }
                        }
                    } catch (error) {
                        console.error('Draft save error:', error);
                        this.showError('Error saving draft. Please check your connection.');
                    } finally {
                        this.savingDraft = false;
                    }
                },
                // Simple form validation - no complex logic
                validateForm() {
                    const requiredFields = document.querySelectorAll('[required]');
                    let allValid = true;
                    let firstInvalidField = null;
                    
                    requiredFields.forEach(field => {
                        let isValid = false;
                        
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            isValid = field.checked;
                        } else if (field.type === 'file') {
                            isValid = field.files && field.files.length > 0;
                        } else {
                            isValid = field.value.trim() !== '';
                        }
                        
                        if (!isValid) {
                            allValid = false;
                            if (!firstInvalidField) firstInvalidField = field;
                            field.classList.add('ring-2', 'ring-red-500', 'ring-offset-2');
                            setTimeout(() => field.classList.remove('ring-2', 'ring-red-500', 'ring-offset-2'), 3000);
                        }
                    });
                    
                    if (!allValid && firstInvalidField) {
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                        this.showError('Please complete all required fields before submitting.');
                    }
                    
                    return allValid;
                },
                // Simple tab switching - no complex validation
                switchTab(targetTab) {
                    this.activeTab = targetTab;
                },
                checkTabCompletion() {
                    // Only check the current tab for real-time validation
                    this.checkCurrentTabCompletion();
                },
                checkCurrentTabCompletion() {
                    const currentTab = this.activeTab;
                    console.log(`Checking current tab completion for: ${currentTab}`);
                    
                    if (currentTab === 'incentive') {
                        const incentiveFields = ['name', 'academicrank', 'college', 'bibentry', 'issn', 'doi', 'scopus', 'wos', 'aci', 'faculty_name', 'center_manager', 'dean_name'];
                        this.tabCompletion.incentive = incentiveFields.every(field => {
                            const element = document.querySelector(`[name="${field}"]`);
                            if (!element) return false;
                            if (element.type === 'radio') return document.querySelector(`[name="${field}"]:checked`) !== null;
                            if (element.type === 'checkbox') return Array.from(document.querySelectorAll(`[name="${field}"]`)).some(cb => cb.checked);
                            return element.value.trim() !== '';
                        });
                    } else if (currentTab === 'recommendation') {
                        const recommendationFields = ['rec_collegeheader', 'date', 'rec_faculty_name', 'rec_publication_details', 'rec_indexing_details', 'rec_dean_name'];
                        this.tabCompletion.recommendation = recommendationFields.every(field => {
                            const element = document.querySelector(`[name="${field}"]`);
                            if (!element) return false;
                            return element.value.trim() !== '';
                        });
                    } else if (currentTab === 'terminal') {
                        const terminalFields = ['title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references'];
                        this.tabCompletion.terminal = terminalFields.every(field => {
                            const element = document.querySelector(`[name="${field}"]`);
                            if (!element) return false;
                            return element.value.trim() !== '';
                        });
                    } else if (currentTab === 'upload') {
                        const uploadFields = ['article_pdf', 'acceptance_pdf', 'peer_review_pdf', 'terminal_report_pdf'];
                        this.tabCompletion.upload = uploadFields.every(field => {
                            const element = document.querySelector(`[name="${field}"]`);
                            if (!element) return false;
                            return element.files && element.files.length > 0;
                        });
                    }
                    
                    // Review tab is complete if upload is complete
                    this.tabCompletion.review = this.tabCompletion.upload;
                    
                    console.log(`Current tab (${currentTab}) completion:`, this.tabCompletion[currentTab]);
                },
                updateAllTabCompletion() {
                    console.log('=== UPDATING ALL TAB COMPLETION ===');
                    
                    // Check incentive tab
                    const incentiveFields = ['name', 'academicrank', 'college', 'bibentry', 'issn', 'doi', 'scopus', 'wos', 'aci', 'faculty_name', 'center_manager', 'dean_name'];
                    console.log('Checking incentive fields:', incentiveFields);
                    
                    this.tabCompletion.incentive = incentiveFields.every(field => {
                        return this.isFieldValid(field);
                    });
                    
                    console.log(`Incentive tab completion: ${this.tabCompletion.incentive}`);
                    
                    // Check recommendation tab
                    const recommendationFields = ['rec_collegeheader', 'date', 'rec_faculty_name', 'rec_publication_details', 'rec_indexing_details', 'rec_dean_name'];
                    this.tabCompletion.recommendation = recommendationFields.every(field => {
                        return this.isFieldValid(field);
                    });
                    
                    // Check terminal tab
                    const terminalFields = ['title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references'];
                    this.tabCompletion.terminal = terminalFields.every(field => {
                        return this.isFieldValid(field);
                    });
                    
                    // Check upload tab
                    const uploadFields = ['article_pdf', 'acceptance_pdf', 'peer_review_pdf', 'terminal_report_pdf'];
                    this.tabCompletion.upload = uploadFields.every(field => {
                        return this.isFieldValid(field);
                    });
                    
                    // Review tab is complete if upload is complete
                    this.tabCompletion.review = this.tabCompletion.upload;
                },
                checkSpecificTabCompletion(tabName) {
                    if (tabName === 'incentive') {
                        const incentiveFields = ['name', 'academicrank', 'college', 'bibentry', 'issn', 'doi', 'scopus', 'wos', 'aci', 'faculty_name', 'center_manager', 'dean_name'];
                        return incentiveFields.every(field => this.isFieldValid(field));
                    } else if (tabName === 'recommendation') {
                        const recommendationFields = ['rec_collegeheader', 'date', 'rec_faculty_name', 'rec_publication_details', 'rec_indexing_details', 'rec_dean_name'];
                        return recommendationFields.every(field => this.isFieldValid(field));
                    } else if (tabName === 'terminal') {
                        const terminalFields = ['title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references'];
                        return terminalFields.every(field => this.isFieldValid(field));
                    } else if (tabName === 'upload') {
                        const uploadFields = ['article_pdf', 'acceptance_pdf', 'peer_review_pdf', 'terminal_report_pdf'];
                        return uploadFields.every(field => this.isFieldValid(field));
                    }
                    return true;
                },
                isFieldValid(fieldName) {
                    const element = document.querySelector(`[name="${fieldName}"]`);
                    if (!element) return false;
                    
                    if (element.type === 'checkbox') {
                        return element.checked;
                    } else if (element.type === 'radio') {
                        return document.querySelector(`[name="${fieldName}"]:checked`) !== null;
                    } else if (element.type === 'file') {
                        return element.files && element.files.length > 0;
                    } else {
                        return element.value.trim() !== '';
                    }
                },
                init() {
                    // Wait for form to be fully loaded before checking completion
                    this.waitForFormLoad();
                },
                waitForFormLoad() {
                    // Always wait a bit to prevent flash
                    setTimeout(() => {
                        this.formLoaded = true;
                        this.updateAllTabCompletion();
                    }, 1000);
                },
                autoSave() {
                    // Auto-save every 30 seconds if there are changes
                    if (this.hasFormChanges()) {
                        this.saveDraft();
                    }
                },
                hasFormChanges() {
                    // Simple check to see if form has any data
                    const form = document.getElementById('publication-request-form');
                    if (!form) return false;
                    
                    const inputs = form.querySelectorAll('input, textarea, select');
                    return Array.from(inputs).some(input => {
                        if (input.type === 'file') return input.files && input.files.length > 0;
                        if (input.type === 'checkbox' || input.type === 'radio') return input.checked;
                        return input.value.trim() !== '';
                    });
                }
            }
        }
    </script>

</x-app-layout>
