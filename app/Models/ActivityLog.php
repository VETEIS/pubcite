<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Request as UserRequest;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'request_id',
        'action',
        'details',
        'created_at',
    ];
    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userRequest()
    {
        return $this->belongsTo(UserRequest::class)->withTrashed();
    }
    
    /**
     * Get a display name for the request (works even if request is deleted)
     */
    public function getRequestDisplayNameAttribute()
    {
        if ($this->userRequest) {
            return $this->userRequest->request_code;
        }
        
        // If request is deleted, get from details
        $details = $this->details;
        return $details['request_code'] ?? 'Deleted Request';
    }
    
    /**
     * Get the user who made the request (works even if request is deleted)
     */
    public function getRequestUserAttribute()
    {
        if ($this->userRequest && $this->userRequest->user) {
            return $this->userRequest->user;
        }
        
        // If request is deleted, get from details
        $details = $this->details;
        return (object) [
            'name' => $details['user_name'] ?? 'Unknown User',
            'email' => $details['user_email'] ?? 'Unknown Email',
        ];
    }
} 