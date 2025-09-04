<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;

class AdminFileController extends Controller
{
    /**
     * Download a file securely for admin users
     */
    public function download(Request $request, string $type, string $filename)
    {
        try {
            // Verify admin permissions
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized access');
            }

            // Decode the filename (it's base64 encoded for security)
            $decodedFilename = base64_decode($filename);
            if (!$decodedFilename) {
                abort(400, 'Invalid filename');
            }

            // Extract request ID and actual filename from the encoded string
            $parts = explode('|', $decodedFilename);
            if (count($parts) !== 2) {
                abort(400, 'Invalid filename format');
            }

            $requestId = $parts[0];
            $actualFilename = $parts[1];

            // Verify the request exists and belongs to a valid user
            $requestModel = RequestModel::find($requestId);
            if (!$requestModel) {
                abort(404, 'Request not found');
            }

            // Build the file path based on type
            $filePath = $this->buildFilePath($type, $requestModel, $actualFilename);
            if (!$filePath) {
                abort(404, 'File not found');
            }
            
            // For signed and backup documents, the path is already complete
            if (in_array($type, ['signed', 'backup'])) {
                $filePath = $filePath; // Use as-is
            }
            
            // Debug logging
            Log::info('Admin file download path construction', [
                'type' => $type,
                'actual_filename' => $actualFilename,
                'constructed_path' => $filePath,
                'request_id' => $requestId
            ]);

            // Check if file exists on local disk first, then public for backward compatibility
            $fullPath = Storage::disk('local')->path($filePath);
            if (!file_exists($fullPath)) {
                $fullPath = Storage::disk('public')->path($filePath);
                if (!file_exists($fullPath)) {
                    abort(404, 'File not found on disk');
                }
            }

            // Log the download for audit purposes
            Log::info('Admin file download', [
                'admin_id' => Auth::id(),
                'request_id' => $requestId,
                'file_type' => $type,
                'filename' => $actualFilename,
                'ip_address' => $request->ip()
            ]);

            // Return the file for download with security headers
            return response()->download($fullPath, $actualFilename, [
                'Content-Type' => $this->getContentType($actualFilename),
                'Content-Disposition' => 'attachment; filename="' . $actualFilename . '"',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin file download error', [
                'admin_id' => Auth::id(),
                'type' => $type,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'Error downloading file');
        }
    }

    /**
     * Build the file path based on type and request
     */
    private function buildFilePath(string $type, RequestModel $request, string $filename): ?string
    {
        $userId = $request->user_id;
        $requestCode = $request->request_code;
        $basePath = "requests/{$userId}/{$requestCode}";

        switch ($type) {
            case 'pdf':
            case 'docx':
                // All files are stored directly in the request folder
                return $basePath . '/' . $filename;
            
            case 'signed':
                return $request->signed_document_path;
            
            case 'backup':
                return $request->original_document_path;
            
            default:
                return null;
        }
    }

    /**
     * Get the appropriate content type for the file
     */
    private function getContentType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'application/pdf';
            case 'docx':
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            case 'doc':
                return 'application/msword';
            default:
                return 'application/octet-stream';
        }
    }
}
