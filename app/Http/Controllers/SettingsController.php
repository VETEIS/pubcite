<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

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
        // Save simple scalar settings
        Setting::set('official_deputy_director_name', $validated['official_deputy_director_name']);
        Setting::set('official_deputy_director_title', $validated['official_deputy_director_title']);
        Setting::set('official_rdd_director_name', $validated['official_rdd_director_name']);
        Setting::set('official_rdd_director_title', $validated['official_rdd_director_title']);
        Setting::set('citations_request_enabled', $validated['citations_request_enabled']);

        // Normalize and save calendar marks as JSON
        $marks = [];
        if (!empty($validated['calendar_marks']) && is_array($validated['calendar_marks'])) {
            foreach ($validated['calendar_marks'] as $row) {
                $date = $row['date'] ?? null;
                $note = $row['note'] ?? null;
                if (!$date && !$note) {
                    continue; // skip empty rows
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

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }
    }
} 