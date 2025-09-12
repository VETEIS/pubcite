<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'signatory_role',
        'signatory_name',
        'signed_at',
        'signed_document_path',
        'original_document_path',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specific signatory has signed a specific request
     */
    public static function hasSigned($requestId, $userId): bool
    {
        return self::where('request_id', $requestId)
                   ->where('user_id', $userId)
                   ->exists();
    }

    /**
     * Get all signatories who have signed a specific request
     */
    public static function getSignatoriesForRequest($requestId)
    {
        return self::where('request_id', $requestId)
                   ->with('user')
                   ->orderBy('signed_at')
                   ->get();
    }

    /**
     * Get all requests signed by a specific user
     */
    public static function getRequestsSignedByUser($userId)
    {
        return self::where('user_id', $userId)
                   ->with('request')
                   ->orderBy('signed_at', 'desc')
                   ->get();
    }
}