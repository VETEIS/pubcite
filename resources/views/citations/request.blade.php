<x-app-layout>
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4 pb-20">
    <div class="w-full max-w-4xl mx-auto">
        <!-- Main Form Card - Fixed Height for Consistency -->
        <div class="bg-white/30 backdrop-blur-md border border-white/40 overflow-hidden shadow-xl sm:rounded-lg p-0 relative h-[calc(90vh-4rem)] flex flex-col">
            <div class="flex flex-col items-center text-center p-6">
                <h2 class="text-xl font-bold text-burgundy-800 mb-1">Citation Request</h2>
                <p class="text-sm text-gray-600">Fill out all required forms and upload documents to submit your citation request</p>
            </div>
            <div class="flex-1 overflow-y-auto px-6 pb-6">
                <div x-data="{ showError: false, errorMsg: '' }" x-ref="errorBanner" class="relative">
                    <template x-if="showError">
                        <div class="fixed inset-0 z-50 flex items-center justify-center">
                            <div class="fixed inset-0 bg-black/30" @click="showError = false"></div>
                            <div class="relative bg-white rounded-xl shadow-xl border border-gray-200 p-5 w-full max-w-md">
                                <div class="text-base font-semibold text-gray-800 mb-1">Incomplete form</div>
                                <div class="text-sm text-gray-600 mb-4" x-text="errorMsg"></div>
                                <div class="text-right">
                                    <button @click="showError = false" type="button" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-burgundy-600 text-white hover:bg-burgundy-700">OK</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <form 
                        id="citation-request-form"
                        method="POST" 
                        action="{{ route('citations.submit') }}" 
                        enctype="multipart/form-data" 
                        class="space-b-3"
                        x-data="citationForm()"
                        x-ref="citationForm"
                        @input="checkFilled()"
                        @change="checkFilled()"
                        x-init="checkFilled()"
                        autocomplete="on"
                    >
                        @csrf
                        <div x-data x-init="Alpine.store('tabNav').checkTabs()" class="flex flex-col">
                            <div class="flex w-full border-b mb-3 sticky top-0 z-20 bg-white/30 backdrop-blur">
                                <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                    :class="[
                                        $store.tabNav.tab === 'incentive' ? 'border-burgundy-700 text-burgundy-700' : 'border-transparent text-gray-500',
                                    ]"
                                    @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'incentive' }"
                                >Incentive Application</button>
                                <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                    :class="[
                                        $store.tabNav.tab === 'recommendation' ? 'border-burgundy-700 text-burgundy-700' : (!$store.tabNav.tabCompletion.incentive ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                    ]"
                                    @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'recommendation' }"
                                    :disabled="false"}
                                >Recommendation</button>
                                <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                    :class="[
                                        $store.tabNav.tab === 'upload' ? 'border-burgundy-700 text-burgundy-700' : (!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation) ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                    ]"
                                    @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'upload' }"
                                    :disabled="!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation)"
                                >Upload Documents</button>
                                <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                    :class="[
                                        $store.tabNav.tab === 'review' ? 'border-burgundy-700 text-burgundy-700' : (!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.upload) ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                    ]"
                                    @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'review' }"
                                    :disabled="!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.upload)"
                                >Review & Submit</button>
                            </div>

                            <div class="flex-1 overflow-y-auto">
                                <!-- Incentive Application Tab -->
                                <div x-show="$store.tabNav && $store.tabNav.tab === 'incentive'" class="space-y-4">
                                    @include('citations.incentive-application')
                                </div>

                                <!-- Recommendation Letter Tab -->
                                <div x-show="$store.tabNav && $store.tabNav.tab === 'recommendation'" class="space-y-4">
                                    @include('citations.recommendation-letter')
                                </div>

                                <!-- File Upload Tab -->
                                <div x-show="$store.tabNav && $store.tabNav.tab === 'upload'" class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <h3 class="font-semibold text-burgundy-800 mb-3">Required Documents</h3>
                                        <p class="text-sm text-gray-600 mb-4">Click on any card to upload the required PDF document.</p>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                            <!-- Recommendation Letter Card -->
                                            <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                                 x-data="{ fileName: '', displayName: '' }"
                                                 @click="$refs.recommendationLetter.click()">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="font-medium text-gray-800 text-sm">Recommendation Letter</h4>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-3 text-center">Recommendation Letter approved by the College Dean</p>
                                                <div class="text-xs text-burgundy-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                     :title="fileName"
                                                     x-text="displayName || 'Click to upload'"></div>
                                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                                <input type="file" name="recommendation_letter" accept=".pdf" class="hidden" x-ref="recommendationLetter" required
                                                    @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                            </div>

                                            <!-- Citing Article Card -->
                                            <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                                 x-data="{ fileName: '', displayName: '' }"
                                                 @click="$refs.citingArticle.click()">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="font-medium text-gray-800 text-sm">Citing Article</h4>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-3 text-center">Copy of the citing article (PDF copy)</p>
                                                <div class="text-xs text-burgundy-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                     :title="fileName"
                                                     x-text="displayName || 'Click to upload'"></div>
                                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                                <input type="file" name="citing_article" accept=".pdf" class="hidden" x-ref="citingArticle" required
                                                    @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                            </div>

                                            <!-- Cited Article Card -->
                                            <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                                 x-data="{ fileName: '', displayName: '' }"
                                                 @click="$refs.citedArticle.click()">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h4 class="font-medium text-gray-800 text-sm">Cited Article</h4>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-3 text-center">Copy of the cited article (PDF copy)</p>
                                                <div class="text-xs text-burgundy-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                     :title="fileName"
                                                     x-text="displayName || 'Click to upload'"></div>
                                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                                <input type="file" name="cited_article" accept=".pdf" class="hidden" x-ref="citedArticle" required
                                                    @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review & Submit Tab -->
                                <div x-show="$store.tabNav && $store.tabNav.tab === 'review'" class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <h3 class="font-semibold text-burgundy-800 mb-3">Review Your Submission</h3>
                                        <p class="text-sm text-gray-600 mb-4">Review all uploaded files and generated documents.</p>
                                        
                                        <!-- Uploaded Files Section -->
                                        <div class="mb-6">
                                            <h4 class="font-medium text-burgundy-700 mb-3">Uploaded Documents</h4>
                                            <div class="grid grid-cols-5 gap-4">
                                                <!-- Recommendation Letter Review Card -->
                                                <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm flex flex-col">
                                                    <div class="text-center mb-3">
                                                        <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                            <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h5 class="font-medium text-gray-800 text-sm">Recommendation Letter</h5>
                                                    </div>
                                                    <div class="text-xs text-gray-600 text-center mb-2 flex-1" id="review-recommendation-letter">No file uploaded</div>
                                                    <div class="text-center mt-auto">
                                                        <button type="button" class="text-xs text-burgundy-600 hover:text-burgundy-800" onclick="document.getElementById('recommendation-letter-review').click()">Change File</button>
                                                        <input type="file" id="recommendation-letter-review" class="hidden" accept=".pdf" onchange="updateReviewFile('recommendation-letter', this)">
                                                    </div>
                                                </div>

                                                <!-- Citing Article Review Card -->
                                                <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm flex flex-col">
                                                    <div class="text-center mb-3">
                                                        <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                            <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h5 class="font-medium text-gray-800 text-sm">Citing Article</h5>
                                                    </div>
                                                    <div class="text-xs text-gray-600 text-center mb-2 flex-1" id="review-citing-article">No file uploaded</div>
                                                    <div class="text-center mt-auto">
                                                        <button type="button" class="text-xs text-burgundy-600 hover:text-burgundy-800" onclick="document.getElementById('citing-article-review').click()">Change File</button>
                                                        <input type="file" id="citing-article-review" class="hidden" accept=".pdf" onchange="updateReviewFile('citing-article', this)">
                                                    </div>
                                                </div>

                                                <!-- Cited Article Review Card -->
                                                <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm flex flex-col">
                                                    <div class="text-center mb-3">
                                                        <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                            <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h5 class="font-medium text-gray-800 text-sm">Cited Article</h5>
                                                    </div>
                                                    <div class="text-xs text-gray-600 text-center mb-2 flex-1" id="review-cited-article">No file uploaded</div>
                                                    <div class="text-center mt-auto">
                                                        <button type="button" class="text-xs text-burgundy-600 hover:text-burgundy-800" onclick="document.getElementById('cited-article-review').click()">Change File</button>
                                                        <input type="file" id="cited-article-review" class="hidden" accept=".pdf" onchange="updateReviewFile('cited-article', this)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Generated Documents Section -->
                                        <div class="mb-6">
                                            <h4 class="font-medium text-burgundy-700 mb-3">Generated Documents</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Incentive Application Review -->
                                                <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm">
                                                    <div class="text-center mb-3">
                                                        <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                            <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h5 class="font-medium text-gray-800 text-sm">Incentive Application</h5>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs bg-burgundy-600 text-white px-3 py-1 rounded hover:bg-burgundy-700 transition-colors" onclick="generateDocx('incentive')">Generate DOCX</button>
                                                    </div>
                                                </div>

                                                <!-- Recommendation Letter Review -->
                                                <div class="bg-burgundy-50 p-4 rounded-lg border border-burgundy-300 shadow-sm">
                                                    <div class="text-center mb-3">
                                                        <div class="w-12 h-12 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                            <svg class="w-6 h-6 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h5 class="font-medium text-gray-800 text-sm">Recommendation Letter</h5>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs bg-burgundy-600 text-white px-3 py-1 rounded hover:bg-burgundy-700 transition-colors" onclick="generateDocx('recommendation')">Generate DOCX</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Loading Overlay - Removed: Now handled by LoadingManager -->

    <!-- Success/Error Notifications -->
    <div x-data="{ 
        showNotification: false, 
        notificationType: 'success', 
        notificationMessage: '',
        init() {
            // Check for Laravel session messages
            @if(session('success'))
                this.showNotification = true;
                this.notificationType = 'success';
                this.notificationMessage = '{{ session('success') }}';
                setTimeout(() => this.showNotification = false, 5000);
            @endif
            @if(session('error'))
                this.showNotification = true;
                this.notificationType = 'error';
                this.notificationMessage = '{{ session('error') }}';
                setTimeout(() => this.showNotification = false, 5000);
            @endif
        }
    }" 
    x-show="showNotification" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-full"
    class="fixed top-20 right-4 z-[60]">
        <div :class="notificationType === 'success' ? 'bg-green-600' : 'bg-red-600'" class="text-white px-4 py-2 rounded shadow-lg backdrop-blur border" :class="notificationType === 'success' ? 'border-green-500/20' : 'border-red-500/20'">
            <div class="flex items-center">
                <svg x-show="notificationType === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg x-show="notificationType === 'error'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span x-text="notificationMessage"></span>
                <button @click="showNotification = false" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L10 11.414l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Progress Bar - Alpine.js Sticky/Docked -->
