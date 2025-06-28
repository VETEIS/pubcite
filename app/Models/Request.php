<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_code',
        'type',
        'status',
        'requested_at',
        'form_data',
        'pdf_path',
        'pdf_content',
        'token',
    ];

    protected $casts = [
        'form_data' => 'array',
        'requested_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPDFContent()
    {
        if ($this->pdf_content) {
            return base64_decode($this->pdf_content);
        }
        return null;
    }
} 