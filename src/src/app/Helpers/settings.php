<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {

    function setting(string $clave, $default = null)
    {
        return Cache::remember("setting_{$clave}", 3600, function () use ($clave, $default) {
            return Setting::where('clave', $clave)->value('valor') ?? $default;
        });
    }
}
