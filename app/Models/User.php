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
        'name', 'email', 'password', 'role', 'signatory_type',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret',
    ];

    protected $appends = [ 'profile_photo_url', ];

    protected function casts(): array
    {
        return [ 'email_verified_at' => 'datetime', 'password' => 'hashed', ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isUser(): bool { return $this->role === 'user'; }
    public function isSignatory(): bool { return $this->role === 'signatory'; }
    public function signatoryType(): ?string { return $this->signatory_type; }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
