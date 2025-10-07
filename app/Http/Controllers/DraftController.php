<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Request as UserRequest;

class DraftController extends Controller
{
    public function apiIndex()
    {
        $user = Auth::user();
        
        // Get all drafts for the current user
        $drafts = UserRequest::where('user_id', $user->id)
            ->where('status', 'draft')
            ->orderByDesc('requested_at')
            ->get(['id', 'type', 'request_code', 'requested_at']);
        
        // SECURITY FIX: Removed sensitive user data from logs
        Log::info('Draft API called', [
            'drafts_count' => $drafts->count()
        ]);
        
        return response()->json([
            'success' => true,
            'drafts' => $drafts
        ]);
    }
    
    public function apiShow(UserRequest $draft)
    {
        $user = Auth::user();
        
        // Ensure the draft belongs to the current user
        if ($draft->user_id !== $user->id || $draft->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Draft not found or access denied.'
            ], 404);
        }
        
        // Decode form_data for frontend consumption
        $draftData = $draft->toArray();
        if (isset($draftData['form_data'])) {
            $draftData['form_data'] = json_decode($draftData['form_data'], true);
        }
        
        return response()->json([
            'success' => true,
            'draft' => $draftData
        ]);
    }
    
    public function destroy(UserRequest $draft)
    {
        $user = Auth::user();
        
        // Ensure the draft belongs to the current user
        if ($draft->user_id !== $user->id || $draft->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Draft not found or access denied.'
            ], 404);
        }
        
        try {
            // Delete associated files if they exist
            if ($draft->pdf_path) {
                $pdfData = json_decode($draft->pdf_path, true);
                
                // Delete PDF files
                if (isset($pdfData['pdfs'])) {
                    foreach ($pdfData['pdfs'] as $filePath) {
                        if (file_exists(storage_path('app/' . $filePath))) {
                            unlink(storage_path('app/' . $filePath));
                        }
                    }
                }
                
                // Delete DOCX files
                if (isset($pdfData['docxs'])) {
                    foreach ($pdfData['docxs'] as $filePath) {
                        if (file_exists(storage_path('app/' . $filePath))) {
                            unlink(storage_path('app/' . $filePath));
                        }
                    }
                }
            }
            
            // Delete the draft
            $draft->delete();
            
            Log::info('Draft deleted successfully', [
                'draft_id' => $draft->id,
                'user_id' => $user->id,
                'request_code' => $draft->request_code
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Draft deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete draft: ' . $e->getMessage(), [
                'draft_id' => $draft->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete draft. Please try again.'
            ], 500);
        }
    }
}
