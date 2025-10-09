<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SignatureStatus;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_code',
        'type',
        'status',
        'workflow_state',
        'signature_status',
        'requested_at',
        'signed_at',
        'signed_by',
        'signed_document_path',
        'original_document_path',
        'form_data',
        'pdf_path',
        'pdf_content',
        'token',
    ];

    protected $casts = [
        'form_data' => 'array',
        'requested_at' => 'datetime',
        'signed_at' => 'datetime',
        'signature_status' => SignatureStatus::class,
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function signatures()
    {
        return $this->hasMany(RequestSignature::class);
    }

    public function isSigned(): bool
    {
        return $this->signature_status === SignatureStatus::SIGNED;
    }

    public function canBeReverted(): bool
    {
        if (!$this->signed_at) {
            return false;
        }
        
        // Check if signed within last 24 hours
        return $this->signed_at->diffInHours(now()) < 24;
    }

    public function getPDFContent()
    {
        if ($this->pdf_content) {
            return base64_decode($this->pdf_content);
        }
        return null;
    }

    /**
     * Check if a specific user has signed this request
     */
    public function hasBeenSignedBy($userId): bool
    {
        return $this->signatures()->where('user_id', $userId)->exists();
    }

    /**
     * Get all signatories who have signed this request
     */
    public function getSignatories()
    {
        return $this->signatures()->with('user')->orderBy('signed_at')->get();
    }

    /**
     * Check if all required signatories have signed this request
     */
    public function isFullySigned(): bool
    {
        // This would need to be implemented based on business logic
        // For now, we'll consider it fully signed if at least one signatory has signed
        return $this->signatures()->exists();
    }
} 