<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait EnsuresTemplateFiles
{
    /**
     * Ensure that a given DOCX template exists in storage. If it is missing (such as on a fresh deployment
     * where a persistent disk may not contain the template files), copy it from the read-only resources folder.
     */
    protected function ensureTemplateAvailable(string $filename): string
    {
        // Primary location where templates are expected at runtime
        $storageRelativePath = "templates/{$filename}";
        $storagePath = storage_path('app/' . $storageRelativePath);

        if (file_exists($storagePath)) {
            return $storagePath;
        }

        // Fallback location bundled with the repository (read-only)
        $resourcePath = resource_path('templates/' . $filename);

        if (!file_exists($resourcePath)) {
            throw new \RuntimeException("Template file not found: {$storagePath} (fallback {$resourcePath} also missing)");
        }

        // Ensure the storage templates directory exists
        if (!Storage::disk('local')->exists('templates')) {
            Storage::disk('local')->makeDirectory('templates');
        }

        // Copy the template from the repo resources into storage
        if (!@copy($resourcePath, $storagePath)) {
            throw new \RuntimeException("Failed to copy template from {$resourcePath} to {$storagePath}");
        }

        return $storagePath;
    }
}
