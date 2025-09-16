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
        ];
        return view('admin.settings', $data);
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'official_deputy_director_name' => 'required|string|max:255',
            'official_deputy_director_title' => 'required|string|max:255',
            'official_rdd_director_name' => 'required|string|max:255',
            'official_rdd_director_title' => 'required|string|max:255',
            'citations_request_enabled' => 'required|in:0,1',
            'calendar_marks' => 'nullable|array',
            'calendar_marks.*.date' => 'nullable|date',
            'calendar_marks.*.note' => 'nullable|string|max:500',
        ]);
        Setting::set('official_deputy_director_name', $validated['official_deputy_director_name']);
        Setting::set('official_deputy_director_title', $validated['official_deputy_director_title']);
        Setting::set('official_rdd_director_name', $validated['official_rdd_director_name']);
        Setting::set('official_rdd_director_title', $validated['official_rdd_director_title']);
        Setting::set('citations_request_enabled', $validated['citations_request_enabled']);

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
        return back()->with('success', 'Settings updated.');
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