<x-app-layout>
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4 pb-20">
    <div class="w-full max-w-4xl mx-auto">
        <!-- Main Form Card - Fixed Height for Consistency -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 relative" style="min-height: 600px;">
            <div class="flex flex-col items-center text-center mb-4">
                <x-application-logo class="h-10 w-10 mb-2" />
                <h2 class="text-xl font-bold text-maroon-800 mb-1">Publication Request</h2>
                <p class="text-sm text-gray-600">Fill out all required forms and upload documents to submit your publication request</p>
            </div>

            <div x-data="{ showError: false, errorMsg: '' }" x-ref="errorBanner" class="relative">
                <template x-if="showError">
                    <div class="absolute top-0 left-0 w-full z-20">
                        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between shadow mb-4 animate-fade-in">
                            <span x-text="errorMsg"></span>
                            <button @click="showError = false" class="ml-4 text-red-600 hover:text-red-800 font-bold text-lg">&times;</button>
                        </div>
                    </div>
                </template>
                <form 
                    id="publication-request-form"
                    method="POST" 
                    action="{{ route('publications.submit') }}" 
                    enctype="multipart/form-data" 
                    class="space-y-3 h-full"
                    x-data="publicationForm()"
                    @input="checkFilled()"
                    @change="checkFilled()"
                    x-init="checkFilled()"
                    @submit.prevent="if (validateAllTabs()) { $el.submit(); }"
                    autocomplete="on"
                >
                    @csrf
                    <div x-data x-init="Alpine.store('tabNav').checkTabs()" class="h-full flex flex-col">
                        <div class="flex w-full border-b mb-3">
                            <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                :class="[
                                    $store.tabNav.tab === 'incentive' ? 'border-maroon-700 text-maroon-700' : 'border-transparent text-gray-500',
                                    !$store.tabNav.tabCompletion.incentive && $store.tabNav.tab !== 'incentive' ? 'border-b-2 border-red-500 bg-red-50' : '',
                                ]"
                                @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'incentive' }"
                            >Incentive Application</button>
                            <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                :class="[
                                    $store.tabNav.tab === 'recommendation' ? 'border-maroon-700 text-maroon-700' : (!$store.tabNav.tabCompletion.incentive ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                    !$store.tabNav.tabCompletion.recommendation && $store.tabNav.tab !== 'recommendation' && $store.tabNav.tabCompletion.incentive ? 'border-b-2 border-red-500 bg-red-50' : '',
                                ]"
                                @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'recommendation' }"
                                :disabled="!$store.tabNav.tabCompletion.incentive"
                            >Recommendation</button>
                            <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                :class="[
                                    $store.tabNav.tab === 'terminal' ? 'border-maroon-700 text-maroon-700' : (!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation) ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                    !$store.tabNav.tabCompletion.terminal && $store.tabNav.tab !== 'terminal' && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.incentive ? 'border-b-2 border-red-500 bg-red-50' : '',
                                ]"
                                @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'terminal' }"
                                :disabled="!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation)"
                            >Terminal Report</button>
                            <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                :class="[
                                    $store.tabNav.tab === 'upload' ? 'border-maroon-700 text-maroon-700' : (!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.terminal) ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                    !$store.tabNav.tabCompletion.upload && $store.tabNav.tab !== 'upload' && $store.tabNav.tabCompletion.terminal && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.incentive ? 'border-b-2 border-red-500 bg-red-50' : '',
                                ]"
                                @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'upload' }"
                                :disabled="!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.terminal)"
                            >Upload Documents</button>
                            <button type="button" class="flex-1 px-3 py-2 text-sm font-semibold focus:outline-none border-b-2 text-center"
                                :class="[
                                    $store.tabNav.tab === 'review' ? 'border-maroon-700 text-maroon-700' : (!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.terminal && $store.tabNav.tabCompletion.upload) ? 'border-transparent text-gray-400 bg-gray-50 cursor-not-allowed' : 'border-transparent text-gray-500'),
                                ]"
                                @click="if ($store.tabNav.validateCurrentTab()) { $store.tabNav.tab = 'review' }"
                                :disabled="!($store.tabNav.tabCompletion.incentive && $store.tabNav.tabCompletion.recommendation && $store.tabNav.tabCompletion.terminal && $store.tabNav.tabCompletion.upload)"
                            >Review & Submit</button>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            <!-- Incentive Application Tab -->
                            <div x-show="$store.tabNav && $store.tabNav.tab === 'incentive'" class="space-y-4">
                                @include('publications.incentive-application')
                            </div>

                            <!-- Recommendation Letter Tab -->
                            <div x-show="$store.tabNav && $store.tabNav.tab === 'recommendation'" class="space-y-4">
                                @include('publications.recommendation-letter')
                            </div>

                            <!-- Terminal Report Tab -->
                            <div x-show="$store.tabNav && $store.tabNav.tab === 'terminal'" class="space-y-4">
                                @include('publications.terminal-report')
                            </div>

                            <!-- File Upload Tab -->
                            <div x-show="$store.tabNav && $store.tabNav.tab === 'upload'" class="space-y-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-semibold text-maroon-800 mb-3">Required Documents</h3>
                                    <p class="text-sm text-gray-600 mb-4">Click on any card to upload the required PDF document.</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                        <!-- Article PDF Card -->
                                        <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                             x-data="{ fileName: '', displayName: '' }"
                                             @click="$refs.articlePdf.click()">
                                            <div class="text-center mb-3">
                                                <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-medium text-gray-800 text-sm">Article PDF</h4>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-3 text-center">Your published research article in PDF format</p>
                                            <div class="text-xs text-maroon-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                 :title="fileName"
                                                 x-text="displayName || 'Click to upload'"></div>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                            <input type="file" name="article_pdf" accept=".pdf" class="hidden" x-ref="articlePdf" required
                                                @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                        </div>

                                        <!-- Cover Letter Card -->
                                        <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                             x-data="{ fileName: '', displayName: '' }"
                                             @click="$refs.coverPdf.click()">
                                            <div class="text-center mb-3">
                                                <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-medium text-gray-800 text-sm">Cover Letter</h4>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-3 text-center">Cover letter from the journal editor</p>
                                            <div class="text-xs text-maroon-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                 :title="fileName"
                                                 x-text="displayName || 'Click to upload'"></div>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                            <input type="file" name="cover_pdf" accept=".pdf" class="hidden" x-ref="coverPdf" required
                                                @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                        </div>

                                        <!-- Acceptance Letter Card -->
                                        <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                             x-data="{ fileName: '', displayName: '' }"
                                             @click="$refs.acceptancePdf.click()">
                                            <div class="text-center mb-3">
                                                <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-medium text-gray-800 text-sm">Acceptance Letter</h4>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-3 text-center">Official acceptance letter from the journal</p>
                                            <div class="text-xs text-maroon-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                 :title="fileName"
                                                 x-text="displayName || 'Click to upload'"></div>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                            <input type="file" name="acceptance_pdf" accept=".pdf" class="hidden" x-ref="acceptancePdf" required
                                                @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                        </div>

                                        <!-- Peer Review Card -->
                                        <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                             x-data="{ fileName: '', displayName: '' }"
                                             @click="$refs.peerReviewPdf.click()">
                                            <div class="text-center mb-3">
                                                <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-medium text-gray-800 text-sm">Peer Review</h4>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-3 text-center">Peer review comments and responses</p>
                                            <div class="text-xs text-maroon-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                 :title="fileName"
                                                 x-text="displayName || 'Click to upload'"></div>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                            <input type="file" name="peer_review_pdf" accept=".pdf" class="hidden" x-ref="peerReviewPdf" required
                                                @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                        </div>

                                        <!-- Terminal Report Card -->
                                        <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer flex flex-col"
                                             x-data="{ fileName: '', displayName: '' }"
                                             @click="$refs.terminalReportPdf.click()">
                                            <div class="text-center mb-3">
                                                <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-medium text-gray-800 text-sm">Terminal Report</h4>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-3 text-center">Final terminal report document</p>
                                            <div class="text-xs text-maroon-600 text-center font-medium mt-auto truncate whitespace-nowrap max-w-full"
                                                 :title="fileName"
                                                 x-text="displayName || 'Click to upload'"></div>
                                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 20MB</p>
                                            <input type="file" name="terminal_report_pdf" accept=".pdf" class="hidden" x-ref="terminalReportPdf" required
                                                @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''; displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Review & Submit Tab -->
                            <div x-show="$store.tabNav && $store.tabNav.tab === 'review'" class="space-y-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-semibold text-maroon-800 mb-3">Review Your Submission</h3>
                                    <p class="text-sm text-gray-600 mb-4">Review all uploaded files and generated documents before submitting.</p>
                                    
                                    <!-- Uploaded Files Section -->
                                    <div class="mb-6">
                                        <h4 class="font-medium text-maroon-700 mb-3">Uploaded Documents</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                            <!-- Article PDF Review Card -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm flex flex-col">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Article PDF</h5>
                                                </div>
                                                <div class="flex-1 flex flex-col justify-end">
                                                    <div class="text-xs text-gray-600 text-center mb-2" id="review-article">No file uploaded</div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800" onclick="document.getElementById('article-pdf-review').click()">Change File</button>
                                                        <input type="file" id="article-pdf-review" class="hidden" accept=".pdf" onchange="updateReviewFile('article-pdf', this)">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Cover Letter Review Card -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm flex flex-col">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Cover Letter</h5>
                                                </div>
                                                <div class="flex-1 flex flex-col justify-end">
                                                    <div class="text-xs text-gray-600 text-center mb-2" id="review-cover">No file uploaded</div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800" onclick="document.getElementById('cover-pdf-review').click()">Change File</button>
                                                        <input type="file" id="cover-pdf-review" class="hidden" accept=".pdf" onchange="updateReviewFile('cover-pdf', this)">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Acceptance Letter Review Card -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm flex flex-col">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Acceptance Letter</h5>
                                                </div>
                                                <div class="flex-1 flex flex-col justify-end">
                                                    <div class="text-xs text-gray-600 text-center mb-2" id="review-acceptance">No file uploaded</div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800" onclick="document.getElementById('acceptance-pdf-review').click()">Change File</button>
                                                        <input type="file" id="acceptance-pdf-review" class="hidden" accept=".pdf" onchange="updateReviewFile('acceptance-pdf', this)">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Peer Review Review Card -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm flex flex-col">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="text-medium text-gray-800 text-sm">Peer Review</h5>
                                                </div>
                                                <div class="flex-1 flex flex-col justify-end">
                                                    <div class="text-xs text-gray-600 text-center mb-2" id="review-peer">No file uploaded</div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800" onclick="document.getElementById('peer-review-pdf-review').click()">Change File</button>
                                                        <input type="file" id="peer-review-pdf-review" class="hidden" accept=".pdf" onchange="updateReviewFile('peer-review-pdf', this)">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Terminal Report Review Card -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm flex flex-col">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Terminal Report</h5>
                                                </div>
                                                <div class="flex-1 flex flex-col justify-end">
                                                    <div class="text-xs text-gray-600 text-center mb-2" id="review-terminal">No file uploaded</div>
                                                    <div class="text-center">
                                                        <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800" onclick="document.getElementById('terminal-report-pdf-review').click()">Change File</button>
                                                        <input type="file" id="terminal-report-pdf-review" class="hidden" accept=".pdf" onchange="updateReviewFile('terminal-report-pdf', this)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Generated Documents Section -->
                                    <div class="border-t pt-4">
                                        <h4 class="font-medium text-maroon-700 mb-3">Generated Documents</h4>
                                        <p class="text-sm text-gray-600 mb-3">Your documents have been automatically generated. Click to preview:</p>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <!-- Incentive Application DOCX -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer" 
                                                 @click="$store.tabNav.previewDocx('incentive')">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Incentive Application</h5>
                                                </div>
                                                <div class="text-xs text-maroon-600 text-center font-medium">Click to preview</div>
                                            </div>

                                            <!-- Recommendation Letter DOCX -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer" 
                                                 @click="$store.tabNav.previewDocx('recommendation')">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Recommendation Letter</h5>
                                                </div>
                                                <div class="text-xs text-maroon-600 text-center font-medium">Click to preview</div>
                                            </div>

                                            <!-- Terminal Report DOCX -->
                                            <div class="bg-maroon-50 p-4 rounded-lg border border-maroon-300 shadow-sm hover:shadow-md transition-shadow cursor-pointer" 
                                                 @click="$store.tabNav.previewDocx('terminal')">
                                                <div class="text-center mb-3">
                                                    <div class="w-12 h-12 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-6 h-6 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="font-medium text-gray-800 text-sm">Terminal Report</h5>
                                                </div>
                                                <div class="text-xs text-maroon-600 text-center font-medium">Click to preview</div>
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

    <!-- DOCX Preview Modal -->
    <div x-data="{ showModal: false, previewUrl: '', documentName: '' }" 
         x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900" x-text="documentName"></h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="w-full h-96 border border-gray-300 rounded">
                        <iframe x-show="previewUrl" :src="previewUrl" class="w-full h-full" frameborder="0"></iframe>
                        <div x-show="!previewUrl" class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2">Loading preview...</p>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 text-xs rounded">
                            <strong>Disclaimer:</strong> For best results, please use <span class="font-semibold">Microsoft Word</span> to review DOCX files. The template and file design are optimized for Word and may not display correctly in other editors or viewers.
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-maroon-600 text-base font-medium text-white hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                    <button @click="downloadDocument()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Download
                    </button>
                </div>
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
            <div class="bg-maroon-800 shadow-2xl rounded-lg border border-maroon-700 shadow-black/20 shadow-lg">
                <div class="px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-4">
                            <div class="w-full bg-maroon-600 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full transition-all duration-300" :style="`width: ${$store.tabNav.progressPercentage}%`"></div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-sm">
                            <span :class="$store.tabNav.currentStep === 1 ? 'font-semibold text-white' : 'text-maroon-200'">Step 1: Details</span>
                            <span :class="$store.tabNav.currentStep === 2 ? 'font-semibold text-white' : 'text-maroon-200'">Step 2: Upload</span>
                            <span :class="$store.tabNav.currentStep === 3 ? 'font-semibold text-white' : 'text-maroon-200'">Step 3: Review</span>
                            <div x-show="$store.tabNav && $store.tabNav.tab !== 'review'">
                                <button
                                    type="button"
                                    @click="$store.tabNav.nextTab()"
                                    :disabled="!$store.tabNav.currentTabComplete"
                                    :class="!$store.tabNav.currentTabComplete
                                        ? 'font-semibold px-4 py-2 rounded-lg bg-maroon-800 text-maroon-200 opacity-90 cursor-not-allowed transition shadow-lg'
                                        : 'font-semibold px-4 py-2 rounded-lg bg-maroon-800 text-white shadow-lg hover:bg-maroon-900 hover:shadow-xl cursor-pointer transition'"
                                >
                                    Next
                                </button>
                            </div>
                            <div x-show="$store.tabNav && $store.tabNav.tab === 'review'">
                                <button
                                    type="submit"
                                    form="publication-request-form"
                                    :disabled="!$store.tabNav.allComplete"
                                    :class="!$store.tabNav.allComplete
                                        ? 'font-semibold px-4 py-2 rounded-lg bg-maroon-800 text-maroon-50 opacity-90 cursor-not-allowed transition shadow-lg'
                                        : 'font-semibold px-4 py-2 rounded-lg bg-maroon-800 text-white shadow-lg hover:bg-maroon-900 hover:shadow-xl cursor-pointer transition'"
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
</div>
<script>
function publicationForm() {
    return {
        checkFilled() {
            // No-op, logic moved to tabNav
        },
        validateAllTabs() {
            const tabNav = Alpine.store('tabNav');
            if (!tabNav) return false;
            tabNav.checkTabs();
            if (!tabNav.allComplete) {
                ['incentive', 'recommendation', 'terminal', 'upload'].forEach(tab => {
                    tabNav.highlightIncompleteFieldsForTab(tab);
                });
                // Show error banner
                this.showError = true;
                this.errorMsg = 'Please complete all required fields before submitting.';
                // Scroll to top to show the error
                this.$nextTick(() => {
                    this.$refs.errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
                return false;
            }
            return true;
        }
    }
}

function updateReviewFile(type, input) {
    const fileName = input.files.length > 0 ? input.files[0].name : 'No file uploaded';
    
    // Apply the same truncation logic as the upload tab
    const displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;
    
    // Map the review field names to the correct review element IDs
    const fieldMapping = {
        'article-pdf': 'review-article',
        'cover-pdf': 'review-cover',
        'acceptance-pdf': 'review-acceptance',
        'peer-review-pdf': 'review-peer',
        'terminal-report-pdf': 'review-terminal'
    };
    
    const reviewElementId = fieldMapping[type];
    if (reviewElementId) {
        const element = document.getElementById(reviewElementId);
        if (element) {
            element.textContent = displayName;
            element.title = fileName; // Show full filename on hover
        }
    }
    
    // Update the original file input
    const originalFieldName = type.replace('-', '_');
    const originalInput = document.querySelector(`[name="${originalFieldName}"]`);
    if (originalInput && input.files.length > 0) {
        originalInput.files = input.files;
    }
}

// Tab navigation and validation logic
function tabNav() {
    return {
        tab: 'incentive',
        tabCompletion: { 
            incentive: false, 
            recommendation: false, 
            terminal: false, 
            upload: false, 
            review: false 
        },
        allComplete: false,
        generatedDocx: [],
        get currentTabComplete() {
            return this.tabCompletion[this.tab];
        },
        get allFormsComplete() {
            return this.tabCompletion.incentive && this.tabCompletion.recommendation && this.tabCompletion.terminal;
        },
        get currentStep() {
            if (['incentive', 'recommendation', 'terminal'].includes(this.tab)) {
                return 1; // Details step
            } else if (this.tab === 'upload') {
                return 2; // Upload step
            } else if (this.tab === 'review') {
                return 3; // Review step
            }
            return 1;
        },
        get progressPercentage() {
            const steps = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
            const currentIndex = steps.indexOf(this.tab);
            return Math.max(33, (currentIndex + 1) * 20);
        },
        nextTab() {
            const tabs = ['incentive', 'recommendation', 'terminal', 'upload', 'review'];
            const currentIndex = tabs.indexOf(this.tab);
            // If current tab is incomplete, highlight incomplete fields and shake button
            if (!this.currentTabComplete) {
                this.highlightIncompleteFieldsForTab(this.tab);
                this.shakeNextButton();
                return;
            }
            // If moving to review tab, validate all tabs
            if (tabs[currentIndex + 1] === 'review') {
                this.checkTabs();
                if (!this.allComplete) {
                    ['incentive', 'recommendation', 'terminal', 'upload'].forEach(tab => {
                        this.highlightIncompleteFieldsForTab(tab);
                    });
                    // Show error banner
                    if (typeof this.showError !== 'undefined') {
                        this.showError = true;
                        this.errorMsg = 'Please complete all required fields before reviewing.';
                        this.$nextTick(() => {
                            this.$refs.errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    }
                    return;
                }
            }
            // Find the next available tab that is complete
            for (let i = currentIndex + 1; i < tabs.length; i++) {
                if (this.tabCompletion[tabs[i]]) {
                    this.tab = tabs[i];
                    return;
                }
            }
            // If no next tab is complete, go to the next tab in sequence
            if (currentIndex < tabs.length - 1) {
                this.tab = tabs[currentIndex + 1];
            }
        },
        // Highlight incomplete fields for a given tab
        highlightIncompleteFieldsForTab(tab) {
            let fields = [];
                            if (tab === 'incentive') {
                    fields = [
                        'name', 'academicrank', 'employmentstatus', 'college', 'campus', 'field', 'years',
                        'papertitle', 'journaltitle', 'version', 'publisher', 'type', 'particulars', 'facultyname', 'centermanager', 'collegedean', 'indexed_in'
                    ];
                            } else if (tab === 'recommendation') {
                    fields = ['rec_date', 'rec_facultyname', 'details', 'indexing', 'dean'];
            } else if (tab === 'terminal') {
                fields = ['title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references', 'appendices'];
            } else if (tab === 'upload') {
                fields = ['article_pdf', 'cover_pdf', 'acceptance_pdf', 'peer_review_pdf', 'terminal_report_pdf'];
            }
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
                    element.classList.add('ring-2', 'ring-maroon-500', 'ring-offset-2');
                    setTimeout(() => element.classList.remove('ring-2', 'ring-maroon-500', 'ring-offset-2'), 2000);
                }
            });
        },
        // Shake the Next button for feedback
        shakeNextButton() {
            const btn = document.querySelector('.progress-bar-next-btn');
            if (!btn) return;
            btn.classList.add('animate-shake');
            setTimeout(() => btn.classList.remove('animate-shake'), 600);
        },
        checkTabs() {
            // Check incentive tab completion
            const incentiveFields = [
                'name', 'academicrank', 'employmentstatus', 'college', 'campus', 'field', 'years',
                'papertitle', 'journaltitle', 'version', 'publisher', 'type', 'particulars', 'facultyname', 'centermanager', 'collegedean', 'indexed_in'
            ];
            
            // Debug: Log field validation
            console.log('=== TAB VALIDATION DEBUG ===');
            console.log('Current tab:', this.tab);
            
            // Wait a bit for DOM to be ready if we're on incentive tab
            if (this.tab === 'incentive') {
                setTimeout(() => {
                    this.performIncentiveValidation(incentiveFields);
                }, 100);
            } else {
                this.performIncentiveValidation(incentiveFields);
            }
        },
        
        // Helper to get element(s) by field name, supporting array-style names
        getFieldElement(field) {
            // Try normal name first
            let el = document.querySelector(`[name="${field}"]`);
            if (el) return el;
            // Try array-style name
            el = document.querySelector(`[name="${field}[]"]`);
            return el;
        },

        // Helper to get all elements by field name (for checkboxes)
        getFieldElements(field) {
            let els = document.querySelectorAll(`[name="${field}"]`);
            if (els.length > 0) return els;
            els = document.querySelectorAll(`[name="${field}[]"]`);
            return els;
        },

        performIncentiveValidation(incentiveFields) {
            this.tabCompletion.incentive = incentiveFields.every(field => {
                const element = this.getFieldElement(field);
                let isValid = false;
                
                if (!element) {
                    console.log(`❌ Field "${field}" not found in DOM`);
                    return false;
                }
                
                if (element.type === 'radio') {
                    const checked = document.querySelector(`[name="${field}"]:checked`);
                    isValid = !!checked;
                    console.log(`📻 Radio "${field}": ${isValid ? '✅' : '❌'} (checked: ${checked ? checked.value : 'none'})`);
                } else if (element.type === 'checkbox') {
                    const checked = this.getFieldElements(field);
                    const checkedCount = Array.from(checked).filter(cb => cb.checked).length;
                    isValid = checkedCount > 0;
                    console.log(`☑️ Checkbox "${field}": ${isValid ? '✅' : '❌'} (checked: ${checkedCount} items)`);
                } else {
                    isValid = element.value.trim() !== '';
                    console.log(`📝 Text "${field}": ${isValid ? '✅' : '❌'} (value: "${element.value.trim()}")`);
                }
                
                return isValid;
            });
            
            console.log(`🎯 Incentive tab complete: ${this.tabCompletion.incentive ? '✅' : '❌'}`);

            // Check recommendation tab completion
            const recommendationFields = [
                'rec_date', 'rec_facultyname', 'details', 'indexing', 'dean'
            ];
            this.tabCompletion.recommendation = recommendationFields.every(field => {
                const element = document.querySelector(`[name="${field}"]`);
                return element && element.value.trim() !== '';
            });

            // Check terminal tab completion
            const terminalFields = [
                'title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references', 'appendices'
            ];
            this.tabCompletion.terminal = terminalFields.every(field => {
                const element = document.querySelector(`[name="${field}"]`);
                return element && element.value.trim() !== '';
            });

            // Check upload tab completion
            const uploadFields = [
                'article_pdf', 'cover_pdf', 'acceptance_pdf', 'peer_review_pdf', 'terminal_report_pdf'
            ];
            this.tabCompletion.upload = uploadFields.every(field => {
                const element = document.querySelector(`[name="${field}"]`);
                return element && element.files && element.files.length > 0;
            });

            // Review tab is complete if upload is complete
            this.tabCompletion.review = this.tabCompletion.upload;

            // All complete if all tabs are complete
            this.allComplete = this.tabCompletion.incentive && this.tabCompletion.recommendation && 
                              this.tabCompletion.terminal && this.tabCompletion.upload;

            // Debug: Log overall completion status
            console.log('📊 Tab completion status:', {
                incentive: this.tabCompletion.incentive,
                recommendation: this.tabCompletion.recommendation,
                terminal: this.tabCompletion.terminal,
                upload: this.tabCompletion.upload,
                review: this.tabCompletion.review,
                allComplete: this.allComplete
            });
            console.log('=== END DEBUG ===');

            // Update review display
            this.updateReviewDisplay();
            
            // Auto-generate DOCX files when entering review tab
            if (this.tab === 'review' && this.allFormsComplete && this.generatedDocx.length === 0) {
                this.autoGenerateDocx();
            }
        },
        autoGenerateDocx() {
            // Generate all three DOCX files automatically
            const types = ['incentive', 'recommendation', 'terminal'];
            types.forEach(type => {
                this.generateDocx(type, true); // true = silent generation
            });
        },
        updateReviewDisplay() {
            // Update review tab with actual data
            const nameEl = document.getElementById('review-name');
            const collegeEl = document.getElementById('review-college');
            const titleEl = document.getElementById('review-title');
            
            if (nameEl) {
                const nameInput = document.querySelector('[name="name"]');
                nameEl.textContent = nameInput ? nameInput.value || '-' : '-';
            }
            
            if (collegeEl) {
                const collegeInput = document.querySelector('[name="college"]');
                collegeEl.textContent = collegeInput ? collegeInput.value || '-' : '-';
            }
            
            if (titleEl) {
                const titleInput = document.querySelector('[name="papertitle"]');
                titleEl.textContent = titleInput ? titleInput.value || '-' : '-';
            }

            // Update file names with correct mapping and truncation
            const fileFields = [
                { field: 'article_pdf', id: 'review-article' },
                { field: 'cover_pdf', id: 'review-cover' },
                { field: 'acceptance_pdf', id: 'review-acceptance' },
                { field: 'peer_review_pdf', id: 'review-peer' },
                { field: 'terminal_report_pdf', id: 'review-terminal' }
            ];
            fileFields.forEach(({ field, id }) => {
                const element = document.getElementById(id);
                const fileInput = document.querySelector(`[name="${field}"]`);
                if (element && fileInput && fileInput.files && fileInput.files.length > 0) {
                    const fileName = fileInput.files[0].name;
                    const displayName = fileName.length > 16 ? fileName.slice(0, 3) + '...' + fileName.slice(-6) : fileName;
                    element.textContent = displayName;
                    element.title = fileName; // Show full filename on hover
                } else if (element) {
                    element.textContent = 'No file uploaded';
                    element.title = '';
                }
            });
        },
        generateDocx(type, silent = false) {
            if (!this.allFormsComplete) return;
            
            // Collect form data
            const formData = new FormData();
            
            // Add all form fields
            const allFields = [
                'name', 'academicrank', 'employmentstatus', 'college', 'campus', 'field', 'years',
                'papertitle', 'coauthors', 'journaltitle', 'version', 'pissn', 'eissn', 'doi', 'publisher', 'type', 'citescore', 'particulars',
                'facultyname', 'centermanager', 'collegedean', 'indexed_in',
                'rec_date', 'rec_facultyname', 'details', 'indexing', 'dean',
                'title', 'author', 'duration', 'abstract', 'introduction', 'methodology', 'rnd', 'car', 'references', 'appendices'
            ];
            
            allFields.forEach(field => {
                const element = this.getFieldElement(field);
                if (element) {
                    if (element.type === 'radio') {
                        const checked = document.querySelector(`[name="${field}"]:checked`);
                        if (checked) formData.append(field, checked.value);
                    } else if (element.type === 'checkbox') {
                        const checked = this.getFieldElements(field);
                        checked.forEach(cb => formData.append(field + '[]', cb.value));
                    } else {
                        formData.append(field, element.value);
                    }
                }
            });
            
            // Add document type
            formData.append('docx_type', type);
            
            // Get user name for filename (same as citations)
            const applicantName = document.querySelector('[name="name"]')?.value || 'User';
            const sanitizedName = applicantName.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '_');
            const timestamp = new Date().toISOString().slice(0, 10);
            
            // Generate DOCX via AJAX
            fetch('/publications/incentive-application/generate', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                // Create filename following citations pattern
                let filename;
                if (type === 'incentive') {
                    filename = `${sanitizedName}_Publication_Incentive_Application_${timestamp}.docx`;
                } else if (type === 'recommendation') {
                    filename = `${sanitizedName}_Publication_Recommendation_Letter_${timestamp}.docx`;
                } else {
                    filename = `${sanitizedName}_Publication_Terminal_Report_${timestamp}.docx`;
                }
                
                return response.blob().then(blob => ({ blob, filename }));
            })
            .then(({ blob, filename }) => {
                // Create download link
                const url = URL.createObjectURL(blob);
                
                // Add to generated documents list
                this.generatedDocx.push({
                    type: type,
                    name: filename,
                    url: url,
                    blob: blob
                });
                
                // Trigger download only if not silent
                if (!silent) {
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            })
            .catch(error => {
                console.error('Error generating DOCX:', error);
                if (!silent) {
                    alert('Error generating document. Please try again.');
                }
            });
        },
        previewDocx(type) {
            // Find the generated document
            const doc = this.generatedDocx.find(d => d.type === type);
            if (!doc) {
                // Generate it first if not available
                this.generateDocx(type);
                return;
            }
            
            // Show modal with preview
            const modal = document.querySelector('[x-data*="showModal"]').__x.$data;
            modal.documentName = doc.name;
            modal.previewUrl = doc.url;
            modal.showModal = true;
            
            // Store current document for download
            modal.currentDocument = doc;
        },
        downloadDocument() {
            const modal = document.querySelector('[x-data*="showModal"]').__x.$data;
            if (modal.currentDocument) {
                const link = document.createElement('a');
                link.href = modal.currentDocument.url;
                link.download = modal.currentDocument.name;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        },
        init() {
            this.checkTabs();
            
            // Enhanced event listeners for better validation
            const events = ['input', 'change', 'click', 'keyup', 'blur'];
            events.forEach(eventType => {
                document.addEventListener(eventType, (e) => {
                    // Only run validation if the event is on a form element
                    if (e.target.matches('input, textarea, select')) {
                        console.log(`🔄 Event triggered: ${eventType} on ${e.target.name}`);
                        this.checkTabs();
                    }
                });
            });
            
            // Also run validation periodically to catch any missed updates
            setInterval(() => {
                if (this.tab === 'incentive') {
                    this.checkTabs();
                }
            }, 1000);
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

function validateFileSize(input) {
    if (input.files && input.files[0] && input.files[0].size > 20 * 1024 * 1024) {
        alert('File is too large! Maximum allowed size is 20MB.');
        input.value = '';
    }
}

document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function() { validateFileSize(this); });
});

document.addEventListener('alpine:init', () => {
    Alpine.data('stickyProgressBar', () => ({
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
    }));
    Alpine.store('tabNav', tabNav());
});
</script>
</x-app-layout> 