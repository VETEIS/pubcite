<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
} 