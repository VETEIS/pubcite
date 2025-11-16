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

    /**
     * Get signature progress as a string (e.g., "2/5")
     * Returns the number of signatures collected out of total required stages
     */
    public function getSignatureProgress(): string
    {
        // Total stages in workflow: 5 (user, research_manager, dean, deputy_director, director)
        $totalStages = 5;
        
        // Count how many signatures have been collected
        $signaturesCount = $this->signatures()->count();
        
        // If workflow is completed, all signatures are done
        if ($this->workflow_state === 'completed') {
            return "{$totalStages}/{$totalStages}";
        }
        
        // Determine current stage based on workflow_state
        $stageMap = [
            'pending_user_signature' => 0,
            'pending_research_manager' => 1,
            'pending_dean' => 2,
            'pending_deputy_director' => 3,
            'pending_director' => 4,
            'completed' => 5,
        ];
        
        $currentStage = $stageMap[$this->workflow_state] ?? 0;
        
        // The number of signatures should match the current stage
        // (e.g., if at pending_dean, user and research_manager should have signed = 2 signatures)
        // But we use actual signature count for accuracy
        $progress = max($signaturesCount, $currentStage);
        
        return "{$progress}/{$totalStages}";
    }

    /**
     * Get the current workflow stage name in a readable format
     */
    public function getWorkflowStageName(): string
    {
        $stageNames = [
            'pending_user_signature' => 'User',
            'pending_research_manager' => 'Center',
            'pending_dean' => 'Dean',
            'pending_deputy_director' => 'Deputy',
            'pending_director' => 'Director',
            'completed' => 'Completed',
        ];
        
        return $stageNames[$this->workflow_state] ?? 'Unknown';
    }
} 