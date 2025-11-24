# Researcher Management - Complete Feature Analysis

## Current Implementation Features

### 1. **Data Fields**
- `name` (string, max 255, required if title empty)
- `title` (string, max 255, required if name empty)
- `bio` (string, max 1000, optional)
- `research_areas` (string, comma-separated, max 500, converted to array)
- `status_badge` (enum: Active, Research, Innovation, Leadership, default: Active)
- `background_color` (enum: maroon, blue, green, purple, orange, teal, rose, default: maroon)
- `profile_link` (email, max 255, optional)
- `scopus_link` (URL, max 500, optional)
- `orcid_link` (URL, max 500, optional)
- `wos_link` (URL, max 500, optional)
- `google_scholar_link` (URL, max 500, optional)
- `photo_path` (string, max 500, optional)
- `sort_order` (integer, auto-generated from array index)
- `is_active` (boolean, always true)

### 2. **UI Features**

#### Card Display
- Collapsible cards (expand/collapse with Alpine.js x-show)
- Header shows: photo (or icon), name, title, research areas
- Real-time updates: name/title/research areas update in header as user types
- Profile picture preview in both header and expanded section
- Remove button for each card
- Floating action button to add new researcher

#### Form Fields Layout
- Row 1: Profile Picture | Biography
- Row 2: Full Name | Email Address
- Row 3: Title/Position | Status Badge
- Row 4: Research Areas | Card Background Color
- Row 5: Research Profile Links (SCOPUS, ORCID, WOS, Google Scholar)

### 3. **Photo Upload Features**
- Accepts: image/* (jpeg, png, jpg, gif, webp)
- Max size: 10MB (10240 KB)
- Client-side compression: Compresses images > 1.8MB before upload
- WebP conversion: Server converts uploaded images to WebP format
- Photo preview: Shows preview immediately when file selected
- Photo preservation: Keeps existing photo if not changed
- Photo matching: Matches photos by name/title when updating

### 4. **Validation Rules**
- At least `name` OR `title` must be provided
- Empty rows are filtered out (skipped)
- Maximum 100 researchers limit
- Photo validation: image|mimes:jpeg,png,jpg,gif,webp|max:10240

### 5. **Business Logic**
- **Delete and Recreate**: All researchers are deleted, then recreated from form data
- **Sort Order**: Based on array index (0, 1, 2, ...)
- **Research Areas**: Comma-separated string converted to array
- **Empty Row Filtering**: Rows with no name, title, bio, or research areas are skipped
- **Photo Matching**: When updating, matches existing photos by name+title combination

### 6. **Change Detection**
- Tracks original values on page load
- Compares current values with original
- Enables/disables "Save Changes" button based on changes
- Detects file uploads as changes
- Detects added/removed researchers

### 7. **JavaScript Functions**
- `addResearcherRow()`: Adds new researcher card dynamically
- `removeResearcherRow()`: Removes researcher card
- `checkResearcherChanges()`: Detects changes and updates save button
- `previewImage()`: Shows photo preview and compresses if needed
- `compressImage()`: Client-side image compression

### 8. **Backend Processing**
- `updateResearchers()`: Main processing method
- `storePhotoAsWebp()`: Converts and stores photo as WebP
- `safeStoreFile()`: Fallback file storage method
- Transaction-based save (all or nothing)
- Response cache clearing after save
- Activity logging (if implemented)

### 9. **Error Handling**
- Validation errors displayed at top of section
- Transaction rollback on failure
- Photo upload error handling
- WebP conversion fallback to original format

### 10. **Success/Error Messages**
- Success: "X researchers updated successfully" or "Researcher updated successfully"
- Error: "No valid researchers to save..." or "Maximum 100 researchers allowed..."

## Implementation Details

### Photo Upload Flow
1. User selects image file
2. Client-side compression if > 1.8MB
3. Preview shown immediately
4. On submit, file uploaded to server
5. Server converts to WebP using GD library
6. WebP stored in `storage/app/public/researcher-photos/`
7. Path stored in database

### Research Areas Processing
- Input: Comma-separated string ("AI, Machine Learning, Data Science")
- Processing: Split by comma, trim, filter empty, convert to array
- Storage: Stored as JSON array in database

### Change Detection Logic
1. Store original values on page load
2. Listen to all input/select/textarea changes
3. Compare current vs original
4. Enable save button if changes detected
5. Disable save button if no changes

### Card State Management
- Server-rendered cards: Use Alpine.js `x-data="{ isExpanded: false }"`
- Dynamically added cards: Use vanilla JS for expand/collapse
- Fields hidden by default (x-show="isExpanded")
- Toggle button rotates chevron icon

## Files Involved

### Backend
- `app/Http/Controllers/SettingsController.php`
  - `updateResearchers()` method
  - `storePhotoAsWebp()` method
  - `safeStoreFile()` method

### Frontend
- `resources/views/admin/settings.blade.php`
  - Researcher section HTML (lines ~804-1066)
  - JavaScript functions (lines ~1237-3257)
  - Form submission handling

### Model
- `app/Models/ResearcherProfile.php`
  - Fillable fields
  - Casts (research_areas as array)
  - Scopes (active, ordered)

## Dependencies
- Alpine.js (for collapsible cards)
- GD library (for WebP conversion)
- Laravel Storage facade
- ResponseCache (for cache clearing)

## Notes
- Activity logging may not be implemented for researchers (need to check)
- Photo matching by name+title may not work perfectly if name/title changes
- Empty rows are silently filtered (no user feedback)

