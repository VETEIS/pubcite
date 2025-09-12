<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'signatory_type', 'profile_photo_path', 'auth_provider', 'privacy_accepted_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret',
    ];

    protected $appends = [];

    protected function casts(): array
    {
        return [ 
            'email_verified_at' => 'datetime', 
            'password' => 'hashed',
            'privacy_accepted_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isUser(): bool { return $this->role === 'user'; }
    public function isSignatory(): bool { return $this->role === 'signatory'; }
    public function signatoryType(): ?string { return $this->signatory_type; }
    
    public function hasAcceptedPrivacy(): bool 
    { 
        return !is_null($this->privacy_accepted_at); 
    }

    /**
     * Mutator to automatically convert name to uppercase for signatories
     */
    public function setNameAttribute($value)
    {
        // Only convert to uppercase if the user is being set as a signatory
        if ($this->role === 'signatory' || (request()->has('role') && request()->input('role') === 'signatory')) {
            $this->attributes['name'] = strtoupper($value);
        } else {
            $this->attributes['name'] = $value;
        }
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }



    /**
     * Get the user's profile photo URL.
     * If profile_photo_path is a full URL (Google), return it directly.
     * Otherwise, use the default Jetstream behavior.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            // Check if it's a full URL (starts with http/https)
            if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
                // For Google profile pictures, ensure we're using the correct URL format
                if (str_contains($this->profile_photo_path, 'googleusercontent.com')) {
                    // Ensure the URL uses HTTPS and has proper sizing parameters
                    $url = str_replace('http://', 'https://', $this->profile_photo_path);
                    // Add size parameter if not present
                    if (!str_contains($url, '=')) {
                        $url .= '=s96-c'; // 96x96 pixels, cropped
                    }
                    return $url;
                }
                return $this->profile_photo_path;
            }
        }
        
        // Use default Jetstream behavior for local files
        return $this->hasProfilePhoto()
            ? $this->profilePhotoUrl
            : $this->defaultProfilePhotoUrl();
    }
}
