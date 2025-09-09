<x-app-layout>
    {{-- Privacy Enforcer --}}
    <!-- Privacy Modal -->
    <div id="privacyModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-maroon-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">Data Privacy Notice</h2>
                    <p class="text-lg mb-6">
                        By continuing to use <span class="font-bold text-maroon-600">PubCite</span>, you agree to the 
                        <a href="https://www.usep.edu.ph/usep-data-privacy-statement/" target="_blank" rel="noopener noreferrer" 
                           class="text-maroon-600 hover:text-maroon-800 font-medium transition-colors duration-200">
                            University of Southeastern Philippines' Data Privacy Statement
                        </a>.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button onclick="acceptPrivacy()" 
                                class="px-8 py-3 bg-maroon-600 text-white rounded-lg hover:bg-maroon-700 transition-colors duration-200 font-medium">
                            I Accept
                        </button>
                        <button onclick="declinePrivacy()" 
                                class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium">
                            Decline
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Privacy Modal Functions
    function acceptPrivacy() {
        console.log('Accept privacy clicked');
        
        // Set privacy accepted in localStorage with timestamp
        const timestamp = Date.now();
        localStorage.setItem('privacyAccepted', 'true');
        localStorage.setItem('privacyAcceptedAt', timestamp.toString());
        
        console.log('Privacy accepted - localStorage:', localStorage.getItem('privacyAccepted'));
        
        // Sync with server
        fetch('{{ route("privacy.accept") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server sync successful:', data);
        })
        .catch(error => {
            console.error('Server sync failed:', error);
            // Continue anyway - client-side enforcement will work
        });
        
        // Close the modal
        const modal = document.getElementById('privacyModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function declinePrivacy() {
        console.log('Decline privacy clicked');
        // Redirect to USeP main website
        window.location.href = 'https://www.usep.edu.ph/';
    }

    // Initialize privacy modal on page load
    function initPrivacyModal() {
        console.log('Initializing privacy modal...');
        
        // Check localStorage for privacy acceptance
        const privacyAccepted = localStorage.getItem('privacyAccepted') === 'true';
        const modal = document.getElementById('privacyModal');
        
        console.log('Page load - privacy accepted:', privacyAccepted);
        console.log('Modal element found:', !!modal);
        console.log('Current URL:', window.location.href);
        
        if (!modal) {
            console.error('Privacy modal element not found!');
            return;
        }
        
        if (privacyAccepted) {
            // User has already accepted - hide modal
            console.log('Hiding modal - user already accepted');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        } else {
            // User hasn't accepted - show modal
            console.log('Showing modal - user needs to accept');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    // Try multiple initialization methods
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrivacyModal);
    } else {
        initPrivacyModal();
    }

    // Also try after a short delay in case of timing issues
    setTimeout(initPrivacyModal, 100);
    </script>
    
    <script>
        function citationRequestData() {
            return {
                loading: false,
                errorMessage: null,
                errorTimer: null,
                activeTab: 'incentive',
                searchOpen: false,
                userMenuOpen: false,
                savingDraft: false,
                lastSaved: null,
                autoSaveTimer: null,
                tabStatesRefreshed: 0,
                
                showError(message) {
                    this.errorMessage = message;
                    if (this.errorTimer) clearTimeout(this.errorTimer);
                    this.errorTimer = setTimeout(() => {
                        this.errorMessage = null;
                    }, 3000);
                },
                
                // Simple form validation - no complex logic
                validateForm(showError = false) {
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
                    
                    if (!allValid && firstInvalidField && showError) {
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                        this.showError('Please complete all required fields before submitting.');
                    }
                    
                    return allValid;
                },
                
                // Sequential tab switching with validation
                switchTab(targetTab) {
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    const targetIndex = tabs.indexOf(targetTab);
                    
                    // Always allow going back or staying on same tab
                    if (targetIndex <= currentIndex) {
                        this.activeTab = targetTab;
                        return;
                    }
                    
                    // Going forward - validate current tab
                    if (!this.validateCurrentTab()) {
                        this.showError('Please complete all required fields in the current tab before proceeding.');
                        return;
                    }
                    
                    this.activeTab = targetTab;
                },
                
                // Validate current tab - simple and reliable
                validateCurrentTab() {
                    const currentTab = this.activeTab;
                    
                    // Special handling for upload tab
                    if (currentTab === 'upload') {
                        const requiredFiles = ['recommendation_letter', 'published_article', 'peer_review'];
                        
                        for (let fileName of requiredFiles) {
                            const fileInput = document.querySelector(`input[name="${fileName}"]`);
                            console.log('Checking file:', fileName, 'Found:', !!fileInput, 'Files:', fileInput?.files?.length);
                            
                            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                                console.log('No files selected for:', fileName);
                                return false;
                            }
                        }
                        return true;
                    }
                    
                    // Define required fields for other tabs
                    const tabFields = {
                        'incentive': ['name', 'rank', 'college', 'bibentry', 'issn'],
                        'recommendation': ['faculty_name', 'dean_name', 'rec_publication_details', 'rec_indexing_details']
                    };
                    
                    const requiredFields = tabFields[currentTab] || [];
                    if (requiredFields.length === 0) {
                        return true; // No validation needed for this tab
                    }
                    
                    // Check each required field
                    for (let fieldName of requiredFields) {
                        const field = document.querySelector(`[name="${fieldName}"]`);
                        console.log('Checking field:', fieldName, 'Found:', !!field, 'Type:', field?.type, 'Value:', field?.value);
                        
                        if (!field) continue;
                        
                        // Check if field is valid
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            if (!field.checked) {
                                console.log('Field not checked:', fieldName);
                                return false;
                            }
                        } else {
                            if (!field.value || field.value.trim() === '') {
                                console.log('Field is empty:', fieldName);
                                return false;
                            }
                        }
                    }
                    
                    return true;
                },
                
                // Check if field belongs to current tab
                fieldBelongsToTab(field, tab) {
                    const tabElement = document.querySelector(`[x-show="activeTab === '${tab}'"]`);
                    if (!tabElement) return false;
                    
                    return tabElement.contains(field);
                },
                
                // Check if a tab should be enabled (progressive unlocking)
                isTabEnabled(tabName) {
                    // Use the reactive property to force re-evaluation
                    const _ = this.tabStatesRefreshed;
                    
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    const targetIndex = tabs.indexOf(tabName);
                    
                    console.log('Checking if tab is enabled:', tabName, 'Current tab:', this.activeTab);
                    
                    // Always allow current tab and previous tabs
                    if (targetIndex <= currentIndex) {
                        console.log('Tab enabled (current or previous):', tabName);
                        return true;
                    }
                    
                    // For next tab, check if current tab is complete
                    if (targetIndex === currentIndex + 1) {
                        const isValid = this.validateCurrentTab();
                        console.log('Next tab validation result:', tabName, '=', isValid);
                        return isValid;
                    }
                    
                    // For future tabs, check if all previous tabs are complete
                    for (let i = 0; i < targetIndex; i++) {
                        const previousTab = tabs[i];
                        
                        // Temporarily switch to check previous tab
                        const originalTab = this.activeTab;
                        this.activeTab = previousTab;
                        const isComplete = this.validateCurrentTab();
                        this.activeTab = originalTab;
                        
                        if (!isComplete) {
                            console.log('Previous tab not complete:', previousTab);
                            return false;
                        }
                    }
                    
                    console.log('All previous tabs complete, enabling:', tabName);
                    return true;
                },
                
                // Silent auto-save - no notifications
                async saveDraft() {
                    this.savingDraft = true;
                    try {
                        const formData = new FormData();
                        const form = document.getElementById('citation-request-form');
                        
                        // Collect ALL form data from ALL tabs
                        const inputs = form.querySelectorAll('input, textarea, select');
                        
                        console.log('Draft save - collecting form data from', inputs.length, 'inputs');
                        inputs.forEach(input => {
                            if (input.type === 'file') {
                                if (input.files && input.files.length > 0) {
                                    formData.append(input.name, input.files[0]);
                                }
                            } else if (input.type === 'checkbox' || input.type === 'radio') {
                                if (input.checked) {
                                    formData.append(input.name, input.value);
                                }
                            } else {
                                // Debug signatory fields specifically
                                if (input.name.includes('faculty_name') || input.name.includes('center_manager') || input.name.includes('dean_name') || input.name.includes('rec_faculty_name') || input.name.includes('rec_dean_name')) {
                                    console.log('Saving signatory field:', input.name, '=', input.value);
                                }
                                formData.append(input.name, input.value || '');
                            }
                        });
                        
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('save_draft', '1');
                        
                        const response = await fetch('{{ route("citations.submit") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (response.ok) {
                            this.lastSaved = new Date().toLocaleTimeString();
                        }
                        // Silent save - no error notifications for auto-save
                    } catch (error) {
                        console.error('Auto-save error:', error);
                        // Silent error - don't show notifications for auto-save
                    } finally {
                        this.savingDraft = false;
                    }
                },
                
                // Debounced auto-save
                autoSave() {
                    // Clear existing timer
                    if (this.autoSaveTimer) {
                        clearTimeout(this.autoSaveTimer);
                    }
                    
                    // Set new timer - save after 2 seconds of inactivity
                    this.autoSaveTimer = setTimeout(() => {
                        this.saveDraft();
                    }, 2000);
                },
                
                // Load draft data into form
                loadDraftData() {
                    const draftData = @json($request->form_data ?? []);
                    if (!draftData || Object.keys(draftData).length === 0) {
                        // Set initial timestamp even if no draft data
                        this.lastSaved = 'Never';
                        return;
                    }
                    
                    console.log('Loading citation draft data:', draftData);
                    
                    // Set timestamp to show draft was loaded
                    this.lastSaved = 'Draft loaded';
                    
                    // Populate all form fields
                    Object.keys(draftData).forEach(key => {
                        const element = document.querySelector(`[name="${key}"]`);
                        if (element) {
                            if (element.type === 'checkbox' || element.type === 'radio') {
                                element.checked = draftData[key] === '1' || draftData[key] === 'on';
                            } else if (element.type === 'file') {
                                // Files can't be restored, skip
                            } else {
                                element.value = draftData[key] || '';
                            }
                        }
                    });
                    
                    // Restore signatory selections
                    this.restoreSignatorySelections(draftData);
                    
                    // Update submit button state
                    this.updateSubmitButton();
                },
                
                // Restore signatory Alpine.js selections
                restoreSignatorySelections(draftData) {
                    const signatoryFields = [
                        'faculty_name', 'center_manager', 'dean_name',  // Incentive tab
                        'rec_faculty_name', 'rec_dean_name'             // Recommendation tab
                    ];
                    
                    signatoryFields.forEach(fieldName => {
                        const value = draftData[fieldName];
                        console.log('Loading signatory field:', fieldName, 'Value:', value);
                        if (value) {
                            // Find the Alpine.js component and set selectedName
                            const component = document.querySelector(`[x-data*="signatorySelect"][data-field="${fieldName}"]`);
                            console.log('Found component for', fieldName, ':', !!component);
                            if (component) {
                                // Wait for Alpine.js to be ready
                                this.$nextTick(() => {
                                    const alpineData = Alpine.$data(component);
                                    console.log('Alpine data for', fieldName, ':', alpineData);
                                    if (alpineData) {
                                        alpineData.selectedName = value;
                                        alpineData.query = value;
                                        console.log('Set signatory value:', fieldName, '=', value);
                                    } else {
                                        console.log('Alpine data not ready for', fieldName, ', retrying...');
                                        // Retry after a short delay
                                        setTimeout(() => {
                                            const retryData = Alpine.$data(component);
                                            if (retryData) {
                                                retryData.selectedName = value;
                                                retryData.query = value;
                                                console.log('Retry successful for', fieldName, '=', value);
                                            }
                                        }, 500);
                                    }
                                });
                            } else {
                                console.log('Component not found for', fieldName);
                            }
                        }
                    });
                },
                
                // Simple tab navigation helpers
                getNextTab() {
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    return tabs[currentIndex + 1] || this.activeTab;
                },
                
                getPreviousTab() {
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    return tabs[currentIndex - 1] || this.activeTab;
                },
                
                // Update submit button state
                updateSubmitButton() {
                    const submitBtn = document.querySelector('#submit-btn');
                    if (submitBtn) {
                        const formValid = this.validateForm();
                        const confirmChecked = document.querySelector('#confirm-submission')?.checked || false;
                        submitBtn.disabled = !(formValid && confirmChecked);
                    }
                    
                    // Also refresh tab states when form changes
                    this.refreshTabStates();
                },
                
                // Refresh tab enabled/disabled states
                refreshTabStates() {
                    console.log('Refreshing tab states for current tab:', this.activeTab);
                    // Force Alpine.js to re-evaluate the tab states
                    this.$nextTick(() => {
                        // Trigger validation for current tab
                        const currentTabValid = this.validateCurrentTab();
                        console.log('Current tab validation result:', currentTabValid);
                        
                        // Force Alpine.js to re-render by updating a reactive property
                        // This will cause isTabEnabled() to be called for all tabs
                        this.tabStatesRefreshed = Date.now();
                    });
                },
                
                // Handle form submission - only show error popup on actual submit
                handleSubmit(event) {
                    if (!this.validateForm(true)) {
                        event.preventDefault();
                        // Error popup is already shown by validateForm()
                        return false;
                    }
                    // Form is valid, allow submission
                    return true;
                },
                
                // Initialize form
                init() {
                    // Load draft data after a short delay to ensure DOM is ready
                    setTimeout(() => {
                        this.loadDraftData();
                    }, 500);
                    
                    // Add event listener for confirmation checkbox
                    this.$nextTick(() => {
                        const confirmCheckbox = document.querySelector('#confirm-submission');
                        if (confirmCheckbox) {
                            confirmCheckbox.addEventListener('change', () => {
                                this.updateSubmitButton();
                            });
                        }
                    });
                    
                    // Setup real-time validation
                    this.setupRealTimeValidation();
                },
                
                // Setup real-time validation
                setupRealTimeValidation() {
                    // Listen for input changes on all form fields
                    document.addEventListener('input', (e) => {
                        if (e.target.matches('input, textarea, select')) {
                            console.log('Input event detected:', e.target.name, 'Value:', e.target.value);
                            this.refreshTabStates();
                        }
                    });
                    
                    // Listen for checkbox/radio changes
                    document.addEventListener('change', (e) => {
                        if (e.target.matches('input[type="checkbox"], input[type="radio"]')) {
                            console.log('Checkbox/radio change detected:', e.target.name, 'Checked:', e.target.checked);
                            this.refreshTabStates();
                        }
                    });
                    
                    // Listen for file input changes
                    document.addEventListener('change', (e) => {
                        if (e.target.matches('input[type="file"]')) {
                            console.log('File input change detected:', e.target.name, 'Files:', e.target.files.length);
                            this.refreshTabStates();
                        }
                    });
                    
                    // Listen for signatory selection changes
                    document.addEventListener('signatory-selected', (e) => {
                        console.log('Signatory selected:', e.detail);
                        this.refreshTabStates();
                    });
                    
                    // Listen for Alpine.js initialization
                    document.addEventListener('alpine:init', () => {
                        // This will be called when Alpine.js initializes
                        setTimeout(() => {
                            this.refreshTabStates();
                        }, 100);
                    });
                }
            }
        }
    </script>
    
    <div x-data="citationRequestData()" x-init="init()" class="h-screen bg-gray-50 flex overflow-hidden">
        
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Citation Request</span>
                    </div>
                    
                <!-- Enhanced Search and User Controls -->
                <div class="flex items-center gap-4">
                    <!-- Auto-save Status -->
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg x-show="savingDraft" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span x-show="savingDraft" class="text-blue-600">Saving...</span>
                        <span x-show="!savingDraft" class="text-gray-500">
                            Last saved: <span x-text="lastSaved || 'Never'"></span>
                        </span>
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
                            id="citation-request-form"
                            method="POST" 
                            action="{{ route('citations.submit') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                            @input="updateSubmitButton(); autoSave()"
                            @change="updateSubmitButton(); autoSave()"
                            @submit="handleSubmit($event)"
                            autocomplete="on"
                        >
                            @csrf
                            
                            <!-- Tab Navigation -->
                            <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg">
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 rounded-tl-lg"
                                    :class="activeTab === 'incentive' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                                    @click="switchTab('incentive')">
                                    Incentive Application
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200"
                                    :class="activeTab === 'recommendation' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : isTabEnabled('recommendation') ? 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' : 'border-transparent text-gray-400 cursor-not-allowed bg-gray-100'"
                                    :disabled="!isTabEnabled('recommendation')"
                                    @click="switchTab('recommendation')">
                                    Recommendation Letter
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200"
                                    :class="activeTab === 'upload' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : isTabEnabled('upload') ? 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' : 'border-transparent text-gray-400 cursor-not-allowed bg-gray-100'"
                                    :disabled="!isTabEnabled('upload')"
                                    @click="switchTab('upload')">
                                    Upload Documents
                                </button>
                                <button type="button" 
                                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-all duration-200 rounded-tr-lg"
                                    :class="activeTab === 'review' ? 'border-maroon-600 text-maroon-700 bg-maroon-50' : isTabEnabled('review') ? 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' : 'border-transparent text-gray-400 cursor-not-allowed bg-gray-100'"
                                    :disabled="!isTabEnabled('review')"
                                    @click="switchTab('review')">
                                    Review & Submit
                                </button>
                            </div>

                            <!-- Tab Content -->
                            <div class="min-h-[500px] bg-white rounded-b-lg shadow-sm border border-t-0 border-gray-200 p-6">
                                <!-- Incentive Application Tab -->
                                <div x-show="activeTab === 'incentive'" class="space-y-6">
                                    @include('citations.incentive-application-modern')
                                </div>

                                <!-- Recommendation Letter Tab -->
                                <div x-show="activeTab === 'recommendation'" class="space-y-6">
                                    @include('citations.recommendation-letter-modern')
                                </div>

                                <!-- Upload Documents Tab -->
                                <div x-show="activeTab === 'upload'" class="space-y-6">
                                    @include('citations.upload-documents-simple')
                                </div>

                                <!-- Review & Submit Tab -->
                                <div x-show="activeTab === 'review'" class="space-y-6">
                                    @include('citations.review-submit-modern')
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-between pt-6 border-t border-gray-200">
                                <button type="button" 
                                    @click="switchTab(getPreviousTab())"
                                    x-show="activeTab !== 'incentive'"
                                    class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    Previous
                                </button>
                                <div class="flex-1"></div>
                                <button type="button" 
                                    @click="switchTab(getNextTab())"
                                    x-show="activeTab !== 'review'"
                                    :disabled="!isTabEnabled(getNextTab())"
                                    :class="isTabEnabled(getNextTab()) ? 'px-6 py-2 bg-maroon-600 text-white rounded-lg hover:bg-maroon-700 transition-colors' : 'px-6 py-2 bg-gray-400 text-gray-200 rounded-lg cursor-not-allowed transition-colors'">
                                    Next
                                </button>
                                <button type="submit" 
                                    id="submit-btn"
                                    :disabled="!validateForm() || !document.querySelector('#confirm-submission')?.checked"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

</x-app-layout>
