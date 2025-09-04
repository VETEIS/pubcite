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
} 