<div
    x-data="stickyProgressBar"
    x-init="init()"
    :class="isDocked ? 'absolute' : 'fixed'"
    :style="`left: 0; right: 0; bottom: 10px; transition: bottom 0.3s cubic-bezier(.4,0,.2,1);`"
    class="z-50 flex justify-center"
>
    <div class="max-w-xl mx-auto">
        <div class="bg-burgundy-800 shadow-2xl rounded-lg border border-burgundy-700 shadow-black/20 shadow-lg">
            <div class="px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex-1 mr-4">
                        <div class="w-full bg-burgundy-600 rounded-full h-2">
                            <div class="bg-white h-2 rounded-full transition-all duration-300" :style="`width: ${$store.tabNav.progressPercentage || 33}%`"></div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm">
                        <span :class="$store.tabNav.currentStep === 1 ? 'font-semibold text-white' : 'text-burgundy-200'">Step 1: Details</span>
                        <span :class="$store.tabNav.currentStep === 2 ? 'font-semibold text-white' : 'text-burgundy-200'">Step 2: Upload</span>
                        <span :class="$store.tabNav.currentStep === 3 ? 'font-semibold text-white' : 'text-burgundy-200'">Step 3: Review</span>
                        <div x-show="$store.tabNav && $store.tabNav.tab !== 'review'">
                            <button
                                type="button"
                                @click="$store.tabNav.nextTab()"
                                :disabled="false"
                                :class="!$store.tabNav.currentTabComplete
                                    ? 'font-semibold px-4 py-2 rounded-lg bg-burgundy-800 text-burgundy-200 opacity-90 cursor-not-allowed transition shadow-lg'
                                    : 'font-semibold px-4 py-2 rounded-lg bg-burgundy-800 text-white shadow-lg hover:bg-burgundy-900 hover:shadow-xl cursor-pointer transition'"
                            >
                                Next
                            </button>
                        </div>
                        <div x-show="$store.tabNav && $store.tabNav.tab === 'review'">
                                                            <button
                                    type="button"
                                    onclick="submitCitationForm(event)"
                                    :class="!$store.tabNav.allComplete
                                        ? 'font-semibold px-4 py-2 rounded-lg bg-burgundy-800 text-burgundy-50 opacity-90 cursor-not-allowed transition shadow-lg'
                                        : 'font-semibold px-4 py-2 rounded-lg bg-burgundy-800 text-white shadow-lg hover:bg-burgundy-900 hover:shadow-xl cursor-pointer transition'"
                                >
                                Submit Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DOCX Spinner - Removed: Now handled by LoadingManager -->

