<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\SanitizesFilePaths;

class SignatureController extends Controller
{
    use SanitizesFilePaths;
    public function index(Request $request)
    {
        $user = Auth::user();
        $signatures = Signature::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'label', 'path', 'created_at']);

        if ($request->expectsJson()) {
            return response()->json($signatures);
        }

        return view('signatures.index', compact('signatures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'signature' => 'required|file|mimes:png,jpg,jpeg|max:5120', // 5MB max
            'label' => 'nullable|string|max:120',
        ]);

        $user = Auth::user();
        $file = $request->file('signature');
        
        // Sanitize path to prevent directory traversal
        $sanitizedPath = $this->sanitizePath("signatures/{$user->id}");
        $sanitizedFilename = $this->sanitizePath(basename($file->getClientOriginalName()));
        
        $path = $file->storeAs($sanitizedPath, $sanitizedFilename, 'local');
        
        $signature = Signature::create([
            'user_id' => $user->id,
            'label' => $request->input('label'),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'signature' => $signature,
                'message' => 'Signature uploaded successfully.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Signature uploaded successfully.');
    }

    public function show(Signature $signature)
    {
        if (!Gate::allows('view', $signature)) {
            abort(404);
        }

        // Sanitize path to prevent directory traversal
        $sanitizedPath = $this->sanitizePath($signature->path);
        
        if (!Storage::disk('local')->exists($sanitizedPath)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($sanitizedPath));
    }

    public function edit(Signature $signature)
    {
        if (!Gate::allows('update', $signature)) {
            abort(404);
        }

        return view('signatures.edit', compact('signature'));
    }

    public function update(Request $request, Signature $signature)
    {
        if (!Gate::allows('update', $signature)) {
            abort(404);
        }

        $request->validate([
            'label' => 'nullable|string|max:120',
            'signature' => 'nullable|file|mimes:png,jpg,jpeg|max:5120', // 5MB max
        ]);

        $data = ['label' => $request->input('label')];

        if ($request->hasFile('signature')) {
            $file = $request->file('signature');
            
            if (Storage::disk('local')->exists($signature->path)) {
                Storage::disk('local')->delete($signature->path);
            }
            
            // Sanitize path to prevent directory traversal
            $sanitizedPath = $this->sanitizePath("signatures/{$signature->user_id}");
            $sanitizedFilename = $this->sanitizePath(basename($file->getClientOriginalName()));
            
            $path = $file->storeAs($sanitizedPath, $sanitizedFilename, 'local');
            
            $data['path'] = $path;
            $data['mime_type'] = $file->getMimeType();
        }

        $signature->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'signature' => $signature,
                'message' => 'Signature updated successfully.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Signature updated successfully.');
    }

    public function destroy(Signature $signature)
    {
        if (!Gate::allows('delete', $signature)) {
            abort(404);
        }

        if (Storage::disk('local')->exists($signature->path)) {
            Storage::disk('local')->delete($signature->path);
        }

        $signature->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Signature deleted successfully.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Signature deleted successfully.');
    }
}
