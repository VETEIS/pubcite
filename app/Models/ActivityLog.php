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
    
    public function getRequestDisplayNameAttribute()
    {
        if ($this->userRequest) {
            return $this->userRequest->request_code;
        }
        
        $details = $this->details;
        return $details['request_code'] ?? 'Deleted Request';
    }
    
    public function getRequestUserAttribute()
    {
        if ($this->userRequest && $this->userRequest->user) {
            return $this->userRequest->user;
        }
        
        $details = $this->details;
        return (object) [
            'name' => $details['user_name'] ?? 'Unknown User',
            'email' => $details['user_email'] ?? 'Unknown Email',
        ];
    }
} 