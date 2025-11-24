<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ResearcherProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Spatie\ResponseCache\Facades\ResponseCache;

class ResearcherManager extends Component
{
    use WithFileUploads;

    public $researchers = [];
    public $hasChanges = false;
    public $originalResearchers = [];
    public $editingIndex = null;
    public $showModal = false;
    public $confirmingResearcherDeletion = false;
    public $researcherToDelete = null;

    protected $listeners = ['refresh' => '$refresh'];

    public function mount()
    {
        $this->loadResearchers();
        $this->storeOriginalState();
    }

    public function loadResearchers()
    {
        $existing = ResearcherProfile::ordered()->get();
        
        $this->researchers = $existing->map(function ($researcher) {
            return [
                'id' => $researcher->id,
                'prefix' => $researcher->prefix ?? '',
                'name' => $researcher->name ?? '',
                'title' => $researcher->title ?? '',
                'bio' => $researcher->bio ?? '',
                'research_areas' => is_array($researcher->research_areas) 
                    ? implode(', ', $researcher->research_areas) 
                    : ($researcher->research_areas ?? ''),
                'status_badge' => $researcher->status_badge ?? 'Active',
                'background_color' => $researcher->background_color ?? 'maroon',
                'profile_link' => $researcher->profile_link ?? '',
                'scopus_link' => $researcher->scopus_link ?? '',
                'orcid_link' => $researcher->orcid_link ?? '',
                'wos_link' => $researcher->wos_link ?? '',
                'google_scholar_link' => $researcher->google_scholar_link ?? '',
                'photo_path' => $researcher->photo_path,
                'photo' => null,
            ];
        })->toArray();
    }