<script>
    function citationForm() {
        return {
            isFormComplete: false,
            
            checkFilled() {
                // No-op, logic moved to tabNav
            }
        }
    }
    
    function submitCitationForm(event) {
        // Prevent double submission
        if (window.loadingManager && window.loadingManager.isLoading()) {
            return false;
        }
        
        // Validate all tabs before submission
        const tabNav = Alpine.store('tabNav');
        if (!tabNav.allComplete) {
            tabNav.checkTabs();
            if (!tabNav.allComplete) {
                // Show error via global notifications
                if (window.notificationManager) {
                    window.notificationManager.error('Please complete all required fields before submitting.');
                }
                return false;
            }
        }
        
        // Show comprehensive loading state
        const operationId = `submit-citation-${Date.now()}`;
        if (window.loadingManager) {
            window.loadingManager.show(operationId, {
                title: 'Submitting Request',
                message: 'Please wait while we process your citation request...',
                showOverlay: true,
                disableButtons: true
            });
        } else {
            // Fallback: Basic button disabling
            const submitBtn = event && event.target ? event.target : null;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Submit the form after a microtask so the overlay paints first
        const form = document.getElementById('citation-request-form');
        if (form) {
            if (window.queueMicrotask) {
                queueMicrotask(() => form.submit());
            } else {
                Promise.resolve().then(() => form.submit());
            }
        }
        
        return false; // Prevent default form submission
    }
    
    function updateReviewFile(type, input) {
        const fileName = input.files.length > 0 ? input.files[0].name : 'No file uploaded';
        document.getElementById(`review-${type}`).textContent = fileName;
        
        // Update the original file input
        const originalInput = document.querySelector(`[name="${type.replace('-', '_')}"]`);
        if (originalInput && input.files.length > 0) {
            originalInput.files = input.files;
        }
    }
    
    function generateDocx(type) {
        // Prevent double submission
        if (window.loadingManager && window.loadingManager.isLoading()) {
            return;
        }
        
        const form = document.getElementById('citation-request-form');
        const formData = new FormData(form);
        formData.append('docx_type', type);

        // Check if this is a preview (before submission) or post-submission
        // If the form has a request_id hidden field with a value, it's post-submission
        const requestIdField = document.querySelector('input[name="request_id"]');
        const isPreview = !requestIdField || !requestIdField.value;
        
        if (!isPreview) {
            // Post-submission: include request_id
            formData.append('request_id', requestIdField.value);
        }
        // Preview mode: don't include request_id (will use temp directory)

        // Show comprehensive loading state
        const operationId = `generate-citation-docx-${type}-${Date.now()}`;
        if (window.loadingManager) {
            window.loadingManager.show(operationId, {
                title: 'Generating Document',
                message: `Creating ${type} document, please wait...`,
                showOverlay: true,
                disableButtons: true
            });
        } else {
            // Fallback: Basic button disabling
            const button = event.target;
            if (button) {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Get user name for filename
        const applicantName = document.querySelector('[name="applicant_name"]')?.value || 'User';
        const sanitizedName = applicantName.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '_');

        fetch('{{ route("citations.generate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Network response was not ok');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const timestamp = new Date().toISOString().slice(0, 10);
            a.download = type === 'incentive' 
                ? `${sanitizedName}_Citation_Incentive_Application_${timestamp}.docx` 
                : `${sanitizedName}_Citation_Recommendation_Letter_${timestamp}.docx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            alert('Error generating document. Please try again.');
            // Hide loading state
            if (window.loadingManager) {
                window.loadingManager.hide(operationId);
            }
        })
        .finally(() => {
            // Hide loading state
            if (window.loadingManager) {
                window.loadingManager.hide(operationId);
            }
        });
    }

    function syncUploadedFiles() {
        const fileFields = [
            'recommendation_letter',
            'citing_article', 
            'cited_article'
        ];
        
        fileFields.forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            const reviewElement = document.getElementById(`review-${field.replace(/_/g, '-')}`);
            
            if (input && input.files.length > 0 && reviewElement) {
                const fileName = input.files[0].name;
                reviewElement.textContent = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
            }
        });
    }
    
    function tabNav() {
        return {
            tab: 'incentive',
            tabCompletion: { 
                incentive: false, 
                recommendation: false, 
                upload: false, 
                review: false 
            },
            allComplete: false,
            get currentTabComplete() {
                return this.tabCompletion[this.tab];
            },
            get allFormsComplete() {
                return this.tabCompletion.incentive && this.tabCompletion.recommendation && this.tabCompletion.upload;
            },
            get currentStep() {
                if (['incentive', 'recommendation'].includes(this.tab)) {
                    return 1; // Details step
                } else if (this.tab === 'upload') {
                    return 2; // Upload step
                } else if (this.tab === 'review') {
                    return 3; // Review step
                }
                return 1;
            },
            get progressPercentage() {
                const steps = ['incentive', 'recommendation', 'upload', 'review'];
                const currentIndex = steps.indexOf(this.tab);
                return Math.max(33, (currentIndex + 1) * 20);
            },
            nextTab() {
                const tabs = ['incentive', 'recommendation', 'upload', 'review'];
                const currentIndex = tabs.indexOf(this.tab);
                if (!this.currentTabComplete) {
                    this.highlightIncompleteFieldsForTab(this.tab);
                    return;
                }
                if (currentIndex < tabs.length - 1) {
                    this.tab = tabs[currentIndex + 1];
                    // Sync uploaded files when moving to review tab
                    if (this.tab === 'review') {
                        setTimeout(syncUploadedFiles, 100);
                    }
                }
            },
            highlightIncompleteFieldsForTab(tab) {
                let fields = [];
                if (tab === 'incentive') {
                    fields = [
                        'name', 'rank', 'college', 'bibentry', 'issn', 'faculty_name', 'dean_name'
                    ]; // 'citescore' and 'doi' are optional
                } else if (tab === 'recommendation') {
                    fields = ['rec_collegeheader', 'rec_faculty_name', 'rec_citing_details', 'rec_indexing_details', 'rec_dean_name'];
                } else if (tab === 'upload') {
                    fields = ['recommendation_letter', 'citing_article', 'cited_article'];
                }
                let firstIncompleteEl = null;
                fields.forEach(field => {
                    const element = this.getFieldElement(field);
                    if (!element) return;
                    let incomplete = false;
                    if (element.type === 'radio') {
                        incomplete = !document.querySelector(`[name="${field}"]:checked`);
                    } else if (element.type === 'checkbox') {
                        const checked = this.getFieldElements(field);
                        incomplete = Array.from(checked).filter(cb => cb.checked).length === 0;
                    } else if (element.type === 'file') {
                        incomplete = !(element.files && element.files.length > 0);
                    } else {
                        incomplete = element.value.trim() === '';
                    }
                    if (incomplete) {
                        element.classList.add('ring-2', 'ring-burgundy-500', 'ring-offset-2');
                        if (!firstIncompleteEl) firstIncompleteEl = element;
                        setTimeout(() => element.classList.remove('ring-2', 'ring-burgundy-500', 'ring-offset-2'), 2000);
                    }
                });
                // Focus and scroll to first incomplete field
            if (firstIncompleteEl) {
                setTimeout(() => {
                    firstIncompleteEl.focus();
                    firstIncompleteEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
            },
            getFieldElement(field) {
                let el = document.querySelector(`[name="${field}"]`);
                if (el) return el;
                el = document.querySelector(`[name="${field}[]"]`);
                return el;
            },
            getFieldElements(field) {
                let els = document.querySelectorAll(`[name="${field}"]`);
                if (els.length > 0) return els;
                els = document.querySelectorAll(`[name="${field}[]"]`);
                return els;
            },
            checkTabs() {
                // Check incentive tab completion
                const incentiveFields = [
                    'name', 'rank', 'college', 'bibentry', 'issn', 'faculty_name', 'dean_name'
                ]; // 'citescore' and 'doi' are optional
                this.tabCompletion.incentive = incentiveFields.every(field => {
                    const element = document.querySelector(`[name="${field}"]`);
                    return element && (element.type === 'checkbox' ? element.checked : (element.type === 'file' ? (element.files && element.files.length > 0) : element.value.trim() !== ''));
                });
                
                // Check recommendation tab completion
                const recommendationFields = [
                    'rec_collegeheader', 'rec_faculty_name', 'rec_citing_details', 'rec_indexing_details', 'rec_dean_name'
                ];
                this.tabCompletion.recommendation = recommendationFields.every(field => {
                    const element = document.querySelector(`[name="${field}"]`);
                    if (!element) {
                        return false;
                    }
                    const isFilled = element.value.trim() !== '';
                    return isFilled;
                });
                
                // Check upload tab completion
                const uploadFields = [
                    'recommendation_letter', 'citing_article', 'cited_article'
                ];
                this.tabCompletion.upload = uploadFields.every(fileName => {
                    const element = document.querySelector(`[name="${fileName}"]`);
                    return element && element.files.length > 0;
                });
                
                // Review tab is complete if upload is complete
                this.tabCompletion.review = this.tabCompletion.upload;
                
                // All complete if all tabs are complete
                this.allComplete = this.tabCompletion.incentive && this.tabCompletion.recommendation && this.tabCompletion.upload;
            },
            validateCurrentTab() {
                this.checkTabs();
                if (!this.currentTabComplete) {
                    this.highlightIncompleteFieldsForTab(this.tab);
                    if (typeof this.showError !== 'undefined') {
                        this.showError = true;
                        this.errorMsg = 'Please complete all required fields in this section before continuing.';
                        this.$nextTick(() => {
                            this.$refs.errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    }
                    return false;
                }
                return true;
            }
        }
    }

    function stickyProgressBar() {
        return {
            isDocked: false,
            observer: null,
            init() {
                const footer = document.getElementById('main-footer');
                if (!footer) return;
                this.observer = new IntersectionObserver(
                    ([entry]) => {
                        this.isDocked = entry.isIntersecting;
                    },
                    {
                        root: null,
                        threshold: 0.01
                    }
                );
                this.observer.observe(footer);
            },
            destroy() {
                if (this.observer) this.observer.disconnect();
            }
        }
    }

    function validateFileSize(input) {
        if (input.files && input.files[0] && input.files[0].size > 20 * 1024 * 1024) {
            alert('File is too large! Maximum allowed size is 20MB.');
            input.value = '';
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.store('tabNav', tabNav());
        Alpine.store('stickyProgressBar', stickyProgressBar());

        // Add event listeners for all form fields to update tab completion
        const events = ['input', 'change', 'click', 'keyup', 'blur'];
        events.forEach(eventType => {
            document.addEventListener(eventType, (e) => {
                if (e.target.matches('input, textarea, select')) {
                    if (Alpine.store('tabNav') && typeof Alpine.store('tabNav').checkTabs === 'function') {
                        Alpine.store('tabNav').checkTabs();
                    }
                }
            });
        });
        // Periodic check as fallback
        setInterval(() => {
            if (Alpine.store('tabNav') && typeof Alpine.store('tabNav').checkTabs === 'function') {
                Alpine.store('tabNav').checkTabs();
            }
        }, 1000);

        // Add event listeners for file input validation
        document.querySelectorAll('input[type="file"]').forEach(function(input) {
            input.addEventListener('change', function() { validateFileSize(this); });
        });
    });
</script>
</x-app-layout> 