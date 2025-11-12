<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;
use App\Traits\SanitizesFilePaths;

class AdminFileController extends Controller
{
    use SanitizesFilePaths;
    public function download(Request $request, string $type, string $filename)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized access');
            }

            $decodedFilename = base64_decode($filename);
            if (!$decodedFilename) {
                abort(400, 'Invalid filename');
            }

            $parts = explode('|', $decodedFilename);
            if (count($parts) !== 2) {
                abort(400, 'Invalid filename format');
            }

            $requestId = $parts[0];
            $actualFilename = $parts[1];

            $requestModel = RequestModel::find($requestId);
            if (!$requestModel) {
                abort(404, 'Request not found');
            }

            $filePath = $this->buildFilePath($type, $requestModel, $actualFilename);
            if (!$filePath) {
                abort(404, 'File not found');
            }
            
            Log::info('Admin file download path construction', [
                'type' => $type,
                'actual_filename' => $actualFilename,
                'constructed_path' => $filePath,
                'request_id' => $requestId
            ]);

            // Sanitize file path to prevent directory traversal
            $filePath = $this->sanitizePath($filePath);
            
            // Use local disk only (standardized storage)
            $fullPath = Storage::disk('local')->path($filePath);
            if (!file_exists($fullPath)) {
                Log::error('File not found on local disk', [
                    'file_path' => $filePath,
                    'local_path' => $fullPath,
                    'local_exists' => file_exists($fullPath)
                ]);
                abort(404, 'File not found: ' . basename($filePath));
            }

            Log::info('Admin file download', [
                'admin_id' => Auth::id(),
                'request_id' => $requestId,
                'file_type' => $type,
                'filename' => $actualFilename,
                'ip_address' => $request->ip()
            ]);

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

    private function buildFilePath(string $type, RequestModel $request, string $filename): ?string
    {
        $userId = $request->user_id;
        $requestCode = $request->request_code;
        $basePath = "requests/{$userId}/{$requestCode}";

        switch ($type) {
            case 'pdf':
            case 'docx':
                return $this->getSanitizedStoragePath($basePath, $filename);
            
            case 'signed':
                $path = $request->signed_document_path;
                // Remove any storage path prefixes
                $path = preg_replace('#^(storage/app/(public|local)/)?#', '', $path);
                return $this->sanitizePath($path);
            
            case 'backup':
                $path = $request->original_document_path;
                // Remove any storage path prefixes
                $path = preg_replace('#^(storage/app/(public|local)/)?#', '', $path);
                return $this->sanitizePath($path);
            
            default:
                return null;
        }
    }

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
