<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\ResearcherProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Spatie\ResponseCache\Facades\ResponseCache;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        
        // Check if accounts exist and get their emails
        $deputyDirectorUser = User::where('signatory_type', 'deputy_director')->first();
        $rddDirectorUser = User::where('signatory_type', 'rdd_director')->first();
        
        $data = [
            'official_deputy_director_name' => Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD'),
            'official_deputy_director_title' => Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit'),
            'official_rdd_director_name' => Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD'),
            'official_rdd_director_title' => Setting::get('official_rdd_director_title', 'Director, Research and Development Division'),
            'deputy_director_email' => $deputyDirectorUser ? $deputyDirectorUser->email : Setting::get('deputy_director_email', ''),
            'rdd_director_email' => $rddDirectorUser ? $rddDirectorUser->email : Setting::get('rdd_director_email', ''),
            'deputy_director_account_exists' => $deputyDirectorUser ? true : false,
            'rdd_director_account_exists' => $rddDirectorUser ? true : false,
            'citations_request_enabled' => Setting::get('citations_request_enabled', '1'),
            'calendar_marks' => json_decode(Setting::get('calendar_marks', '[]'), true) ?? [],
            'announcements' => json_decode(Setting::get('landing_page_announcements', '[]'), true) ?? [],
            'researchers' => ResearcherProfile::ordered()->get()->toArray(),
            // Publication counts for welcome hero
            'scopus_publications_count' => Setting::get('scopus_publications_count', '0'),
            'wos_publications_count' => Setting::get('wos_publications_count', '0'),
            'aci_publications_count' => Setting::get('aci_publications_count', '0'),
            'peer_publications_count' => Setting::get('peer_publications_count', '0'),
            // Academic ranks and colleges for dropdowns
            'academic_ranks' => json_decode(Setting::get('academic_ranks', '[]'), true) ?? [],
            'colleges' => json_decode(Setting::get('colleges', '[]'), true) ?? [],
            // Others indexing options for dropdown
            'others_indexing_options' => json_decode(Setting::get('others_indexing_options', '[]'), true) ?? [],
        ];
        return view('admin.settings', $data);
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();
        
        Log::info('[DEBUG] SettingsController::update called', [
            'all_inputs' => $request->all(),
            'has_save_official_info' => $request->has('save_official_info'),
            'has_save_application_controls' => $request->has('save_application_controls'),
            'has_save_calendar' => $request->has('save_calendar'),
            'has_save_announcements' => $request->has('save_announcements'),
            'has_save_researchers' => $request->has('save_researchers'),
            'has_save_publication_counts' => $request->has('save_publication_counts'),
            'has_save_form_dropdowns' => $request->has('save_form_dropdowns'),
        ]);
        
        // Check which save button was clicked
        if ($request->has('save_official_info')) {
            Log::info('[DEBUG] Routing to updateOfficialInfo');
            return $this->updateOfficialInfo($request);
        } elseif ($request->has('save_application_controls')) {
            Log::info('[DEBUG] Routing to updateApplicationControls');
            return $this->updateApplicationControls($request);
        } elseif ($request->has('save_calendar')) {
            Log::info('[DEBUG] Routing to updateCalendar');
            return $this->updateCalendar($request);
        } elseif ($request->has('save_announcements')) {
            Log::info('[DEBUG] Routing to updateAnnouncements');
            Log::info('[DEBUG] Announcements data received:', [
                'announcements' => $request->input('announcements'),
                'calendar_marks' => $request->input('calendar_marks'),
                'scopus_count' => $request->input('scopus_publications_count'),
                'wos_count' => $request->input('wos_publications_count'),
                'aci_count' => $request->input('aci_publications_count'),
                'peer_count' => $request->input('peer_publications_count'),
            ]);
            return $this->updateAnnouncements($request);
        } elseif ($request->has('save_researchers')) {
            Log::info('[DEBUG] Routing to updateResearchers');
            
            // Debug: Check all request data to see what's actually being sent
            $allRequestData = $request->all();
            Log::info('[DEBUG] All request data keys:', array_keys($allRequestData));
            
            // Check for researchers data in various formats
            $rawResearchers = $request->input('researchers', []);
            
            // Also check if researchers data is coming in a different format
            // Sometimes Laravel doesn't parse empty arrays correctly
            $researcherKeys = array_filter(array_keys($allRequestData), function($key) {
                return strpos($key, 'researchers') === 0;
            });
            
            Log::info('[DEBUG] Researchers data received:', [
                'researchers_count' => count($rawResearchers),
                'researchers' => $rawResearchers,
                'all_researcher_keys' => array_keys($rawResearchers),
                'request_all_keys' => array_keys($request->all()),
                'researcher_keys_in_request' => $researcherKeys,
            ]);
            
            // If researchers array is empty but we have researcher keys, try to rebuild it
            if (empty($rawResearchers) && !empty($researcherKeys)) {
                Log::warning('[DEBUG] Researchers array is empty but researcher keys found in request!');
                Log::warning('[DEBUG] This suggests a form serialization issue.');
            }
            // Log each researcher individually
            foreach ($rawResearchers as $idx => $researcher) {
                Log::info("[DEBUG] Researcher at index {$idx}:", [
                    'index' => $idx,
                    'has_name' => !empty($researcher['name'] ?? ''),
                    'has_title' => !empty($researcher['title'] ?? ''),
                    'has_bio' => !empty($researcher['bio'] ?? ''),
                    'has_research_areas' => !empty($researcher['research_areas'] ?? ''),
                    'data' => $researcher,
                ]);
            }
            return $this->updateResearchers($request);
        } elseif ($request->has('save_publication_counts')) {
            Log::info('[DEBUG] Routing to updatePublicationCounts');
            return $this->updatePublicationCounts($request);
        } elseif ($request->has('save_form_dropdowns')) {
            Log::info('[DEBUG] Routing to updateFormDropdowns');
            return $this->updateFormDropdowns($request);
        }
        
        Log::warning('[DEBUG] No matching save button found!');
        return back()->with('error', 'Invalid form submission.');
    }

    private function updatePublicationCounts(Request $request)
    {
        $validated = $request->validate([
            'scopus_publications_count' => 'required|integer|min:0|max:10000000',
            'wos_publications_count' => 'required|integer|min:0|max:10000000',
            'aci_publications_count' => 'required|integer|min:0|max:10000000',
            'peer_publications_count' => 'required|integer|min:0|max:10000000',
        ]);

        Setting::set('scopus_publications_count', (string)$validated['scopus_publications_count']);
        Setting::set('wos_publications_count', (string)$validated['wos_publications_count']);
        Setting::set('aci_publications_count', (string)$validated['aci_publications_count']);
        Setting::set('peer_publications_count', (string)$validated['peer_publications_count']);

        $this->logSettingsUpdate('publication_counts', $validated);

        return back()->with('success', 'Publication counters updated.');
    }
    
    private function updateOfficialInfo(Request $request)
    {
        $validated = $request->validate([
            'official_deputy_director_name' => 'required|string|max:255',
            'official_deputy_director_title' => 'required|string|max:255',
            'official_rdd_director_name' => 'required|string|max:255',
            'official_rdd_director_title' => 'required|string|max:255',
        ]);
        
        Setting::set('official_deputy_director_name', $validated['official_deputy_director_name']);
        Setting::set('official_deputy_director_title', $validated['official_deputy_director_title']);
        Setting::set('official_rdd_director_name', $validated['official_rdd_director_name']);
        Setting::set('official_rdd_director_title', $validated['official_rdd_director_title']);
        
        $this->logSettingsUpdate('official_info', $validated);
        
        return back()->with('success', 'Official information updated.');
    }
    
    private function updateApplicationControls(Request $request)
    {
        $validated = $request->validate([
            'citations_request_enabled' => 'required|in:0,1',
        ]);
        
        Setting::set('citations_request_enabled', $validated['citations_request_enabled']);
        
        $this->logSettingsUpdate('application_controls', $validated);
        
        return back()->with('success', 'Application controls updated.');
    }
    
    private function updateCalendar(Request $request)
    {
        $validated = $request->validate([
            'calendar_marks' => 'nullable|array',
            'calendar_marks.*.date' => 'nullable|date',
            'calendar_marks.*.note' => 'nullable|string|max:500',
        ]);
        
        $marks = [];
        if (!empty($validated['calendar_marks']) && is_array($validated['calendar_marks'])) {
            foreach ($validated['calendar_marks'] as $row) {
                $date = $row['date'] ?? null;
                $note = $row['note'] ?? null;
                if (!$date && !$note) {
                    continue;
                }
                $marks[] = [
                    'date' => $date,
                    'note' => $note,
                ];
            }
        }
        Setting::set('calendar_marks', json_encode($marks));
        
        $this->logSettingsUpdate('calendar', ['marks_count' => count($marks)]);
        
        return back()->with('success', 'Calendar settings updated.');
    }
    
    private function updateAnnouncements(Request $request)
    {
        Log::info('[DEBUG] updateAnnouncements called', [
            'raw_announcements' => $request->input('announcements'),
            'raw_calendar_marks' => $request->input('calendar_marks'),
            'raw_publication_counts' => [
                'scopus' => $request->input('scopus_publications_count'),
                'wos' => $request->input('wos_publications_count'),
                'aci' => $request->input('aci_publications_count'),
                'peer' => $request->input('peer_publications_count'),
            ],
        ]);
        
        $validated = $request->validate([
            'announcements' => 'nullable|array',
            'announcements.*.title' => 'nullable|string|max:255',
            'announcements.*.description' => 'nullable|string|max:1000',
            // Also accept publication counters when saving the Landing Page card
            'scopus_publications_count' => 'nullable|integer|min:0|max:10000000',
            'wos_publications_count' => 'nullable|integer|min:0|max:10000000',
            'aci_publications_count' => 'nullable|integer|min:0|max:10000000',
            'peer_publications_count' => 'nullable|integer|min:0|max:10000000',
            // And accept calendar marks
            'calendar_marks' => 'nullable|array',
            'calendar_marks.*.date' => 'nullable|date',
            'calendar_marks.*.note' => 'nullable|string|max:500',
        ]);

        $announcements = [];
        if (!empty($validated['announcements']) && is_array($validated['announcements'])) {
            // Get existing announcements to preserve created_at timestamps
            $existingAnnouncements = json_decode(Setting::get('landing_page_announcements', '[]'), true) ?? [];
            $existingMap = [];
            foreach ($existingAnnouncements as $existing) {
                $key = ($existing['title'] ?? '') . '|' . ($existing['description'] ?? '');
                $existingMap[$key] = $existing['created_at'] ?? now()->toISOString();
            }

            foreach ($validated['announcements'] as $row) {
                $title = trim($row['title'] ?? '');
                $description = trim($row['description'] ?? '');
                if (!$title && !$description) {
                    continue;
                }

                // Preserve existing created_at or create new one
                $key = $title . '|' . $description;
                $createdAt = $existingMap[$key] ?? now()->toISOString();

                $announcements[] = [
                    'title' => $title,
                    'description' => $description,
                    'created_at' => $createdAt,
                ];
            }
        }

        Log::info('[DEBUG] Processed announcements:', ['count' => count($announcements), 'data' => $announcements]);
        
        Setting::set('landing_page_announcements', json_encode($announcements));

        // Persist publication counters if present
        Log::info('[DEBUG] Processing publication counts', [
            'has_scopus' => array_key_exists('scopus_publications_count', $validated),
            'has_wos' => array_key_exists('wos_publications_count', $validated),
            'has_aci' => array_key_exists('aci_publications_count', $validated),
            'has_peer' => array_key_exists('peer_publications_count', $validated),
        ]);
        if (array_key_exists('scopus_publications_count', $validated)) {
            Setting::set('scopus_publications_count', (string)($validated['scopus_publications_count'] ?? 0));
        }
        if (array_key_exists('wos_publications_count', $validated)) {
            Setting::set('wos_publications_count', (string)($validated['wos_publications_count'] ?? 0));
        }
        if (array_key_exists('aci_publications_count', $validated)) {
            Setting::set('aci_publications_count', (string)($validated['aci_publications_count'] ?? 0));
        }
        if (array_key_exists('peer_publications_count', $validated)) {
            Setting::set('peer_publications_count', (string)($validated['peer_publications_count'] ?? 0));
        }

        // Persist calendar marks if present
        if (array_key_exists('calendar_marks', $validated)) {
            $marks = [];
            if (!empty($validated['calendar_marks']) && is_array($validated['calendar_marks'])) {
                foreach ($validated['calendar_marks'] as $row) {
                    $date = $row['date'] ?? null;
                    $note = $row['note'] ?? null;
                    if (!$date && !$note) {
                        continue;
                    }
                    $marks[] = [
                        'date' => $date,
                        'note' => $note,
                    ];
                }
            }
            Setting::set('calendar_marks', json_encode($marks));
            Log::info('[DEBUG] Calendar marks saved:', ['count' => count($marks), 'data' => $marks]);
        }

        Log::info('[DEBUG] updateAnnouncements completed successfully');
        return back()->with('success', 'Landing Page updated successfully.');
    }

    public function createAccount(Request $request)
    {
        $this->authorizeAdmin();
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|string|max:255',
            'role' => 'required|in:deputy_director,rdd_director'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'signatory',
                'signatory_type' => $request->role, // deputy_director or rdd_director
                'email_verified_at' => now(),
            ]);

            // Store email in settings for future reference
            $emailKey = $request->role . '_email';
            Setting::set($emailKey, $request->email);

            // Log activity
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'request_id' => null,
                    'action' => 'signatory_account_created',
                    'details' => [
                        'account_type' => $request->role,
                        'account_type_label' => $request->role === 'deputy_director' ? 'Deputy Director' : 'RDD Director',
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                    ],
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create activity log for signatory account creation: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        $this->authorizeAdmin();
        
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:deputy_director,rdd_director'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $user = User::where('signatory_type', $request->role)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found'
                ], 404);
            }

            // Store user info before deletion for activity log
            $deletedUserId = $user->id;
            $deletedUserName = $user->name;
            $deletedUserEmail = $user->email;

            // Delete the user account
            $user->delete();

            // Clear email from settings
            $emailKey = $request->role . '_email';
            Setting::set($emailKey, '');

            // Log activity
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'request_id' => null,
                    'action' => 'signatory_account_deleted',
                    'details' => [
                        'account_type' => $request->role,
                        'account_type_label' => $request->role === 'deputy_director' ? 'Deputy Director' : 'RDD Director',
                        'deleted_user_id' => $deletedUserId,
                        'deleted_user_name' => $deletedUserName,
                        'deleted_user_email' => $deletedUserEmail,
                    ],
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create activity log for signatory account deletion: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateResearchers(Request $request)
    {
        Log::info('[DEBUG] updateResearchers called', [
            'raw_researchers' => $request->input('researchers'),
            'researchers_count' => count($request->input('researchers', [])),
        ]);
        
        // Custom validation for researchers with conditional photo validation
        $rules = [
            'researchers' => 'nullable|array',
            'researchers.*.name' => 'nullable|string|max:255',
            'researchers.*.title' => 'nullable|string|max:255',
            'researchers.*.research_areas' => 'nullable|string|max:500',
            'researchers.*.bio' => 'nullable|string|max:1000',
            'researchers.*.status_badge' => 'nullable|string|max:50',
            'researchers.*.background_color' => 'nullable|string|max:50',
            'researchers.*.profile_link' => 'nullable|string|max:255',
            'researchers.*.scopus_link' => 'nullable|string|max:500',
            'researchers.*.orcid_link' => 'nullable|string|max:500',
            'researchers.*.wos_link' => 'nullable|string|max:500',
            'researchers.*.google_scholar_link' => 'nullable|string|max:500',
            'researchers.*.photo_path' => 'nullable|string|max:500',
        ];
        
        // Add photo validation only for indices that have files
        // Increased limit to 10MB (10240 KB) to accommodate larger photos
        if ($request->has('researchers') && is_array($request->input('researchers'))) {
            foreach ($request->input('researchers') as $index => $researcher) {
                if ($request->hasFile("researchers.{$index}.photo")) {
                    $rules["researchers.{$index}.photo"] = 'image|mimes:jpeg,png,jpg,gif,webp|max:10240';
                }
            }
        }
        
        $validated = $request->validate($rules);
        
        // If researchers array is still empty after validation, try to extract from raw request
        // This handles cases where Laravel might not parse empty array fields correctly
        if (empty($validated['researchers']) || !is_array($validated['researchers'])) {
            Log::warning('[DEBUG] Researchers array is empty after validation, attempting to extract from raw request');
            $allData = $request->all();
            $extractedResearchers = [];
            
            // Look for researcher fields in the request
            foreach ($allData as $key => $value) {
                if (preg_match('/^researchers\[(\d+)\]\[(.+)\]$/', $key, $matches)) {
                    $index = (int)$matches[1];
                    $field = $matches[2];
                    if (!isset($extractedResearchers[$index])) {
                        $extractedResearchers[$index] = [];
                    }
                    $extractedResearchers[$index][$field] = $value;
                }
            }
            
            if (!empty($extractedResearchers)) {
                Log::info('[DEBUG] Extracted researchers from raw request:', [
                    'count' => count($extractedResearchers),
                    'indices' => array_keys($extractedResearchers),
                ]);
                $validated['researchers'] = $extractedResearchers;
            }
        }

        $existingResearchers = ResearcherProfile::ordered()->get()->values();
        Log::info('[DEBUG] Existing researchers count:', ['count' => $existingResearchers->count()]);
        
        // Debug: Log all uploaded files to see their structure
        $allFiles = $request->allFiles();
        
        // Helper function to safely extract file names from nested structures
        $extractFileName = function($item) use (&$extractFileName) {
            if ($item instanceof \Illuminate\Http\UploadedFile) {
                return $item->getClientOriginalName();
            } elseif (is_array($item)) {
                return array_map($extractFileName, $item);
            }
            return null;
        };
        
        Log::info('[DEBUG] All uploaded files:', [
            'file_keys' => array_keys($allFiles),
            'files_structure' => array_map($extractFileName, $allFiles),
        ]);
        
        // Build a map of file indices to file objects for easier lookup
        $fileMap = [];
        
        // First, try to extract files directly from $allFiles
        Log::info('[DEBUG] Building file map from allFiles', [
            'has_researchers_key' => isset($allFiles['researchers']),
            'researchers_type' => isset($allFiles['researchers']) ? gettype($allFiles['researchers']) : 'N/A',
            'researchers_is_array' => isset($allFiles['researchers']) && is_array($allFiles['researchers']),
        ]);
        
        if (isset($allFiles['researchers']) && is_array($allFiles['researchers'])) {
            Log::info('[DEBUG] Researchers files structure', [
                'indices' => array_keys($allFiles['researchers']),
                'count' => count($allFiles['researchers']),
            ]);
            
            foreach ($allFiles['researchers'] as $fileIndex => $fileData) {
                Log::info("[DEBUG] Processing file data at index {$fileIndex}", [
                    'file_data_type' => gettype($fileData),
                    'file_data_is_array' => is_array($fileData),
                    'has_photo_key' => is_array($fileData) && isset($fileData['photo']),
                ]);
                
                if (is_array($fileData) && isset($fileData['photo'])) {
                    $photo = $fileData['photo'];
                    Log::info("[DEBUG] Photo object details at index {$fileIndex}", [
                        'photo_type' => gettype($photo),
                        'photo_class' => is_object($photo) ? get_class($photo) : 'N/A',
                        'is_uploaded_file' => $photo instanceof \Illuminate\Http\UploadedFile,
                    ]);
                    
                    // The photo should be an UploadedFile instance
                    if ($photo instanceof \Illuminate\Http\UploadedFile) {
                        // If file is valid, just add it - no extra checks needed
                        if ($photo->isValid()) {
                            $fileMap[$fileIndex] = $photo;
                            Log::info("[DEBUG] ✓ Found valid photo file at index {$fileIndex} from allFiles", [
                                'file_name' => $photo->getClientOriginalName(),
                                'file_size' => $photo->getSize(),
                                'is_valid' => true,
                            ]);
                        } else {
                            // File is invalid - log it but don't try to process it
                            // (Original behavior: just skip invalid files)
                            Log::info("[DEBUG] Photo at index {$fileIndex} is invalid, skipping", [
                                'error' => $photo->getError(),
                                'error_message' => $photo->getErrorMessage(),
                                'file_name' => $photo->getClientOriginalName(),
                            ]);
                        }
                    } else {
                        Log::warning("[DEBUG] Photo at index {$fileIndex} is not an UploadedFile instance", [
                            'type' => gettype($photo),
                            'class' => is_object($photo) ? get_class($photo) : 'N/A',
                        ]);
                    }
                }
            }
        }
        
        // Also try using $request->file() as a fallback for any indices we might have missed
        $researcherIndices = [];
        if (isset($validated['researchers']) && is_array($validated['researchers'])) {
            $researcherIndices = array_keys($validated['researchers']);
        } elseif (isset($allFiles['researchers']) && is_array($allFiles['researchers'])) {
            $researcherIndices = array_keys($allFiles['researchers']);
        }
        
        Log::info('[DEBUG] Trying to find files using request->file() for indices', [
            'indices' => $researcherIndices,
            'file_map_before' => array_keys($fileMap),
        ]);
        
        // For each researcher index, try to get the file using $request->file() if not already in map
        foreach ($researcherIndices as $index) {
            if (isset($fileMap[$index])) {
                Log::info("[DEBUG] File already in map at index {$index}, skipping");
                continue; // Already found
            }
            
            // Try multiple key formats
            $keyFormats = [
                "researchers.{$index}.photo",      // Dot notation
                "researchers[{$index}][photo]",   // Array notation
            ];
            
            foreach ($keyFormats as $photoFileKey) {
                Log::info("[DEBUG] Trying key format: {$photoFileKey}", [
                    'has_file' => $request->hasFile($photoFileKey),
                ]);
                
                if ($request->hasFile($photoFileKey)) {
                    $photo = $request->file($photoFileKey);
                    Log::info("[DEBUG] Got file from request->file()", [
                        'key' => $photoFileKey,
                        'is_uploaded_file' => $photo instanceof \Illuminate\Http\UploadedFile,
                        'is_valid' => $photo instanceof \Illuminate\Http\UploadedFile ? $photo->isValid() : false,
                    ]);
                    
                    if ($photo instanceof \Illuminate\Http\UploadedFile) {
                        // Simple: if file is valid, use it. If not, skip it.
                        if ($photo->isValid()) {
                            $fileMap[$index] = $photo;
                            Log::info("[DEBUG] ✓ Found photo file at index {$index} using key: {$photoFileKey}", [
                                'file_name' => $photo->getClientOriginalName(),
                                'file_size' => $photo->getSize(),
                                'is_valid' => $photo->isValid(),
                            ]);
                            break 2; // Break out of both loops
                        }
                    }
                }
            }
        }
        
        Log::info('[DEBUG] File map built', [
            'file_map_count' => count($fileMap),
            'file_map_indices' => array_keys($fileMap),
        ]);

        $payloads = [];
        $validatedResearchers = $validated['researchers'] ?? [];
        Log::info('[DEBUG] Processing researchers from validated data', [
            'count' => count($validatedResearchers),
            'indices' => array_keys($validatedResearchers),
            'file_map_indices' => array_keys($fileMap),
        ]);
        foreach ($validatedResearchers as $index => $row) {
            Log::info("[DEBUG] Processing researcher index {$index}", [
                'index' => $index,
                'raw_data' => $row,
                'name' => $row['name'] ?? 'MISSING',
                'title' => $row['title'] ?? 'MISSING',
            ]);
            $name = trim($row['name'] ?? '');
            $title = trim($row['title'] ?? '');
            $bio = trim($row['bio'] ?? '');
            $researchAreasInput = $row['research_areas'] ?? '';

            $researchAreas = collect(explode(',', (string) $researchAreasInput))
                ->map(fn ($area) => trim($area))
                ->filter()
                ->values()
                ->all();

            $hasContent = $name !== '' || $title !== '' || $bio !== '' || !empty($researchAreas);
            if (!$hasContent) {
                continue;
            }

            $photoPath = null;
            
            // First, check if we have a file in our file map for this index
            if (isset($fileMap[$index])) {
                $photo = $fileMap[$index];
                // File is already validated when added to map, so we can use it directly
                if ($photo instanceof \Illuminate\Http\UploadedFile) {
                    Log::info("[DEBUG] Photo file found in file map", [
                        'index' => $index,
                        'file_name' => $photo->getClientOriginalName(),
                        'file_size' => $photo->getSize(),
                        'is_valid' => $photo->isValid(),
                        'error_code' => $photo->getError(),
                    ]);
                    $photoPath = $this->storePhotoAsWebp($photo);
                    if ($photoPath) {
                        Log::info("[DEBUG] Photo stored successfully", ['photo_path' => $photoPath]);
                    } else {
                        Log::warning("[DEBUG] Photo storage failed for index {$index}");
                    }
                } else {
                    Log::warning("[DEBUG] Photo file in map is not an UploadedFile at index {$index}", [
                        'type' => gettype($photo),
                    ]);
                    unset($fileMap[$index]);
                }
            }
            
            // If we don't have a photo path yet, try direct file access methods
            if (!$photoPath) {
                $photoFileKey = "researchers.{$index}.photo";
                $photoFileKeyAlt = "researchers[{$index}][photo]";
                
                if ($request->hasFile($photoFileKey)) {
                    $photo = $request->file($photoFileKey);
                    if ($photo instanceof \Illuminate\Http\UploadedFile) {
                        $errorCode = $photo->getError();
                        $hasValidPath = $photo->getRealPath() && file_exists($photo->getRealPath());
                        // Allow processing if valid or if it's just a size limit issue (error code 1)
                        if ($photo->isValid() || ($errorCode === 1 && $hasValidPath)) {
                            Log::info("[DEBUG] Photo file found using dot notation", [
                                'index' => $index,
                                'file_name' => $photo->getClientOriginalName(),
                                'is_valid' => $photo->isValid(),
                            ]);
                            $photoPath = $this->storePhotoAsWebp($photo);
                        }
                    }
                } elseif ($request->hasFile($photoFileKeyAlt)) {
                    $photo = $request->file($photoFileKeyAlt);
                    if ($photo instanceof \Illuminate\Http\UploadedFile) {
                        $errorCode = $photo->getError();
                        $hasValidPath = $photo->getRealPath() && file_exists($photo->getRealPath());
                        // Allow processing if valid or if it's just a size limit issue (error code 1)
                        if ($photo->isValid() || ($errorCode === 1 && $hasValidPath)) {
                            Log::info("[DEBUG] Photo file found using array notation", [
                                'index' => $index,
                                'file_name' => $photo->getClientOriginalName(),
                                'is_valid' => $photo->isValid(),
                            ]);
                            $photoPath = $this->storePhotoAsWebp($photo);
                        }
                    }
                } elseif ($request->has("researchers.{$index}.photo_path") && !empty($request->input("researchers.{$index}.photo_path"))) {
                    // Photo path preserved from hidden input
                    $photoPath = $request->input("researchers.{$index}.photo_path");
                    Log::info("[DEBUG] Using preserved photo_path", ['photo_path' => $photoPath]);
                } else {
                    // Try to find photo by matching name/title with existing researchers
                    // Since we delete all and recreate, we can't match by index, so we match by content
                    $matchingExisting = $existingResearchers->first(function ($existing) use ($name, $title) {
                        return $existing->name === $name && $existing->title === $title;
                    });
                    
                    if ($matchingExisting && $matchingExisting->photo_path) {
                        $photoPath = $matchingExisting->photo_path;
                        Log::info("[DEBUG] Using existing photo_path from matching researcher", [
                            'photo_path' => $photoPath,
                            'matched_name' => $name,
                            'matched_title' => $title,
                        ]);
                    } else {
                        Log::info("[DEBUG] No photo found for researcher", [
                            'index' => $index,
                            'name' => $name,
                            'title' => $title,
                        ]);
                    }
                }
            }

            $payloads[] = [
                'name' => $name,
                'title' => $title,
                'research_areas' => $researchAreas,
                'bio' => $bio,
                'status_badge' => $row['status_badge'] ?? 'Active',
                'background_color' => $row['background_color'] ?? 'maroon',
                'profile_link' => trim($row['profile_link'] ?? '') ?: null,
                'scopus_link' => trim($row['scopus_link'] ?? '') ?: null,
                'orcid_link' => trim($row['orcid_link'] ?? '') ?: null,
                'wos_link' => trim($row['wos_link'] ?? '') ?: null,
                'google_scholar_link' => trim($row['google_scholar_link'] ?? '') ?: null,
                'photo_path' => $photoPath,
                'sort_order' => count($payloads),
                'is_active' => true,
            ];
        }

        Log::info('[DEBUG] Final payloads to save:', ['count' => count($payloads), 'payloads' => $payloads]);
        
        DB::transaction(function () use ($payloads) {
            Log::info('[DEBUG] Starting database transaction');
            ResearcherProfile::query()->delete();
            Log::info('[DEBUG] Deleted existing researchers');
            foreach ($payloads as $index => $data) {
                Log::info("[DEBUG] Creating researcher {$index}", ['data' => $data]);
                ResearcherProfile::create($data);
            }
            Log::info('[DEBUG] All researchers created');
        });

        // Clear response cache so the admin page and public endpoints reflect fresh data
        try {
            ResponseCache::clear();
            Log::info('[DEBUG] Response cache cleared after updating researchers');
        } catch (\Throwable $e) {
            Log::warning('[DEBUG] Failed to clear response cache', ['error' => $e->getMessage()]);
        }

        Log::info('[DEBUG] updateResearchers completed successfully');
        return back()->with('success', 'Researchers updated successfully.');
    }

    /**
     * Safely store an uploaded file, handling cases where MIME type detection fails.
     */
    private function safeStoreFile(UploadedFile $photo, string $directory = 'researcher-photos'): ?string
    {
        try {
            return $photo->store($directory, 'public');
        } catch (\Throwable $storeError) {
            // If store() fails due to MIME type issues, try manual storage
            Log::warning('[DEBUG] store() failed, attempting manual file storage', [
                'error' => $storeError->getMessage(),
                'directory' => $directory,
            ]);
            
                $path = $photo->getRealPath() ?: $photo->getPathname();
                // Make sure it's a file, not a directory
                if ($path && file_exists($path) && is_file($path) && is_readable($path)) {
                    $extension = $photo->getClientOriginalExtension() ?: 'jpg';
                    $filename = $directory . '/' . bin2hex(random_bytes(16)) . '.' . $extension;
                    $fileContents = file_get_contents($path);
                    if ($fileContents !== false) {
                        Storage::disk('public')->put($filename, $fileContents, 'public');
                        return $filename;
                    }
                } else {
                    Log::warning('[DEBUG] Cannot read file for manual storage', [
                        'path' => $path,
                        'exists' => $path ? file_exists($path) : false,
                        'is_file' => $path ? is_file($path) : false,
                        'is_readable' => $path ? is_readable($path) : false,
                        'error' => $photo->getError(),
                        'error_message' => $photo->getErrorMessage(),
                    ]);
                }
                return null;
        }
    }

    /**
     * Store uploaded photo as WebP on the public disk.
     * Falls back to the original file if WebP conversion is unavailable.
     */
    private function storePhotoAsWebp(UploadedFile $photo): ?string
    {
        try {
            // Check if file is valid or if it's just a size issue that we can still process
            $isValid = $photo->isValid();
            $errorCode = $photo->getError();
            $hasValidPath = $photo->getRealPath() && file_exists($photo->getRealPath()) && is_readable($photo->getRealPath());
            
            // Allow processing if file is valid OR if it's just a size limit issue but file exists (error code 1)
            if (!$isValid && !($errorCode === 1 && $hasValidPath)) {
                Log::warning('[DEBUG] Uploaded file cannot be processed', [
                    'error' => $errorCode,
                    'error_message' => $photo->getErrorMessage(),
                    'has_valid_path' => $hasValidPath,
                ]);
                return null;
            }
            
            if (!$isValid) {
                Log::info('[DEBUG] Processing file that exceeds PHP upload limit', [
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                ]);
            }
            
            // Prefer converting to WebP using GD if available
            if (function_exists('imagewebp')) {
                $path = $photo->getRealPath();
                
                // Check if path is valid and file exists
                if (!$path || !file_exists($path) || !is_readable($path)) {
                    Log::warning('[DEBUG] File path is invalid or not readable, attempting manual storage', [
                        'path' => $path,
                        'exists' => $path ? file_exists($path) : false,
                        'readable' => $path ? is_readable($path) : false,
                        'temp_name' => $photo->getPathname(),
                    ]);
                    
                    // Try to get file from temporary location
                    $tempPath = $photo->getPathname();
                    if ($tempPath && file_exists($tempPath) && is_readable($tempPath)) {
                        $path = $tempPath;
                    } else {
                    // Last resort: try to store directly using safe method
                    return $this->safeStoreFile($photo);
                    }
                }
                
                // Try to get MIME type, fallback to extension-based detection
                try {
                    $mime = (string) $photo->getMimeType();
                } catch (\Throwable $e) {
                    // If MIME type detection fails, try to determine from file extension
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $mimeMap = [
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                    ];
                    $mime = $mimeMap[$extension] ?? 'image/jpeg';
                    Log::info('[DEBUG] MIME type detection failed, using extension-based detection', [
                        'extension' => $extension,
                        'detected_mime' => $mime,
                    ]);
                }
                
                $source = null;
                if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                    if (!function_exists('imagecreatefromjpeg')) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefromjpeg($path);
                } elseif ($mime === 'image/png') {
                    if (!function_exists('imagecreatefrompng')) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefrompng($path);
                    if ($source) {
                        imagepalettetotruecolor($source);
                        imagealphablending($source, true);
                        imagesavealpha($source, true);
                    }
                } elseif ($mime === 'image/gif') {
                    if (!function_exists('imagecreatefromgif')) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefromgif($path);
                } else {
                    if (!function_exists('imagecreatefromstring')) {
                        return $this->safeStoreFile($photo);
                    }
                    $bytes = @file_get_contents($path);
                    if ($bytes === false) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefromstring($bytes);
                }

                if (!$source) {
                    return $this->safeStoreFile($photo);
                }

                // Render to WebP in-memory so we can use Storage facade
                ob_start();
                // Quality 85 is a good balance
                @imagewebp($source, null, 85);
                imagedestroy($source);
                $webpData = ob_get_clean();
                if (!$webpData) {
                    return $this->safeStoreFile($photo);
                }

                $filename = 'researcher-photos/' . bin2hex(random_bytes(16)) . '.webp';
                Storage::disk('public')->put($filename, $webpData, 'public');
                Log::info('[DEBUG] Photo converted to WebP', [
                    'original_mime' => $mime,
                    'webp_path' => $filename,
                    'webp_size' => strlen($webpData),
                ]);
                return $filename;
            }

            // Fallback: store the original file
            Log::info('[DEBUG] WebP conversion not available, storing original file', [
                'has_imagewebp' => function_exists('imagewebp'),
            ]);
            
            // Use safe storage method
            return $this->safeStoreFile($photo);
        } catch (\Throwable $e) {
            Log::warning('[DEBUG] WebP conversion failed, attempting to store original', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            // Try to store, but handle MIME type errors gracefully
            try {
                return $photo->store('researcher-photos', 'public');
            } catch (\Throwable $storeError) {
                // If store() fails due to MIME type issues, try manual storage
                Log::warning('[DEBUG] store() failed in catch block, attempting manual file storage', [
                    'error' => $storeError->getMessage(),
                ]);
                
                $path = $photo->getRealPath() ?: $photo->getPathname();
                if ($path && file_exists($path) && is_readable($path)) {
                    $extension = $photo->getClientOriginalExtension() ?: 'jpg';
                    $filename = 'researcher-photos/' . bin2hex(random_bytes(16)) . '.' . $extension;
                    $fileContents = file_get_contents($path);
                    if ($fileContents !== false) {
                        Storage::disk('public')->put($filename, $fileContents, 'public');
                        return $filename;
                    }
                }
                return null;
            }
        }
    }

    private function updateFormDropdowns(Request $request)
    {
        $validated = $request->validate([
            'academic_ranks' => 'nullable|array',
            'academic_ranks.*' => 'nullable|string|max:255',
            'colleges' => 'nullable|array',
            'colleges.*' => 'nullable|string|max:255',
            'others_indexing_options' => 'nullable|array',
            'others_indexing_options.*' => 'nullable|string|max:255',
        ]);

        // Filter out empty values and trim
        $academicRanks = [];
        if (!empty($validated['academic_ranks']) && is_array($validated['academic_ranks'])) {
            foreach ($validated['academic_ranks'] as $rank) {
                $trimmed = trim((string)$rank);
                if ($trimmed !== '') {
                    $academicRanks[] = $trimmed;
                }
            }
        }

        $colleges = [];
        if (!empty($validated['colleges']) && is_array($validated['colleges'])) {
            foreach ($validated['colleges'] as $college) {
                $trimmed = trim((string)$college);
                if ($trimmed !== '') {
                    $colleges[] = $trimmed;
                }
            }
        }

        $othersIndexingOptions = [];
        if (!empty($validated['others_indexing_options']) && is_array($validated['others_indexing_options'])) {
            foreach ($validated['others_indexing_options'] as $option) {
                $trimmed = trim((string)$option);
                if ($trimmed !== '') {
                    $othersIndexingOptions[] = $trimmed;
                }
            }
        }

        Setting::set('academic_ranks', json_encode($academicRanks));
        Setting::set('colleges', json_encode($colleges));
        Setting::set('others_indexing_options', json_encode($othersIndexingOptions));

        $this->logSettingsUpdate('form_dropdowns', [
            'academic_ranks_count' => count($academicRanks),
            'colleges_count' => count($colleges),
            'others_indexing_options_count' => count($othersIndexingOptions),
        ]);

        // Return JSON for AJAX requests, otherwise redirect back
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Form dropdown options updated successfully.']);
        }

        return back()->with('success', 'Form dropdown options updated successfully.');
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }
    }

    private function logSettingsUpdate(string $category, array $details): void
    {
        try {
            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'request_id' => null,
                'action' => 'settings_updated',
                'details' => array_merge([
                    'category' => $category,
                ], $details),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create activity log for settings update: ' . $e->getMessage());
        }
    }
} 