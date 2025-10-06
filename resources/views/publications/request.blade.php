<x-app-layout>
    <x-global-notifications />
    <script>
        function publicationRequestData() {
            return {
                loading: false,
                isSubmitting: false,
                autoSaveDisabled: false,
                errorMessage: null,
                errorTimer: null,
                activeTab: 'incentive',
                searchOpen: false,
                savingDraft: false,
                lastSaved: null,
                autoSaveTimer: null,
                tabStatesRefreshed: 0,
                confirmChecked: false,
                
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
                            isValid = field.value && field.value.trim() !== '';
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
                
                // Validate form for submission - only check fields that have been filled out
                validateFormForSubmission() {
                    // Get all required fields
                    const requiredFields = document.querySelectorAll('[required]');
                    let allValid = true;
                    
                    
                    requiredFields.forEach(field => {
                        // Skip validation if field is hidden (not in current tab)
                        if (field.offsetParent === null) {
                            return;
                        }
                        
                        let isValid = false;
                        
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            isValid = field.checked;
                        } else if (field.type === 'file') {
                            isValid = field.files && field.files.length > 0;
                        } else {
                            isValid = field.value && field.value.trim() !== '';
                        }
                        
                        if (!isValid) {
                            allValid = false;
                        }
                    });
                    
                    return allValid;
                },
                
                // Sequential tab switching with validation
                switchTab(targetTab) {
                    const tabs = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    const targetIndex = tabs.indexOf(targetTab);
                    
                    // Always allow going back or staying on same tab
                    if (targetIndex <= currentIndex) {
                        this.activeTab = targetTab;
                        
                        // Display uploaded files when switching to review tab
                        if (targetTab === 'review') {
                            setTimeout(() => {
                                if (typeof displayUploadedFiles === 'function') {
                                    displayUploadedFiles();
                                }
                                // Update submit button state when switching to review
                                this.updateSubmitButton();
                            }, 100);
                        }
                        return;
                    }
                    
                    // Going forward - validate current tab
                    if (!this.validateCurrentTab()) {
                        this.showError('Please complete all required fields in the current tab before proceeding.');
                        return;
                    }
                    
                    this.activeTab = targetTab;
                    
                    // Display uploaded files when switching to review tab
                    if (targetTab === 'review') {
                        setTimeout(() => {
                            if (typeof displayUploadedFiles === 'function') {
                                displayUploadedFiles();
                            }
                            // Update submit button state when switching to review
                            this.updateSubmitButton();
                        }, 100);
                    }
                },
                
                // Validate current tab - simple and reliable
                validateCurrentTab() {
                    const currentTab = this.activeTab;
                    
                    // Special handling for upload tab
                    if (currentTab === 'upload') {
                        const requiredFiles = ['recommendation_letter', 'published_article', 'peer_review', 'terminal_report'];
                        
                        for (let fileName of requiredFiles) {
                            const fileInput = document.querySelector(`input[name="${fileName}"]`);
                            
                            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                                return false;
                            }
                        }
                        return true;
                    }
                    
                    // Define required fields for other tabs
                    const tabFields = {
                        'incentive': ['name', 'rank', 'college', 'bibentry', 'issn'],
                        'recommendation': ['rec_faculty_name', 'rec_dean_name', 'rec_publication_details', 'rec_indexing_details'],
                        'terminal': ['title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references', 'appendices']
                    };
                    
                    const requiredFields = tabFields[currentTab] || [];
                    if (requiredFields.length === 0) {
                        return true; // No validation needed for this tab
                    }
                    
                    // Check each required field
                    for (let fieldName of requiredFields) {
                        const field = document.querySelector(`[name="${fieldName}"]`);
                        
                        if (!field) {
                            continue;
                        }
                        
                        // Check if field is valid
                        if (field.type === 'checkbox' || field.type === 'radio') {
                            if (!field.checked) {
            return false;
                            }
                        } else {
                            if (!field.value || field.value.trim() === '') {
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
                
                // Get next tab in sequence
                getNextTab() {
                    const tabs = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    return tabs[currentIndex + 1] || 'review';
                },
                
                // Check if a tab should be enabled (progressive unlocking)
                isTabEnabled(tabName) {
                    // Use the reactive property to force re-evaluation
                    const _ = this.tabStatesRefreshed;
                    
                    const tabs = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    const targetIndex = tabs.indexOf(tabName);
                    
                    
                    // Always allow current tab and previous tabs
                    if (targetIndex <= currentIndex) {
                        return true;
                    }
                    
                    // For next tab, check if current tab is complete
                    if (targetIndex === currentIndex + 1) {
                        const isValid = this.validateCurrentTab();
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
                            return false;
                        }
                    }
                    
                    return true;
                },
                
                // Silent auto-save - no notifications
                async saveDraft() {
                    this.savingDraft = true;
                    try {
                        const formData = new FormData();
                        const form = document.getElementById('publication-request-form');
                        
                        // Collect ALL form data from ALL tabs
                        const inputs = form.querySelectorAll('input, textarea, select');
                        
                        
                        // Track if we have meaningful data to save
                        let hasData = false;
                        
                        inputs.forEach(input => {
                            if (input.type === 'file') {
                                // Skip files in auto-save to prevent multiple folder creation
                                // Files will be saved only during final submission
                                return;
                            } else if (input.type === 'checkbox' || input.type === 'radio') {
                                if (input.checked) {
                                    formData.append(input.name, input.value);
                                    hasData = true;
                                }
                            } else {
                                // Only save fields that have actual content
                                const value = input.value || '';
                                if (value.trim() !== '') {
                                    formData.append(input.name, value);
                                    hasData = true;
                                }
                            }
                        });
                        
                        // Don't save if no meaningful data
                        if (!hasData) {
                return;
            }
                        
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('save_draft', '1');
                        
                        const response = await fetch('{{ route("publications.submit") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (response.ok) {
                            this.lastSaved = new Date().toLocaleTimeString();
                        }
                        // Silent save - no error notifications for auto-save
                    } catch (error) {
                        // Silent error - don't show notifications for auto-save
                    } finally {
                        this.savingDraft = false;
                    }
                },
                
                // Debounced auto-save
                autoSave() {
                    // Don't auto-save if disabled (e.g., after form submission)
                    if (this.autoSaveDisabled) {
                    return;
                }
                    
                    // Clear existing timer
                    if (this.autoSaveTimer) {
                        clearTimeout(this.autoSaveTimer);
                    }
                    
                    // Set new timer - save after 2 seconds of inactivity
                    this.autoSaveTimer = setTimeout(() => {
                        this.saveDraft();
                    }, 2000);
                },
                
                // Disable auto-save (called after form submission)
                disableAutoSave() {
                    this.autoSaveDisabled = true;
                    if (this.autoSaveTimer) {
                        clearTimeout(this.autoSaveTimer);
                        this.autoSaveTimer = null;
                    }
                },
                
                // Load draft data into form
                loadDraftData() {
                    // Check if we should load a specific draft from sessionStorage
                    const loadDraftId = sessionStorage.getItem('loadDraftId');
                    if (loadDraftId) {
                        this.loadSpecificDraft(loadDraftId);
                        return;
                    }
                    
                    const draftData = @json($request->form_data ?? []);
                    if (!draftData || Object.keys(draftData).length === 0) {
                        // Set initial timestamp even if no draft data
                        this.lastSaved = 'Never';
                        return;
                    }
                    
                    
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
                
                // Load a specific draft by ID
                async loadSpecificDraft(draftId) {
                    try {
                        const response = await fetch(`/api/draft/${draftId}`);
                        const data = await response.json();
                        
                        
                        if (data.success && data.draft) {
                            // form_data is already an object from the API
                            const draftData = data.draft.form_data;
                            
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
                            
                            // Clear the sessionStorage
                            sessionStorage.removeItem('loadDraftId');
                        } else {
                            this.lastSaved = 'Never';
                        }
                    } catch (error) {
                        this.lastSaved = 'Never';
                    }
                },
                
                // Restore signatory Alpine.js selections
                restoreSignatorySelections(draftData) {
                    const signatoryFields = [
                        'faculty_name', 'center_manager', 'dean_name',  // Incentive tab
                        'rec_faculty_name', 'rec_dean_name'             // Recommendation tab
                    ];
                    
                    signatoryFields.forEach(fieldName => {
                        const value = draftData[fieldName];
                        if (value) {
                            // Use multiple selectors to find the component
                            const selectors = [
                                `[data-field="${fieldName}"]`,
                                `[name="${fieldName}"]`,
                                `input[name="${fieldName}"]`
                            ];
                            
                            let component = null;
                            for (const selector of selectors) {
                                component = document.querySelector(selector);
                                if (component) {
                                    break;
                                }
                            }
                            
                            if (component) {
                                // Robust restoration with multiple attempts
                                this.restoreSignatoryValue(component, fieldName, value, 0);
                            }
                        }
                    });
                },
                
                // Robust signatory value restoration with retries
                restoreSignatoryValue(component, fieldName, value, attempt = 0) {
                    const maxAttempts = 10;
                    const delay = Math.min(100 * Math.pow(1.5, attempt), 2000); // Exponential backoff, max 2s
                    
                    
                    // Try to find Alpine.js data
                    let alpineData = null;
                    try {
                        alpineData = Alpine.$data(component);
                    } catch (e) {
                        // Alpine.$data failed
                    }
                    
                    if (alpineData && alpineData.selectedName !== undefined) {
                        // Alpine.js component is ready
                        alpineData.selectedName = value;
                        alpineData.query = value;
                        
                        // Trigger validation
                        setTimeout(() => {
                            document.dispatchEvent(new CustomEvent('signatory-selected', {
                                detail: { fieldName: fieldName, selectedName: value }
                            }));
                        }, 100);
                    } else if (attempt < maxAttempts) {
                        // Retry after delay
                        setTimeout(() => {
                            this.restoreSignatoryValue(component, fieldName, value, attempt + 1);
                        }, delay);
                    } else {
                        // Fallback: Set the hidden input value directly
                        const hiddenInput = component.querySelector('input[type="hidden"]');
                        if (hiddenInput) {
                            hiddenInput.value = value;
                        }
                    }
                },
                
                // Simple tab navigation helpers
                getNextTab() {
                    const tabs = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    return tabs[currentIndex + 1] || this.activeTab;
                },
                
                getPreviousTab() {
                    const tabs = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    return tabs[currentIndex - 1] || this.activeTab;
                },
                
                // Update submit button state
                updateSubmitButton() {
                    const submitBtn = document.querySelector('#submit-btn');
                    if (submitBtn) {
                        // If user reached review page, all fields are already validated
                        // Only check confirmation checkbox
                        const confirmChecked = this.confirmChecked;
                        submitBtn.disabled = !confirmChecked;
                    }
                    
                    // Also refresh tab states when form changes
                    this.refreshTabStates();
                },

                // Reset submit button after submission
                resetSubmitButton() {
                    const submitBtn = document.querySelector('#submit-btn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit';
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                },
                
                // Refresh tab enabled/disabled states
                refreshTabStates() {
                    // Force Alpine.js to re-evaluate the tab states
                    this.$nextTick(() => {
                        // Trigger validation for current tab
                        const currentTabValid = this.validateCurrentTab();
                        
                        // Force Alpine.js to re-render by updating a reactive property
                        // This will cause isTabEnabled() to be called for all tabs
                        this.tabStatesRefreshed = Date.now();
                    });
                },
                
                // Handle form submission - only show error popup on actual submit
                handleSubmit(event) {
                    // Prevent double submission
                    if (this.isSubmitting) {
                        event.preventDefault();
                        return false;
                    }
                    
                    if (!this.validateForm(true)) {
                        event.preventDefault();
                        // Error popup is already shown by validateForm()
                        return false;
                    }
                    
                    // Populate hidden field with generated DOCX file paths
                    const generatedFilesField = document.getElementById('generated-docx-files');
                    if (generatedFilesField && window.generatedDocxFiles) {
                        generatedFilesField.value = JSON.stringify(window.generatedDocxFiles);
                    }
                    
                    // Mark as submitting and disable submit button
                    this.isSubmitting = true;
                    const submitBtn = document.querySelector('#submit-btn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Submitting...';
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    
                    // Loading screen will be handled by Turbo events
                    
                    // Disable auto-save to prevent duplicate entries after submission
                    this.disableAutoSave();
                    
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
                                this.confirmChecked = confirmCheckbox.checked;
                                this.updateSubmitButton();
                            });
                        }
                    });
                    
                    // Setup real-time validation
                    this.setupRealTimeValidation();
                    
                    // Reset submitting state on page unload or navigation
                    window.addEventListener('beforeunload', () => {
                        this.isSubmitting = false;
                    });
                    
                    // Reset submitting state on form errors
                    window.addEventListener('error', () => {
                        this.isSubmitting = false;
                        this.resetSubmitButton();
            });
        },
                
                // Setup real-time validation with debouncing
                setupRealTimeValidation() {
                    let validationTimeout;
                    
                    // Debounced validation function
                    const debouncedValidation = () => {
                        clearTimeout(validationTimeout);
                        validationTimeout = setTimeout(() => {
                            this.refreshTabStates();
                        }, 500); // 500ms delay
                    };
                    
                    // Listen for input changes on all form fields (debounced)
                    document.addEventListener('input', (e) => {
                        if (e.target.matches('input, textarea, select')) {
                            debouncedValidation();
                        }
                    });
                    
                    // Listen for checkbox/radio changes (immediate for better UX)
                    document.addEventListener('change', (e) => {
                        if (e.target.matches('input[type="checkbox"], input[type="radio"]')) {
                            this.refreshTabStates();
                        }
                    });
                    
                    // Listen for file input changes (immediate for better UX)
                    document.addEventListener('change', (e) => {
                        if (e.target.matches('input[type="file"]')) {
                            this.refreshTabStates();
                        }
                    });
                    
                    // Listen for signatory selection changes (immediate for better UX)
                    document.addEventListener('signatory-selected', (e) => {
                        this.refreshTabStates();
                        this.autoSave(); // Trigger auto-save when signatory is selected
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
    
    <div x-data="publicationRequestData()" x-init="init()" class="h-screen bg-gray-50 flex overflow-hidden">
        
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

        <!-- Submission Loading Overlay -->
        <div x-show="isSubmitting" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center"
             style="display:none;">
            <div class="bg-white rounded-lg shadow-xl px-8 py-6 flex items-center gap-4 max-w-sm mx-4">
                <div class="animate-spin h-8 w-8 border-4 border-maroon-600 border-t-transparent rounded-full"></div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Submitting Request</h3>
                    <p class="text-sm text-gray-600">Please wait while we process your submission...</p>
                </div>
            </div>
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
        <div class="flex-1 h-screen overflow-y-auto">
            <!-- Content Area -->
            <main class="max-w-7xl mx-auto px-4 pt-2">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-2">
                    <!-- Left side - Overview Header -->
                    <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <span>Publication Request</span>
                    </div>
                    
                    <!-- Right side - User Controls -->
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
                    <!-- Tab Header -->
                    <div class="bg-white/30 backdrop-blur-md border border-white/40 rounded-t-xl shadow-xl px-6 py-4">
                        <div class="flex border-b border-maroon-200 mb-0">
                            <button type="button" 
                                class="flex-1 px-4 py-3 text-sm font-semibold text-center border-b-2 transition-all duration-200 rounded-tl-lg border-transparent hover:text-maroon-800 hover:bg-maroon-100"
                                :class="activeTab === 'incentive' ? 'border-maroon-600 text-maroon-800 bg-maroon-100' : 'text-maroon-600'"
                                @click="switchTab('incentive')">
                                Incentive Application
                            </button>
                            <button type="button" 
                                class="flex-1 px-4 py-3 text-sm font-semibold text-center border-b-2 transition-all duration-200 border-transparent hover:text-maroon-800 hover:bg-maroon-100"
                                :class="activeTab === 'recommendation' ? 'border-maroon-600 text-maroon-800 bg-maroon-100' : isTabEnabled('recommendation') ? 'text-maroon-600' : 'text-gray-400 cursor-not-allowed bg-gray-50'"
                                :disabled="!isTabEnabled('recommendation')"
                                @click="switchTab('recommendation')">
                                Recommendation Letter
                            </button>
                            <button type="button" 
                                class="flex-1 px-4 py-3 text-sm font-semibold text-center border-b-2 transition-all duration-200 border-transparent hover:text-maroon-800 hover:bg-maroon-100"
                                :class="activeTab === 'terminal' ? 'border-maroon-600 text-maroon-800 bg-maroon-100' : isTabEnabled('terminal') ? 'text-maroon-600' : 'text-gray-400 cursor-not-allowed bg-gray-50'"
                                :disabled="!isTabEnabled('terminal')"
                                @click="switchTab('terminal')">
                                Terminal Report
                            </button>
                            <button type="button" 
                                class="flex-1 px-4 py-3 text-sm font-semibold text-center border-b-2 transition-all duration-200 border-transparent hover:text-maroon-800 hover:bg-maroon-100"
                                :class="activeTab === 'upload' ? 'border-maroon-600 text-maroon-800 bg-maroon-100' : isTabEnabled('upload') ? 'text-maroon-600' : 'text-gray-400 cursor-not-allowed bg-gray-50'"
                                :disabled="!isTabEnabled('upload')"
                                @click="switchTab('upload')">
                                Upload Documents
                            </button>
                            <button type="button" 
                                class="flex-1 px-4 py-3 text-sm font-semibold text-center border-b-2 transition-all duration-200 rounded-tr-lg border-transparent hover:text-maroon-800 hover:bg-maroon-100"
                                :class="activeTab === 'review' ? 'border-maroon-600 text-maroon-800 bg-maroon-100' : isTabEnabled('review') ? 'text-maroon-600' : 'text-gray-400 cursor-not-allowed bg-gray-50'"
                                :disabled="!isTabEnabled('review')"
                                @click="switchTab('review')">
                                Review & Submit
                            </button>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="pl-6 pr-6 pb-6">
                        <form 
                            id="publication-request-form"
                            method="POST" 
                            action="{{ route('publications.submit') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                            @input="updateSubmitButton(); autoSave()"
                            @change="updateSubmitButton(); autoSave()"
                            @submit="handleSubmit($event)"
                            autocomplete="on"
                        >
                            @csrf
                            <input type="hidden" id="request_id" name="request_id" value="{{ $request->id ?? session('draft_publication_' . auth()->id()) }}">
                            
                            <!-- Hidden field for generated DOCX files -->
                            <input type="hidden" name="generated_docx_files" id="generated-docx-files" value="">
                            
                            <!-- Tab Content -->
                            <div class="min-h-[500px] bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <!-- Incentive Application Tab -->
                                <div x-show="activeTab === 'incentive'" class="space-y-6">
                                    @include('publications.incentive-application')
                                </div>

                                <!-- Recommendation Letter Tab -->
                                <div x-show="activeTab === 'recommendation'" class="space-y-6">
                                    @include('publications.recommendation-letter')
                                </div>

                                <!-- Terminal Report Tab -->
                                <div x-show="activeTab === 'terminal'" class="space-y-6">
                                    @include('publications.terminal-report')
                                </div>

                                <!-- Upload Documents Tab -->
                                <div x-show="activeTab === 'upload'" class="space-y-6">
                                    @include('publications.upload-documents')
                                </div>

                                <!-- Review & Submit Tab -->
                                <div x-show="activeTab === 'review'" class="space-y-6">
                                    @include('publications.review-submit')
                                </div>
                            </div>
                            
                            <!-- Padding card to prevent floating pill from covering content -->
                            <div class="h-16"></div>

                        </form>
                    </div>
                </div>
                
                <!-- Interactive Floating Steps Pill -->
                <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
                    <div class="bg-white/90 backdrop-blur-sm border border-maroon-200 rounded-full px-6 py-3 shadow-lg">
                        <div class="flex items-center gap-8">
                            <!-- Steps group -->
                            <div class="flex items-center gap-4">
                                <!-- Step 1: Details (Incentive + Recommendation + Terminal) -->
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :class="activeTab === 'incentive' || activeTab === 'recommendation' || activeTab === 'terminal' 
                                            ? 'bg-maroon-600 text-white' 
                                            : 'bg-maroon-200 text-maroon-800'">
                                        <span class="font-bold text-sm">1</span>
                                    </div>
                                    <span class="font-medium text-sm"
                                        :class="activeTab === 'incentive' || activeTab === 'recommendation' || activeTab === 'terminal' 
                                            ? 'text-maroon-600 font-semibold' 
                                            : 'text-maroon-800'">Details</span>
                                </div>
                                
                                <div class="w-8 h-0.5 bg-maroon-300"></div>
                                
                                <!-- Step 2: Upload -->
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :class="activeTab === 'upload' 
                                            ? 'bg-maroon-600 text-white' 
                                            : 'bg-maroon-200 text-maroon-800'">
                                        <span class="font-bold text-sm">2</span>
                                    </div>
                                    <span class="font-medium text-sm"
                                        :class="activeTab === 'upload' 
                                            ? 'text-maroon-600 font-semibold' 
                                            : 'text-maroon-800'">Upload</span>
                                </div>
                                
                                <div class="w-8 h-0.5 bg-maroon-300"></div>
                                
                                <!-- Step 3: Review -->
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :class="activeTab === 'review' 
                                            ? 'bg-maroon-600 text-white' 
                                            : 'bg-maroon-200 text-maroon-800'">
                                        <span class="font-bold text-sm">3</span>
                                    </div>
                                    <span class="font-medium text-sm"
                                        :class="activeTab === 'review' 
                                            ? 'text-maroon-600 font-semibold' 
                                            : 'text-maroon-800'">Review</span>
                                </div>
                            </div>
                            
                            <!-- Next/Submit button -->
                            <div class="flex items-center">
                                <button x-show="activeTab !== 'review'"
                                    @click="switchTab(getNextTab())"
                                    :disabled="!isTabEnabled(getNextTab())"
                                    :class="!isTabEnabled(getNextTab())
                                        ? 'px-6 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed w-20'
                                        : 'px-6 py-2 text-sm font-medium text-white bg-maroon-600 rounded-lg hover:bg-maroon-700 transition-colors w-20'"
                                    class="transition-colors">
                                    Next
                                </button>
                                <button x-show="activeTab === 'review'"
                                    id="submit-btn"
                                    @click="document.getElementById('publication-request-form').submit()"
                                    :disabled="!confirmChecked"
                                    :class="!confirmChecked
                                        ? 'px-6 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed w-20'
                                        : 'px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors w-20'"
                                    class="transition-colors text-center">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</x-app-layout> 
