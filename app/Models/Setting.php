<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key','value'];

    public static function get(string $key, $default = null)
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }
        return Cache::rememberForever('setting_'.$key, function() use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting_'.$key);
    }
} 