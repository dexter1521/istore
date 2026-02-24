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

if (!function_exists('currency_symbol')) {
    function currency_symbol($currency = null)
    {
        $code = $currency ?? setting('moneda', 'USD');
        return match (strtoupper($code)) {
            'USD' => '$',
            'MXN' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };
    }
}

if (!function_exists('show_prices')) {
    function show_prices(): bool
    {
        return (bool) (setting('mostrar_precios', '1') == '1');
    }
}
