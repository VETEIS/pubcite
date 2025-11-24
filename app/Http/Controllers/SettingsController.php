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
            // Researchers are now managed via Livewire component
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
            // Researchers are now managed via Livewire component (ResearcherManager)
            // This should not be reached, but kept for backwards compatibility
            Log::warning('[DEBUG] save_researchers request received but researchers are now managed via Livewire');
            return back()->with('info', 'Researchers are now managed via the Livewire component.');
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