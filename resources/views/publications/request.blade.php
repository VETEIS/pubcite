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
                    
                    // Trigger draft save when upload tab becomes active to generate PDFs
                    // This enables the "View PDF" buttons since PDFs are created during draft save
                    if (targetTab === 'upload') {
                        // Save draft immediately to generate PDFs (which enables buttons)
                        this.saveDraft().then(() => {
                            // After draft save completes, update button states
                            // The saveDraft response now includes request_id and request_code
                            // and updates generatedDocxPaths, so buttons should be enabled
                            setTimeout(() => {
                                this.updateDocumentButtonStates();
                            }, 1500); // Wait a bit for PDFs to be created and paths to be updated
                        }).catch(() => {
                            // Silent error - buttons will remain disabled if save fails
                        });
                    }
                    
                    // Display uploaded files when switching to review tab
                    if (targetTab === 'review') {
                        setTimeout(() => {
                            this.displayUploadedFiles();
                            // Update submit button state when switching to review
                            this.updateSubmitButton();
                            // Check file availability and update button states
                            this.updateDocumentButtonStates();
                        }, 100);
                    }
                },
                
                // Validate current tab - simple and reliable
                validateCurrentTab() {
                    const currentTab = this.activeTab;
                    
                    // Special handling for upload tab
                    if (currentTab === 'upload') {
                        const requiredFiles = ['published_article', 'indexing_evidence'];
                        
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
                            return Promise.resolve();
                        }
                        
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('save_draft', '1');
                        
                        const response = await fetch('{{ route("publications.submit") }}', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
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
                        } else if (response.status === 429) {
                            // Rate limited - disable auto-save temporarily
                            this.autoSaveDisabled = true;
                            setTimeout(() => {
                                this.autoSaveDisabled = false;
                            }, 60000); // Re-enable after 1 minute
                            return Promise.reject(new Error('Rate limited'));
                        }
                        // Silent save - no error notifications for auto-save
                        return Promise.reject(new Error('Save failed'));
                    } catch (error) {
                        // Silent error - don't show notifications for auto-save
                        return Promise.reject(error);
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
                    
                    // Clear existing timer
                    if (this.autoSaveTimer) {
                        clearTimeout(this.autoSaveTimer);
                    }
                    
                    // Set new timer - save after 5 seconds of inactivity (reduced frequency)
                    this.autoSaveTimer = setTimeout(() => {
                        // Double-check before saving
                        if (!this.autoSaveDisabled && !this.isSubmitting && !this.savingDraft) {
                            this.saveDraft();
                        }
                    }, 5000);
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
                            facultyNameDisplay.textContent = draftData.name;
                        }
                        
                        const recFacultyNameField = document.getElementById('rec_faculty_name');
                        const recFacultyNameDisplay = document.getElementById('rec-faculty-name-display');
                        if (recFacultyNameField) {
                            recFacultyNameField.value = draftData.name;
                        }
                        if (recFacultyNameDisplay) {
                            recFacultyNameDisplay.textContent = draftData.name;
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
                        { fieldName: 'indexing_evidence', elementId: 'review-indexing-evidence' }
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
                
                // Auto-generate DOCX files in background when upload tab becomes active
                async autoGenerateDocxFiles() {
                    // Generate all three documents in parallel
                    await Promise.all([
                        this.generateDocxInBackground('incentive'),
                        this.generateDocxInBackground('recommendation'),
                        this.generateDocxInBackground('terminal')
                    ]);
                },
                
                // Generate a single DOCX file in background
                async generateDocxInBackground(docxType) {
                    // Check if already generating
                    if (this.generatingDocs[docxType]) {
                        return;
                    }
                    
                    // Calculate current form data hash
                    const currentHash = this.calculateFormDataHash(docxType);
                    
                    // Check if regeneration is needed (form data changed)
                    if (this.formDataHashes[docxType] === currentHash && this.generatedDocxPaths[docxType]) {
                        // No changes, file already generated
                        return;
                    }
                    
                    // Mark as generating
                    this.generatingDocs[docxType] = true;
                    
                    try {
                        const form = document.getElementById('publication-request-form');
                        if (!form) return;
                        
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
                                console.log(`Background DOCX generated: ${docxType}`, result.filePath);
                            } else {
                                console.warn(`Background DOCX generation failed for ${docxType}:`, result);
                            }
                        } else {
                            console.error(`Background DOCX generation HTTP error for ${docxType}:`, response.status);
                        }
                    } catch (error) {
                        // Silent fail - background generation shouldn't interrupt user
                    } finally {
                        this.generatingDocs[docxType] = false;
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
                
                // Check if file (PDF or DOCX) exists for a given path
                async checkFileExists(type, docxPath) {
                    if (!docxPath) return false;
                    
                    try {
                        // First check PDF (preferred)
                        const pdfPath = docxPath.replace(/\.docx$/, '.pdf');
                        const pdfResponse = await fetch(`{{ route("publications.generate") }}?file_path=${encodeURIComponent(pdfPath)}&docx_type=${type}`, {
                            method: 'HEAD',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (pdfResponse.ok) return true;
                        
                        // Fallback: check DOCX
                        const docxResponse = await fetch(`{{ route("publications.generate") }}?file_path=${encodeURIComponent(docxPath)}&docx_type=${type}`, {
                            method: 'HEAD',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        return docxResponse.ok;
                    } catch (error) {
                        return false;
                    }
                },
                
                // Update button enabled/disabled state based on file availability
                async updateDocumentButtonStates() {
                    const types = ['incentive', 'recommendation'];
                    const buttonIds = {
                        'incentive': 'incentive-doc-button',
                        'recommendation': 'recommendation-doc-button'
                    };
                    
                    const requestId = document.getElementById('request_id')?.value;
                    
                    // Try to get request code from form or construct from request structure
                    let requestCode = null;
                    let userId = null;
                    
                    // Get request code from hidden input if available
                    const requestCodeInput = document.querySelector('input[name="request_code"]');
                    if (requestCodeInput) {
                        requestCode = requestCodeInput.value;
                    }
                    
                    // Get user ID from form or construct from URL
                    const userIdInput = document.querySelector('input[name="user_id"]');
                    if (userIdInput) {
                        userId = userIdInput.value;
                    } else {
                        // Try to extract from URL or use a default
                        // For now, we'll construct paths based on request_id if available
                    }
                    
                    // If we have request_id, we can construct paths
                    // PDFs are stored in: requests/{userId}/{requestCode}/{filename}.pdf
                    // But we need requestCode, which we might not have directly
                    
                    // Strategy: Check for PDFs using the paths from generatedDocxPaths first
                    // If not available, try to construct paths from request structure
                    for (const type of types) {
                        const button = document.getElementById(buttonIds[type]);
                        if (!button) continue;
                        
                        let docxPath = null;
                        
                        // First, try generatedDocxPaths (from "View PDF" clicks)
                        if (this.generatedDocxPaths && this.generatedDocxPaths[type]) {
                            docxPath = this.generatedDocxPaths[type];
                        } else if (requestId && requestCode) {
                            // Construct path from request structure
                            const fileName = type === 'incentive' 
                                ? 'Incentive_Application_Form.docx'
                                : type === 'recommendation'
                                ? 'Recommendation_Letter_Form.docx'
                                : 'Terminal_Report_Form.docx';
                            
                            if (userId) {
                                docxPath = `requests/${userId}/${requestCode}/${fileName}`;
                            }
                        }
                        
                        if (docxPath) {
                            const fileExists = await this.checkFileExists(type, docxPath);
                            
                            if (fileExists) {
                                // File exists (PDF or DOCX) - enable button
                                button.classList.remove('opacity-50', 'cursor-not-allowed');
                                button.classList.add('cursor-pointer', 'hover:shadow-md');
                                button.style.pointerEvents = 'auto';
                                
                                // Store the path for future reference
                                if (!this.generatedDocxPaths) {
                                    this.generatedDocxPaths = {};
                                }
                                if (!this.generatedDocxPaths[type]) {
                                    // Store PDF path if it exists, otherwise DOCX path
                                    const pdfPath = docxPath.replace(/\.docx$/, '.pdf');
                                    this.generatedDocxPaths[type] = pdfPath;
                                }
                            } else {
                                // File doesn't exist - disable button
                                button.classList.add('opacity-50', 'cursor-not-allowed');
                                button.classList.remove('hover:shadow-md');
                                button.style.pointerEvents = 'none';
                            }
                        } else {
                            // No path available - try to find files by checking common locations
                            // This is a fallback for when we don't have request info yet
                            // We'll check if files exist in the expected draft location
                            if (requestId) {
                                // Try to fetch request info or check files directly
                                // For now, disable button if we can't determine path
                                button.classList.add('opacity-50', 'cursor-not-allowed');
                                button.classList.remove('hover:shadow-md');
                                button.style.pointerEvents = 'none';
                            } else {
                                // No request ID - disable button (new request, files not generated yet)
                                button.classList.add('opacity-50', 'cursor-not-allowed');
                                button.classList.remove('hover:shadow-md');
                                button.style.pointerEvents = 'none';
                            }
                        }
                    }
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
                    
                    // Add pre-generated DOCX file paths to form before submission
                    const form = document.getElementById('publication-request-form');
                    if (form) {
                        // Remove any existing generated_docx_files inputs
                        const existingInputs = form.querySelectorAll('input[name^="generated_docx_files"]');
                        existingInputs.forEach(input => input.remove());
                        
                        // Debug: Log what we're about to send
                        console.log('Pre-generated DOCX paths:', this.generatedDocxPaths);
                        
                        // Add pre-generated file paths if they exist
                        if (this.generatedDocxPaths.incentive) {
                            const incentiveInput = document.createElement('input');
                            incentiveInput.type = 'hidden';
                            incentiveInput.name = 'generated_docx_files[incentive]';
                            incentiveInput.value = this.generatedDocxPaths.incentive;
                            form.appendChild(incentiveInput);
                            console.log('Added incentive file path:', this.generatedDocxPaths.incentive);
                        }
                        
                        if (this.generatedDocxPaths.recommendation) {
                            const recommendationInput = document.createElement('input');
                            recommendationInput.type = 'hidden';
                            recommendationInput.name = 'generated_docx_files[recommendation]';
                            recommendationInput.value = this.generatedDocxPaths.recommendation;
                            form.appendChild(recommendationInput);
                            console.log('Added recommendation file path:', this.generatedDocxPaths.recommendation);
                        }
                        
                    }
                    
                    // Submit the form manually after a short delay to ensure loading screen shows
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
                        facultyNameDisplay.textContent = nameValue || '';
                    }
                    
                    // Also sync rec_faculty_name for recommendation letter
                    const recFacultyNameField = document.getElementById('rec_faculty_name');
                    const recFacultyNameDisplay = document.getElementById('rec-faculty-name-display');
                    if (nameField && recFacultyNameField && recFacultyNameDisplay) {
                        const nameValue = nameField.value.trim();
                        recFacultyNameField.value = nameValue;
                        recFacultyNameDisplay.textContent = nameValue || '';
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
                    // Load draft data after a short delay to ensure DOM is ready
                    setTimeout(() => {
                        this.loadDraftData();
                        // Sync faculty_name after draft data is loaded
                        setTimeout(() => {
                            this.syncFacultyName();
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
