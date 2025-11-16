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
                uploadedFiles: {
                    published_article: null,
                    indexing_evidence: null,
                    terminal_report: null
                },
                formDataHashes: {
                    incentive: null,
                    recommendation: null
                },
                generatingDocs: {
                    incentive: false,
                    recommendation: false
                },
                generatedDocxPaths: {
                    incentive: null,
                    recommendation: null
                },
                uploadTabVisited: false, // Track if upload tab has been visited
                reviewTabVisited: false, // Track if review tab has been visited
                needsRegeneration: {
                    incentive: false,
                    recommendation: false
                },
                pdfRegenerationTimer: null, // Timer for debounced PDF regeneration
                pdfGenerationStatus: {
                    incentive: 'idle', // 'idle', 'generating', 'ready', 'error'
                    recommendation: 'idle'
                },
                
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
                    
                    // Validate that at least one indexing option is selected
                    const scopus = document.querySelector('input[name="scopus"]');
                    const wos = document.querySelector('input[name="wos"]');
                    const aci = document.querySelector('input[name="aci"]');
                    const others = document.querySelector('select[name="others"]');
                    
                    const hasScopus = scopus && scopus.checked;
                    const hasWos = wos && wos.checked;
                    const hasAci = aci && aci.checked;
                    const hasOthers = others && others.value && others.value.trim() !== '';
                    
                    if (!hasScopus && !hasWos && !hasAci && !hasOthers) {
                        allValid = false;
                        if (showError) {
                            this.showError('Please select at least one indexing option (Scopus, Web of Science, ACI, or Others).');
                            // Highlight the indexing section
                            const scopusLabel = document.querySelector('input[name="scopus"]')?.closest('label');
                            const indexedSection = scopusLabel?.closest('.space-y-4');
                            if (indexedSection) {
                                indexedSection.classList.add('ring-2', 'ring-red-500', 'ring-offset-2');
                                setTimeout(() => indexedSection.classList.remove('ring-2', 'ring-red-500', 'ring-offset-2'), 3000);
                                indexedSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                    }
                    
                    if (!allValid && firstInvalidField && showError) {
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                        if (!hasScopus && !hasWos && !hasAci && !hasOthers) {
                            // Error already shown above
                        } else {
                            this.showError('Please complete all required fields before submitting.');
                        }
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
                    // Sync faculty_name when switching to incentive or recommendation tab
                    if (targetTab === 'incentive' || targetTab === 'recommendation') {
                        this.$nextTick(() => {
                            this.syncFacultyName();
                        });
                    }
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    const targetIndex = tabs.indexOf(targetTab);
                    
                    // Always allow going back or staying on same tab
                    if (targetIndex <= currentIndex) {
                        this.activeTab = targetTab;
                        
                        // Sync college when switching to recommendation tab
                        if (targetTab === 'recommendation') {
                            this.syncCollegeToRecommendation();
                        }
                        
                        // Display uploaded files when switching to review tab
                        if (targetTab === 'review') {
                            setTimeout(() => {
                                this.displayUploadedFiles();
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
                    
                    // Sync college when switching to recommendation tab
                    if (targetTab === 'recommendation') {
                        this.syncCollegeToRecommendation();
                    }
                    
                    // Generate PDFs when upload tab becomes active (forms are complete)
                    if (targetTab === 'upload') {
                        this.uploadTabVisited = true;
                        // Generate PDFs in background
                        this.generatePdfsInBackground();
                    }
                    
                    // Display uploaded files when switching to review tab
                    if (targetTab === 'review') {
                        this.reviewTabVisited = true;
                        setTimeout(() => {
                            this.displayUploadedFiles();
                            // Update submit button state when switching to review
                            this.updateSubmitButton();
                            // Generate PDFs if hash changed (lazy generation)
                            this.generatePdfsIfNeeded();
                        }, 100);
                    }
                },
                
                // Sync college from incentive form to recommendation form
                syncCollegeToRecommendation() {
                    const collegeField = document.querySelector('select[name="college"]');
                    const recCollegeField = document.querySelector('select[name="rec_collegeheader"]');
                    
                    if (collegeField && recCollegeField) {
                        const selectedCollege = collegeField.value;
                        // Sync if incentive college is selected
                        // Only update if recommendation is empty or matches the current incentive value (was previously synced)
                        if (selectedCollege) {
                            const currentRecCollege = recCollegeField.value;
                            // Sync if empty, or if it currently matches (meaning it was synced before)
                            if (!currentRecCollege || currentRecCollege === '' || currentRecCollege === selectedCollege) {
                                recCollegeField.value = selectedCollege;
                                // Trigger change event to ensure auto-save picks it up
                                recCollegeField.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }
                    }
                },
                
                // Validate current tab - simple and reliable
                validateCurrentTab() {
                    const currentTab = this.activeTab;
                    
                    // Special handling for upload tab
                    if (currentTab === 'upload') {
                        const requiredFiles = ['published_article', 'indexing_evidence', 'terminal_report'];
                        
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
                        'recommendation': ['rec_collegeheader', 'rec_dean_name', 'rec_publication_details', 'rec_indexing_details'],
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
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                    const currentIndex = tabs.indexOf(this.activeTab);
                    return tabs[currentIndex + 1] || 'review';
                },
                
                // Check if a tab should be enabled (progressive unlocking)
                isTabEnabled(tabName) {
                    // Use the reactive property to force re-evaluation
                    const _ = this.tabStatesRefreshed;
                    
                    const tabs = ['incentive', 'recommendation', 'upload', 'review'];
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
                        
                        if (!form) {
                            // Form doesn't exist (page might be unloading) - silently skip
                            this.savingDraft = false;
                            return Promise.resolve();
                        }
                        
                        // Collect ALL form data from ALL tabs (including hidden ones)
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
                            } else if (input.tagName === 'SELECT') {
                                // Handle select elements explicitly
                                // Always include select values if they're not disabled and have a name
                                if (!input.disabled && input.name) {
                                    const value = input.value || '';
                                    // Include even empty values for selects (they might be cleared)
                                    formData.append(input.name, value);
                                    // Only mark as hasData if there's an actual value
                                    if (value.trim() !== '') {
                                        hasData = true;
                                    }
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
                            return Promise.resolve();
                        }
                        
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('save_draft', '1');
                        
                        const response = await fetch('{{ route("publications.submit") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            if (data.success) {
                                this.lastSaved = new Date().toLocaleTimeString();
                                
                                // Update request_id and request_code if provided (from draft save response)
                                if (data.request_id) {
                                    const requestIdInput = document.getElementById('request_id');
                                    if (requestIdInput) {
                                        requestIdInput.value = data.request_id;
                                    } else {
                                        // Create hidden input if it doesn't exist
                                        const form = document.getElementById('publication-request-form');
                                        if (form) {
                                            const input = document.createElement('input');
                                            input.type = 'hidden';
                                            input.id = 'request_id';
                                            input.name = 'request_id';
                                            input.value = data.request_id;
                                            form.appendChild(input);
                                        }
                                    }
                                }
                                
                                if (data.request_code) {
                                    let requestCodeInput = document.querySelector('input[name="request_code"]');
                                    if (!requestCodeInput) {
                                        // Create hidden input if it doesn't exist
                                        const form = document.getElementById('publication-request-form');
                                        if (form) {
                                            requestCodeInput = document.createElement('input');
                                            requestCodeInput.type = 'hidden';
                                            requestCodeInput.name = 'request_code';
                                            requestCodeInput.value = data.request_code;
                                            form.appendChild(requestCodeInput);
                                        }
                                    } else {
                                        requestCodeInput.value = data.request_code;
                                    }
                                    
                                    // Store PDF paths in generatedDocxPaths for button state management
                                    if (!this.generatedDocxPaths) {
                                        this.generatedDocxPaths = {};
                                    }
                                    
                                    const userId = {{ Auth::id() ?? 'null' }};
                                    if (userId && data.request_code) {
                                        const types = ['incentive', 'recommendation'];
                                        const fileNames = {
                                            'incentive': 'Incentive_Application_Form.pdf',
                                            'recommendation': 'Recommendation_Letter_Form.pdf'
                                        };
                                        
                                        types.forEach(type => {
                                            if (!this.generatedDocxPaths[type]) {
                                                this.generatedDocxPaths[type] = `requests/${userId}/${data.request_code}/${fileNames[type]}`;
                                            }
                                        });
                                    }
                                }
                                
                                return Promise.resolve();
                            }
                            // Save failed - silently resolve to prevent uncaught promise errors
                            return Promise.resolve();
                        } else if (response.status === 422) {
                            // Validation errors - silently resolve, will retry on next auto-save
                            return Promise.resolve();
                        } else if (response.status === 429) {
                            // Rate limited - this shouldn't happen for drafts, but handle gracefully
                            // Disable auto-save temporarily
                            this.autoSaveDisabled = true;
                            setTimeout(() => {
                                this.autoSaveDisabled = false;
                            }, 60000); // Re-enable after 1 minute
                            // Silently resolve to prevent uncaught promise errors
                            return Promise.resolve();
                        } else {
                            // Try to parse error response
                            try {
                                const errorData = await response.json();
                                if (errorData.message) {
                                    // Log error but don't show to user (silent auto-save)
                                }
                            } catch (e) {
                                // Ignore parse errors
                            }
                            // Silently resolve to prevent uncaught promise errors
                            return Promise.resolve();
                        }
                    } catch (error) {
                        // Silent error - resolve instead of reject to prevent uncaught promise errors
                        // Auto-save failures should be silent
                        return Promise.resolve();
                    } finally {
                        this.savingDraft = false;
                    }
                },
                
                // Debounced auto-save with rate limiting protection
                autoSave() {
                    // Don't auto-save if disabled (e.g., after form submission)
                    if (this.autoSaveDisabled) {
                        return;
                    }
                    
                    // Don't auto-save if currently submitting
                    if (this.isSubmitting) {
                        return;
                    }
                    
                    // Don't auto-save if already saving
                    if (this.savingDraft) {
                        return;
                    }
                    
                    // If upload tab has been visited, check if regeneration is needed
                    if (this.uploadTabVisited) {
                        // Calculate current hashes
                        const incentiveHash = this.calculateFormDataHash('incentive');
                        const recommendationHash = this.calculateFormDataHash('recommendation');
                        
                        // Check if hashes actually changed
                        const incentiveChanged = this.formDataHashes.incentive !== incentiveHash;
                        const recommendationChanged = this.formDataHashes.recommendation !== recommendationHash;
                        
                        // Only mark for regeneration if hash changed
                        if (incentiveChanged) {
                            this.needsRegeneration.incentive = true;
                        }
                        if (recommendationChanged) {
                            this.needsRegeneration.recommendation = true;
                        }
                        
                        // Only trigger regeneration if something changed AND review tab has been visited
                        // (If review tab not visited yet, generate lazily when user switches to it)
                        if ((incentiveChanged || recommendationChanged) && this.reviewTabVisited) {
                            // Debounce PDF regeneration (5 seconds after last form change)
                            if (this.pdfRegenerationTimer) {
                                clearTimeout(this.pdfRegenerationTimer);
                            }
                            this.pdfRegenerationTimer = setTimeout(() => {
                                this.generatePdfsInBackground();
                            }, 5000); // Wait 5 seconds after last change
                        }
                    }
                    
                    // Clear existing timer
                    if (this.autoSaveTimer) {
                        clearTimeout(this.autoSaveTimer);
                    }
                    
                    // Set new timer - save after 2 seconds of inactivity
                    this.autoSaveTimer = setTimeout(() => {
                        // Double-check before saving
                        if (!this.autoSaveDisabled && !this.isSubmitting && !this.savingDraft) {
                            this.saveDraft();
                        }
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
                        'center_manager', 'dean_name',  // Incentive tab (faculty_name is auto-populated from name)
                        'rec_dean_name'             // Recommendation tab (rec_faculty_name is auto-populated from name)
                    ];
                    
                    // Auto-populate faculty_name and rec_faculty_name from name field
                    if (draftData.name) {
                        const facultyNameField = document.getElementById('faculty_name');
                        const facultyNameDisplay = document.getElementById('faculty-name-display');
                        if (facultyNameField) {
                            facultyNameField.value = draftData.name;
                        }
                        if (facultyNameDisplay) {
                            facultyNameDisplay.textContent = draftData.name ? draftData.name.toUpperCase() : '';
                        }
                        
                        const recFacultyNameField = document.getElementById('rec_faculty_name');
                        const recFacultyNameDisplay = document.getElementById('rec-faculty-name-display');
                        if (recFacultyNameField) {
                            recFacultyNameField.value = draftData.name;
                        }
                        if (recFacultyNameDisplay) {
                            recFacultyNameDisplay.textContent = draftData.name ? draftData.name.toUpperCase() : '';
                        }
                    }
                    
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
                
                // Update uploaded file state
                updateUploadedFile(fieldName, fileName) {
                    this.uploadedFiles[fieldName] = fileName;
                    // Trigger review tab update if on review tab
                    if (this.activeTab === 'review') {
                        setTimeout(() => {
                            this.displayUploadedFiles();
                        }, 100);
                    }
                },
                
                // Display uploaded files in review tab
                displayUploadedFiles() {
                    
                    // File state mapping
            const fileFields = [
                        { fieldName: 'published_article', elementId: 'review-published-article' },
                        { fieldName: 'indexing_evidence', elementId: 'review-indexing-evidence' },
                        { fieldName: 'terminal_report', elementId: 'review-terminal-report' }
                    ];
                    
                    fileFields.forEach(({ fieldName, elementId }) => {
                        const fileName = this.uploadedFiles[fieldName];
                        const element = document.getElementById(elementId);
                        
                        if (element) {
                            if (fileName) {
                                const displayName = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
                    element.textContent = displayName;
                                element.title = fileName;
                                element.classList.remove('text-gray-600');
                                element.classList.add('text-green-600', 'font-medium');
                            } else {
                    element.textContent = 'No file uploaded';
                    element.title = '';
                                element.classList.remove('text-green-600', 'font-medium');
                                element.classList.add('text-gray-600');
                            }
                }
            });
                },
                
                // Calculate hash of form data for a specific tab to detect changes
                calculateFormDataHash(tabType) {
                    const form = document.getElementById('publication-request-form');
                    if (!form) return null;
                    
                    // Get fields specific to each tab
                    const tabFields = {
                        'incentive': ['name', 'rank', 'college', 'title', 'journal', 'publisher', 'issn', 'doi', 'scopus', 'wos', 'aci', 'citescore', 'faculty_name', 'center_manager', 'dean_name', 'date'],
                        'recommendation': ['name', 'rec_faculty_name', 'rec_collegeheader', 'rec_citing_details', 'rec_indexing_details', 'rec_dean_name', 'date']
                    };
                    
                    const fields = tabFields[tabType] || [];
                    const data = {};
                    
                    fields.forEach(fieldName => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            if (field.type === 'checkbox' || field.type === 'radio') {
                                data[fieldName] = field.checked ? (field.value || '1') : '0';
                            } else {
                                data[fieldName] = field.value || '';
                            }
                        }
                    });
                    
                    // Create stable hash
                    const json = JSON.stringify(data);
                    return btoa(json).replace(/[^a-zA-Z0-9]/g, '').substring(0, 16);
                },
                
                // Generate PDFs if needed (check hash first)
                generatePdfsIfNeeded() {
                    const incentiveHash = this.calculateFormDataHash('incentive');
                    const recommendationHash = this.calculateFormDataHash('recommendation');
                    
                    const incentiveNeeded = !this.generatedDocxPaths.incentive || 
                                          this.formDataHashes.incentive !== incentiveHash;
                    const recommendationNeeded = !this.generatedDocxPaths.recommendation || 
                                               this.formDataHashes.recommendation !== recommendationHash;
                    
                    if (incentiveNeeded || recommendationNeeded) {
                        this.generatePdfsInBackground();
                    }
                },
                
                // Generate PDFs in background when upload tab is visited
                async generatePdfsInBackground() {
                    // Generate incentive and recommendation PDFs in parallel (terminal report is separate)
                    await Promise.all([
                        this.generatePdfInBackground('incentive'),
                        this.generatePdfInBackground('recommendation')
                    ]);
                },
                
                // Generate a single PDF file in background (with regeneration on form changes)
                async generatePdfInBackground(docxType, retryCount = 0) {
                    // Check if already generating
                    if (this.generatingDocs[docxType]) {
                        return;
                    }
                    
                    // Calculate current form data hash
                    const currentHash = this.calculateFormDataHash(docxType);
                    
                    // Check if regeneration is needed (form data changed or not yet generated)
                    const needsRegen = this.needsRegeneration[docxType] || 
                                      this.formDataHashes[docxType] !== currentHash || 
                                      !this.generatedDocxPaths[docxType];
                    
                    if (!needsRegen && this.generatedDocxPaths[docxType]) {
                        // No changes, file already generated
                        return;
                    }
                    
                    // Mark as generating
                    this.generatingDocs[docxType] = true;
                    this.needsRegeneration[docxType] = false;
                    this.pdfGenerationStatus[docxType] = 'generating';
                    
                    try {
                        const form = document.getElementById('publication-request-form');
                        if (!form) {
                            this.pdfGenerationStatus[docxType] = 'error';
                            return;
                        }
                        
                        // Create FormData but exclude file inputs
                        const formData = new FormData();
                        const formElements = form.querySelectorAll('input, textarea, select');
                        
                        formElements.forEach(element => {
                            if (element.type === 'file') return;
                            
                            if (element.type === 'checkbox' || element.type === 'radio') {
                                if (element.checked) {
                                    formData.append(element.name, element.value || '1');
                                }
                            } else if (element.name && element.value !== null) {
                                formData.append(element.name, element.value);
                            }
                        });
                        
                        formData.append('docx_type', docxType);
                        formData.append('store_for_submit', '1'); // Store for later use in submission
                        formData.append('force_regenerate', '1'); // Always force regeneration to ensure latest data
                        
                        const response = await fetch('{{ route("publications.generate") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (response.ok) {
                            const result = await response.json();
                            if (result.success && result.filePath) {
                                // Store the generated file path
                                this.generatedDocxPaths[docxType] = result.filePath;
                                // Update hash to mark as generated
                                this.formDataHashes[docxType] = currentHash;
                                this.pdfGenerationStatus[docxType] = 'ready';
                            } else {
                                throw new Error(result.message || 'PDF generation failed');
                            }
                        } else {
                            const errorText = await response.text();
                            throw new Error(`HTTP ${response.status}: ${errorText}`);
                        }
                    } catch (error) {
                        console.error(`Background PDF generation error for ${docxType}:`, error);
                        
                        // Retry logic (max 2 retries)
                        if (retryCount < 2) {
                            console.log(`Retrying PDF generation for ${docxType} (attempt ${retryCount + 1})`);
                            setTimeout(() => {
                                this.generatingDocs[docxType] = false;
                                this.generatePdfInBackground(docxType, retryCount + 1);
                            }, 3000); // Wait 3 seconds before retry
                            return;
                        } else {
                            // Max retries reached, mark as error
                            this.pdfGenerationStatus[docxType] = 'error';
                            console.error(`PDF generation failed after ${retryCount + 1} attempts for ${docxType}`);
                        }
                    } finally {
                        if (this.pdfGenerationStatus[docxType] !== 'generating' || retryCount >= 2) {
                            this.generatingDocs[docxType] = false;
                        }
                    }
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
                
                // Setup form change listeners to detect form data changes and trigger regeneration
                setupFormChangeListeners() {
                    const form = document.getElementById('publication-request-form');
                    if (!form) return;
                    
                    // Listen for changes on incentive tab fields
                    const incentiveFields = ['name', 'rank', 'college', 'title', 'journal', 'publisher', 'issn', 'doi', 'scopus', 'wos', 'aci', 'citescore', 'faculty_name', 'center_manager', 'dean_name', 'date'];
                    incentiveFields.forEach(fieldName => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            field.addEventListener('change', () => {
                                // Invalidate incentive hash to trigger regeneration
                                this.formDataHashes.incentive = null;
                                this.generatedDocxPaths.incentive = null;
                            });
                            field.addEventListener('input', () => {
                                // Also listen to input for real-time updates
                                this.formDataHashes.incentive = null;
                                this.generatedDocxPaths.incentive = null;
                            });
                        }
                    });
                    
                    // Listen for changes on recommendation tab fields
                    const recommendationFields = ['name', 'rec_faculty_name', 'rec_collegeheader', 'rec_citing_details', 'rec_indexing_details', 'rec_dean_name', 'date'];
                    recommendationFields.forEach(fieldName => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            field.addEventListener('change', () => {
                                // Invalidate recommendation hash to trigger regeneration
                                this.formDataHashes.recommendation = null;
                                this.generatedDocxPaths.recommendation = null;
                            });
                            field.addEventListener('input', () => {
                                // Also listen to input for real-time updates
                                this.formDataHashes.recommendation = null;
                                this.generatedDocxPaths.recommendation = null;
                            });
                        }
                    });
                    
                },
                
                // Handle form submission - only show error popup on actual submit
                handleSubmit(event) {
                    
                    // CRITICAL: Disable auto-save IMMEDIATELY to prevent conflicts
                    this.disableAutoSave();
                    
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
                    
                    // Mark as submitting and disable submit button
                    this.isSubmitting = true;
                    const submitBtn = document.querySelector('#submit-btn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    
                    // Show real progress tracking
                    const progressSteps = [
                        'Starting submission...',
                        'Processing file uploads...',
                        'Creating admin notifications...',
                        'Sending email notifications...',
                        'Finalizing request...',
                        'Request submitted successfully!'
                    ];
                    window.showLoading('Submitting Request', 'Please wait while we process your publication request...', progressSteps, true);
                    
                    // Prevent default form submission and submit manually
                    event.preventDefault();
                    
                    // Submit the form manually after a short delay to ensure loading screen shows
                    const form = document.getElementById('publication-request-form');
                    setTimeout(() => {
                        if (form) {
                            form.submit();
                        }
                    }, 100);
                    
                    return false;
                },
                
                // Initialize form
                // Sync faculty_name from name field
                syncFacultyName() {
                    const nameField = document.querySelector('input[name="name"]');
                    const facultyNameField = document.getElementById('faculty_name');
                    const facultyNameDisplay = document.getElementById('faculty-name-display');
                    if (nameField && facultyNameField && facultyNameDisplay) {
                        const nameValue = nameField.value.trim();
                        facultyNameField.value = nameValue;
                        facultyNameDisplay.textContent = nameValue ? nameValue.toUpperCase() : '';
                    }
                    
                    // Also sync rec_faculty_name for recommendation letter
                    const recFacultyNameField = document.getElementById('rec_faculty_name');
                    const recFacultyNameDisplay = document.getElementById('rec-faculty-name-display');
                    if (nameField && recFacultyNameField && recFacultyNameDisplay) {
                        const nameValue = nameField.value.trim();
                        recFacultyNameField.value = nameValue;
                        recFacultyNameDisplay.textContent = nameValue ? nameValue.toUpperCase() : '';
                    }
                },
                
                init() {
                    // Sync faculty_name on initialization
                    this.syncFacultyName();
                    
                    // Add form change listeners to trigger regeneration when form data changes
                    this.setupFormChangeListeners();
                    
                    // Set up listener for name field changes
                    const nameField = document.querySelector('input[name="name"]');
                    if (nameField) {
                        nameField.addEventListener('input', () => this.syncFacultyName());
                        nameField.addEventListener('change', () => this.syncFacultyName());
                    }
                    
                    // Set up listener for college field changes (incentive form)
                    const collegeField = document.querySelector('select[name="college"]');
                    if (collegeField) {
                        collegeField.addEventListener('change', () => {
                            // Sync to recommendation form when college changes
                            this.syncCollegeToRecommendation();
                        });
                    }
                    
                    // Load draft data after a short delay to ensure DOM is ready
                    setTimeout(() => {
                        this.loadDraftData();
                        // Sync faculty_name after draft data is loaded
                        setTimeout(() => {
                            this.syncFacultyName();
                            // Sync college after draft data is loaded
                            this.syncCollegeToRecommendation();
                        }, 100);
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
    
    <style>
        /* Force main content scrollbar to always be present to prevent layout shifts */
        .main-content-scroll {
            overflow-y: scroll !important;
        }
    </style>
    
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
        <div class="flex-1 h-screen overflow-y-auto main-content-scroll">
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
                <div class="bg-white/30 backdrop-blur-md border border-white/40 rounded-xl shadow-xl overflow-hidden mb-8">
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
                    <div class="pl-6 pr-6 pb-6 flex-1 flex flex-col">
                        <form 
                            id="publication-request-form"
                            method="POST" 
                            action="{{ route('publications.submit') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6 flex-1 flex flex-col"
                            @input="updateSubmitButton(); autoSave()"
                            @change="updateSubmitButton(); autoSave()"
                            @submit="handleSubmit($event)"
                            autocomplete="on"
                        >
                            @csrf
                            <input type="hidden" id="request_id" name="request_id" value="{{ $request->id }}">
                            
                            <!-- Tab Content -->
                            <!-- Incentive Application Tab -->
                            <div x-show="activeTab === 'incentive'" class="space-y-6">
                                @include('publications.incentive-application')
                            </div>

                            <!-- Recommendation Letter Tab -->
                            <div x-show="activeTab === 'recommendation'" class="space-y-6">
                                @include('publications.recommendation-letter')
                            </div>

                            <!-- Upload Documents Tab -->
                            <div x-show="activeTab === 'upload'" class="space-y-6">
                                @include('publications.upload-documents')
                            </div>

                            <!-- Review & Submit Tab -->
                            <div x-show="activeTab === 'review'" class="space-y-6">
                                @include('publications.review-submit')
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
                                        :class="activeTab === 'incentive' || activeTab === 'recommendation' 
                                            ? 'bg-maroon-600 text-white' 
                                            : 'bg-maroon-200 text-maroon-800'">
                                        <span class="font-bold text-sm">1</span>
                                    </div>
                                    <span class="font-medium text-sm"
                                        :class="activeTab === 'incentive' || activeTab === 'recommendation' 
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
                                        ? 'px-6 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed w-20 flex items-center justify-center'
                                        : 'px-6 py-2 text-sm font-medium text-white bg-maroon-600 rounded-lg hover:bg-maroon-700 transition-colors w-20 flex items-center justify-center'"
                                    class="transition-colors">
                                    Next
                                </button>
                                <button x-show="activeTab === 'review'"
                                    id="submit-btn"
                                    @click="handleSubmit($event)"
                                    :disabled="!confirmChecked"
                                    :class="!confirmChecked
                                        ? 'px-6 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed w-20 flex items-center justify-center'
                                        : 'px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors w-20 flex items-center justify-center'"
                                    class="transition-colors">
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
