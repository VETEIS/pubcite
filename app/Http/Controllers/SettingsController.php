<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $data = [
            'official_deputy_director_name' => Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD'),
            'official_deputy_director_title' => Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit'),
            'official_rdd_director_name' => Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD'),
            'official_rdd_director_title' => Setting::get('official_rdd_director_title', 'Director, Research and Development Division'),
            'deputy_director_email' => Setting::get('deputy_director_email', ''),
            'rdd_director_email' => Setting::get('rdd_director_email', ''),
            'citations_request_enabled' => Setting::get('citations_request_enabled', '1'),
            'calendar_marks' => json_decode(Setting::get('calendar_marks', '[]'), true) ?? [],
            'announcements' => json_decode(Setting::get('landing_page_announcements', '[]'), true) ?? [],
        ];
        return view('admin.settings', $data);
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();
        
        // Check which save button was clicked
        if ($request->has('save_official_info')) {
            return $this->updateOfficialInfo($request);
        } elseif ($request->has('save_application_controls')) {
            return $this->updateApplicationControls($request);
        } elseif ($request->has('save_calendar')) {
            return $this->updateCalendar($request);
        } elseif ($request->has('save_announcements')) {
            return $this->updateAnnouncements($request);
        }
        
        return back()->with('error', 'Invalid form submission.');
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
        
        return back()->with('success', 'Official information updated.');
    }
    
    private function updateApplicationControls(Request $request)
    {
        $validated = $request->validate([
            'citations_request_enabled' => 'required|in:0,1',
        ]);
        
        Setting::set('citations_request_enabled', $validated['citations_request_enabled']);
        
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
        
        return back()->with('success', 'Calendar settings updated.');
    }
    
    private function updateAnnouncements(Request $request)
    {
        $validated = $request->validate([
            'announcements' => 'nullable|array',
            'announcements.*.title' => 'nullable|string|max:255',
            'announcements.*.description' => 'nullable|string|max:1000',
        ]);
        
        $announcements = [];
        if (!empty($validated['announcements']) && is_array($validated['announcements'])) {
            foreach ($validated['announcements'] as $row) {
                $title = trim($row['title'] ?? '');
                $description = trim($row['description'] ?? '');
                if (!$title && !$description) {
                    continue;
                }
                $announcements[] = [
                    'title' => $title,
                    'description' => $description,
                    'created_at' => now()->toISOString(),
                ];
            }
        }
        
        Setting::set('landing_page_announcements', json_encode($announcements));
        
        return back()->with('success', 'Announcements updated.');
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

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }
    }
} 