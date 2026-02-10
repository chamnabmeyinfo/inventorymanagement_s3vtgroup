<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value. DB overrides config.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = 'setting:' . $key;
        $value = Cache::remember($cacheKey, 3600, function () use ($key) {
            $s = static::where('key', $key)->first();
            return $s?->value;
        });
        return $value ?? $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting:' . $key);
    }

    /**
     * Get all settings as key => value.
     */
    public static function allMap(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
