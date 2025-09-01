<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Request as UserRequest;

class SigningController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');

        // Fetch recent pending requests and filter in PHP by expected field keys
        $candidateRequests = UserRequest::orderByDesc('requested_at')->limit(200)->get();
        $needs = [];

        foreach ($candidateRequests as $req) {
            $form = is_array($req->form_data) ? $req->form_data : (json_decode($req->form_data ?? '[]', true) ?: []);
            $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
            if ($matchedRole) {
                $needs[] = [
                    'id' => $req->id,
                    'request_code' => $req->request_code,
                    'type' => $req->type,
                    'status' => $req->status,
                    'matched_role' => $matchedRole,
                    'requested_at' => $req->requested_at,
                ];
            }
        }

        return view('signing.index', [
            'requests' => $needs,
            'signatoryType' => $signatoryType,
        ]);
    }

    public function storeSignature(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }
        $validated = $request->validate([
            'signature' => 'required|image|mimes:png|max:2048',
        ]);
        $file = $validated['signature'];
        $path = 'signatures/' . $user->id . '.png';
        // Ensure directory
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
        return back()->with('success', 'Signature uploaded successfully.');
    }

    private function getSignaturePath(int $userId): string
    {
        return 'signatures/' . $userId . '.png';
    }

    private function matchesSignatory(array $form, ?string $signatoryType, string $userName): ?string
    {
        if ($userName === '') return null;
        $nameLower = mb_strtolower($userName);

        // Map signatory_type to possible keys present in form_data
        $map = [
            'faculty' => ['facultyname', 'faculty_name', 'rec_faculty_name'],
            'center_manager' => ['centermanager', 'center_manager', 'research_center_manager'],
            'college_dean' => ['collegedean', 'college_dean', 'dean', 'dean_name', 'rec_dean_name'],
        ];
        $keys = $map[$signatoryType] ?? [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $form)) continue;
            $val = trim((string) $form[$key]);
            if ($val !== '' && mb_strtolower($val) === $nameLower) {
                return $signatoryType;
            }
        }
        return null;
    }
} 