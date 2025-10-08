<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearcherProfile extends Model
{
    protected $fillable = [
        'name',
        'title',
        'research_areas',
        'bio',
        'status_badge',
        'photo_path',
        'background_color',
        'profile_link',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'research_areas' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Scope for active researchers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered researchers
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