    public function storeOriginalState()
    {
        $this->originalResearchers = collect($this->researchers)->map(function ($r) {
            return [
                'prefix' => $r['prefix'] ?? '',
                'name' => $r['name'] ?? '',
                'title' => $r['title'] ?? '',
                'bio' => $r['bio'] ?? '',
                'research_areas' => $r['research_areas'] ?? '',
                'status_badge' => $r['status_badge'] ?? 'Active',
                'background_color' => $r['background_color'] ?? 'maroon',
                'profile_link' => $r['profile_link'] ?? '',
                'scopus_link' => $r['scopus_link'] ?? '',
                'orcid_link' => $r['orcid_link'] ?? '',
                'wos_link' => $r['wos_link'] ?? '',
                'google_scholar_link' => $r['google_scholar_link'] ?? '',
                'photo_path' => $r['photo_path'] ?? null,
            ];
        })->toArray();
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'researchers.')) {
            $this->checkForChanges();
        }
    }

    public function checkForChanges()
    {
        $current = collect($this->researchers)->map(function ($r) {
            return [
                'prefix' => $r['prefix'] ?? '',
                'name' => trim($r['name'] ?? ''),
                'title' => trim($r['title'] ?? ''),
                'bio' => trim($r['bio'] ?? ''),
                'research_areas' => trim($r['research_areas'] ?? ''),
                'status_badge' => $r['status_badge'] ?? 'Active',
                'background_color' => $r['background_color'] ?? 'maroon',
                'profile_link' => trim($r['profile_link'] ?? ''),
                'scopus_link' => trim($r['scopus_link'] ?? ''),
                'orcid_link' => trim($r['orcid_link'] ?? ''),
                'wos_link' => trim($r['wos_link'] ?? ''),
                'google_scholar_link' => trim($r['google_scholar_link'] ?? ''),
                'photo_path' => $r['photo_path'] ?? null,
            ];
        })->toArray();

        if (count($current) !== count($this->originalResearchers)) {
            $this->hasChanges = true;
            return;
        }

        foreach ($current as $index => $researcher) {
            $original = $this->originalResearchers[$index] ?? [];
            
            foreach ($researcher as $key => $value) {
                if (($original[$key] ?? '') !== $value) {
                    $this->hasChanges = true;
                    return;
                }
            }
        }

        foreach ($this->researchers as $researcher) {
            if (!empty($researcher['photo'])) {
                $this->hasChanges = true;
                return;
            }
        }

        $this->hasChanges = false;
    }

    public function openModal($index = null)
    {
        \Log::debug('[ResearcherManager] openModal called', [
            'index' => $index,
            'researchers_count' => count($this->researchers),
            'has_researcher_at_index' => $index !== null ? isset($this->researchers[$index]) : false,
            'current_showModal' => $this->showModal,
            'current_editingIndex' => $this->editingIndex,
        ]);
        
        $this->editingIndex = $index;
        $this->showModal = true;
        
        \Log::debug('[ResearcherManager] openModal completed', [
            'editingIndex' => $this->editingIndex,
            'showModal' => $this->showModal,
            'showModal_type' => gettype($this->showModal),
        ]);
    }

    public function closeModal()
    {
        \Log::debug('[ResearcherManager] closeModal called', [
            'current_showModal' => $this->showModal,
            'current_editingIndex' => $this->editingIndex,
        ]);
        
        $this->showModal = false;
        $this->editingIndex = null;
        
        \Log::debug('[ResearcherManager] closeModal completed', [
            'showModal' => $this->showModal,
            'editingIndex' => $this->editingIndex,
        ]);
    }

    public function addResearcher()
    {
        $this->researchers[] = [
            'prefix' => '',
            'name' => '',
            'title' => '',
            'bio' => '',
            'research_areas' => '',
            'status_badge' => 'Active',
            'background_color' => 'maroon',
            'profile_link' => '',
            'scopus_link' => '',
            'orcid_link' => '',
            'wos_link' => '',
            'google_scholar_link' => '',
            'photo_path' => null,
            'photo' => null,
        ];
        
        $this->openModal(count($this->researchers) - 1);
        $this->checkForChanges();
    }

    public function editResearcher($index)
    {
        \Log::debug('[ResearcherManager] editResearcher called', [
            'index' => $index,
            'researchers_count' => count($this->researchers),
            'has_researcher_at_index' => isset($this->researchers[$index]),
            'current_showModal' => $this->showModal,
            'current_editingIndex' => $this->editingIndex,
        ]);
        
        try {
            $this->openModal($index);
            
            \Log::debug('[ResearcherManager] After openModal', [
                'showModal' => $this->showModal,
                'editingIndex' => $this->editingIndex,
            ]);
            
            // Dispatch browser event for debugging
            $this->dispatch('researcher-edit-opened', index: $index);
        } catch (\Exception $e) {
            \Log::error('[ResearcherManager] Error in editResearcher', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function confirmResearcherDeletion($index)
    {
        $this->researcherToDelete = $index;
        $this->confirmingResearcherDeletion = true;
    }

    public function removeResearcher()
    {
        if ($this->researcherToDelete !== null) {
            unset($this->researchers[$this->researcherToDelete]);
            $this->researchers = array_values($this->researchers);
            
            $this->checkForChanges();
        }
        
        $this->confirmingResearcherDeletion = false;
        $this->researcherToDelete = null;
    }

    public function updatedResearchers($value, $key)
    {
        $this->checkForChanges();
    }

    public function save()
    {
        // Validate
        $rules = [];
        foreach ($this->researchers as $index => $researcher) {
            $rules["researchers.{$index}.prefix"] = 'nullable|string|max:20';
            $rules["researchers.{$index}.name"] = 'nullable|string|max:255';
            $rules["researchers.{$index}.title"] = 'nullable|string|max:255';
            $rules["researchers.{$index}.bio"] = 'nullable|string|max:1000';
            $rules["researchers.{$index}.research_areas"] = 'required|string|max:500';
            $rules["researchers.{$index}.status_badge"] = 'nullable|string|max:50';
            $rules["researchers.{$index}.profile_link"] = 'nullable|string|max:255';
            $rules["researchers.{$index}.scopus_link"] = 'nullable|string|max:500';
            $rules["researchers.{$index}.orcid_link"] = 'nullable|string|max:500';
            $rules["researchers.{$index}.wos_link"] = 'nullable|string|max:500';
            $rules["researchers.{$index}.google_scholar_link"] = 'nullable|string|max:500';
            
            if (isset($researcher['photo']) && $researcher['photo']) {
                $rules["researchers.{$index}.photo"] = 'image|mimes:jpeg,png,jpg,gif,webp|max:10240';
            }
        }

        $this->validate($rules);

        if (count($this->researchers) > 100) {
            $this->dispatch('show-notification', 
                type: 'error',
                message: 'Maximum 100 researchers allowed. Please remove some researchers and try again.'
            );
            return;
        }

        $payloads = [];
        $existingResearchers = ResearcherProfile::ordered()->get()->keyBy(function ($r) {
            return $r->name . '|' . $r->title;
        });

        foreach ($this->researchers as $index => $researcher) {
            $name = trim($researcher['name'] ?? '');
            $title = trim($researcher['title'] ?? '');
            $bio = trim($researcher['bio'] ?? '');
            $researchAreasInput = trim($researcher['research_areas'] ?? '');

            // Skip empty researchers
            if (empty($name) && empty($title) && empty($bio) && empty($researchAreasInput)) {
                continue;
            }

            // Require at least name or title, and research areas is mandatory
            if (empty($name) && empty($title)) {
                continue;
            }

            // Research areas is mandatory
            if (empty($researchAreasInput)) {
                continue;
            }

            $researchAreas = collect(explode(',', $researchAreasInput))
                ->map(fn ($area) => trim($area))
                ->filter()
                ->values()
                ->all();

            // Ensure research areas is not empty after processing
            if (empty($researchAreas)) {
                continue;
            }

            $photoPath = null;
            if (isset($researcher['photo']) && $researcher['photo']) {
                $photoPath = $this->storePhotoAsWebp($researcher['photo']);
            } elseif (!empty($researcher['photo_path'])) {
                $photoPath = $researcher['photo_path'];
            } else {
                $key = $name . '|' . $title;
                if ($existingResearchers->has($key) && $existingResearchers[$key]->photo_path) {
                    $photoPath = $existingResearchers[$key]->photo_path;
                }
            }

            $payloads[] = [
                'prefix' => !empty($researcher['prefix']) ? trim($researcher['prefix']) : null,
                'name' => $name,
                'title' => $title,
                'research_areas' => $researchAreas,
                'bio' => $bio,
                'status_badge' => $researcher['status_badge'] ?? 'Active',
                'background_color' => $researcher['background_color'] ?? 'maroon',
                'profile_link' => !empty($researcher['profile_link']) ? trim($researcher['profile_link']) : null,
                'scopus_link' => !empty($researcher['scopus_link']) ? trim($researcher['scopus_link']) : null,
                'orcid_link' => !empty($researcher['orcid_link']) ? trim($researcher['orcid_link']) : null,
                'wos_link' => !empty($researcher['wos_link']) ? trim($researcher['wos_link']) : null,
                'google_scholar_link' => !empty($researcher['google_scholar_link']) ? trim($researcher['google_scholar_link']) : null,
                'photo_path' => $photoPath,
                'sort_order' => count($payloads),
                'is_active' => true,
            ];
        }

        if (empty($payloads)) {
            $this->dispatch('show-notification',
                type: 'error',
                message: 'No valid researchers to save. Please ensure at least one researcher has a name or title.'
            );
            return;
        }

        try {
            DB::transaction(function () use ($payloads) {
                ResearcherProfile::query()->delete();
                foreach ($payloads as $data) {
                    ResearcherProfile::create($data);
                }
            });

            try {
                ResponseCache::clear();
            } catch (\Throwable $e) {
                Log::warning('Failed to clear response cache', ['error' => $e->getMessage()]);
            }

            try {
                if (class_exists(\App\Models\ActivityLog::class)) {
                    \App\Models\ActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'settings_updated',
                        'details' => [
                            'category' => 'researchers',
                            'count' => count($payloads),
                            'updated_by' => auth()->user()->name ?? 'System',
                        ],
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Activity logging is optional
            }

            $message = count($payloads) === 1 
                ? 'Researcher updated successfully.' 
                : 'Researchers updated successfully.';
            
            $this->dispatch('show-notification',
                type: 'success',
                message: $message
            );
            
            $this->loadResearchers();
            $this->storeOriginalState();
            $this->hasChanges = false;
            $this->closeModal();
            
        } catch (\Exception $e) {
            Log::error('Failed to save researchers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('show-notification',
                type: 'error',
                message: 'Failed to save researchers: ' . $e->getMessage()
            );
        }
    }

    /**
     * Store uploaded photo as WebP on the public disk.
     */
    private function storePhotoAsWebp(UploadedFile $photo): ?string
    {
        try {
            if (!$photo->isValid()) {
                return null;
            }

            if (function_exists('imagewebp')) {
                $path = $photo->getRealPath();
                
                if (!$path || !file_exists($path) || !is_readable($path)) {
                    $path = $photo->getPathname();
                    if (!$path || !file_exists($path) || !is_readable($path)) {
                        return $this->safeStoreFile($photo);
                    }
                }

                try {
                    $mime = (string) $photo->getMimeType();
                } catch (\Throwable $e) {
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $mimeMap = [
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                    ];
                    $mime = $mimeMap[$extension] ?? 'image/jpeg';
                }

                $source = null;
                if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                    if (!function_exists('imagecreatefromjpeg')) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefromjpeg($path);
                } elseif ($mime === 'image/png') {
                    if (!function_exists('imagecreatefrompng')) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefrompng($path);
                    if ($source) {
                        imagepalettetotruecolor($source);
                        imagealphablending($source, true);
                        imagesavealpha($source, true);
                    }
                } elseif ($mime === 'image/gif') {
                    if (!function_exists('imagecreatefromgif')) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefromgif($path);
                } else {
                    if (!function_exists('imagecreatefromstring')) {
                        return $this->safeStoreFile($photo);
                    }
                    $bytes = @file_get_contents($path);
                    if ($bytes === false) {
                        return $this->safeStoreFile($photo);
                    }
                    $source = @imagecreatefromstring($bytes);
                }

                if (!$source) {
                    return $this->safeStoreFile($photo);
                }

                ob_start();
                @imagewebp($source, null, 85);
                imagedestroy($source);
                $webpData = ob_get_clean();
                
                if (!$webpData) {
                    return $this->safeStoreFile($photo);
                }

                $filename = 'researcher-photos/' . bin2hex(random_bytes(16)) . '.webp';
                Storage::disk('public')->put($filename, $webpData, 'public');
                return $filename;
            }

            return $this->safeStoreFile($photo);
        } catch (\Throwable $e) {
            Log::warning('WebP conversion failed', ['error' => $e->getMessage()]);
            return $this->safeStoreFile($photo);
        }
    }

    private function safeStoreFile(UploadedFile $photo, string $directory = 'researcher-photos'): ?string
    {
        try {
            return $photo->store($directory, 'public');
        } catch (\Throwable $storeError) {
            $path = $photo->getRealPath() ?: $photo->getPathname();
            if ($path && file_exists($path) && is_file($path) && is_readable($path)) {
                $extension = $photo->getClientOriginalExtension() ?: 'jpg';
                $filename = $directory . '/' . bin2hex(random_bytes(16)) . '.' . $extension;
                $fileContents = file_get_contents($path);
                if ($fileContents !== false) {
                    Storage::disk('public')->put($filename, $fileContents, 'public');
                    return $filename;
                }
            }
            return null;
        }
    }

    /**
     * Format name as initials + last name
     * Example: "Vincent Edriel T. Escoton" -> "V.E.T.Escoton"
     */
    public function formatNameAsInitials($name, $prefix = '')
    {
        if (empty($name)) {
            return 'New Researcher';
        }

        $parts = array_filter(explode(' ', trim($name)));
        
        if (count($parts) <= 1) {
            return ($prefix ? $prefix . ' ' : '') . $name;
        }

        // Get first name, middle names, and last name
        $firstName = $parts[0];
        $lastName = array_pop($parts);
        $middleParts = array_slice($parts, 1);

        // Build middle initial(s) - take first character of each middle name
        $middleInitials = '';
        if (!empty($middleParts)) {
            $middleInitialsArray = [];
            foreach ($middleParts as $part) {
                $part = trim($part);
                if (!empty($part)) {
                    // If part already ends with a period (like "T."), keep it as is
                    if (substr($part, -1) === '.') {
                        $middleInitialsArray[] = $part;
                    } else {
                        // Otherwise, take first character and add period
                        $middleInitialsArray[] = strtoupper($part[0]) . '.';
                    }
                }
            }
            $middleInitials = ' ' . implode(' ', $middleInitialsArray);
        }

        // Build formatted name: prefix + first name + middle initial(s) + last name
        $formatted = $firstName . $middleInitials . ' ' . $lastName;
        return ($prefix ? $prefix . ' ' : '') . $formatted;
    }

    public function render()
    {
        return view('livewire.researcher-manager');
    }
}
