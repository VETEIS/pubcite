<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Request as UserRequest;
use App\Models\Setting;
use App\Models\User;
use App\Models\Signature;
use App\Services\DocumentSigningService;
use App\Enums\SignatureStatus;

class SigningController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');

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

        $citations_request_enabled = \App\Models\Setting::get('citations_request_enabled', '1');
        
        foreach ($needs as &$request) {
            $request['signature_status'] = $request['signature_status'] ?? 'pending';
            $request['can_revert'] = false;
            if (isset($request['signed_at'])) {
                $signedAt = \Carbon\Carbon::parse($request['signed_at']);
                $request['can_revert'] = $signedAt->diffInHours(now()) < 24;
            }
        }
        
        return view('signing.index', [
            'requests' => $needs,
            'signatoryType' => $signatoryType,
            'citations_request_enabled' => $citations_request_enabled,
        ]);
    }

    public function storeSignature(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }
        $validated = $request->validate([
            'signature' => 'required|image|mimes:png|max:2048',
        ]);
        $file = $validated['signature'];
        $path = 'signatures/' . $user->id . '.png';
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

    public function signDocument(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'signature_id' => 'required|exists:signatures,id',
        ]);

        $userRequest = UserRequest::findOrFail($validated['request_id']);
        $signature = Signature::findOrFail($validated['signature_id']);

        if ($signature->user_id !== $user->id) {
            abort(403, 'You can only use your own signatures');
        }

        if ($userRequest->isSigned()) {
            return response()->json([
                'success' => false,
                'message' => 'This request is already signed'
            ]);
        }

        $signatoryType = $user->signatoryType();
        $userName = trim($user->name ?? '');
        $form = is_array($userRequest->form_data) ? $userRequest->form_data : (json_decode($userRequest->form_data ?? '[]', true) ?: []);
        $matchedRole = $this->matchesSignatory($form, $signatoryType, $userName);
        
        if (!$matchedRole) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to sign this request'
            ]);
        }

        $signingService = app(DocumentSigningService::class);
        $success = $signingService->signDocument($userRequest, $user, $signature);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Document signed successfully',
                'signature_status' => SignatureStatus::SIGNED->value
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sign document. Please try again.'
            ]);
        }
    }

    public function revertDocument(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isSignatory()) {
            abort(403);
        }

        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
        ]);

        $userRequest = UserRequest::findOrFail($validated['request_id']);

        if ($userRequest->signed_by !== $user->id) {
            abort(403, 'You can only revert documents you signed');
        }

        if (!$userRequest->canBeReverted()) {
            return response()->json([
                'success' => false,
                'message' => 'Document can no longer be reverted (older than 24 hours)'
            ]);
        }

        $signingService = app(DocumentSigningService::class);
        $success = $signingService->revertDocument($userRequest);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Document reverted successfully',
                'signature_status' => SignatureStatus::PENDING->value
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revert document. Please try again.'
            ]);
        }
    }
} 