<?php

use Illuminate\Support\Str;

if (! function_exists('price')) {
    function price($price)
    {
        $currency = \Rapidez\Core\Facades\Rapidez::config('currency/options/default');
        $locale = \Rapidez\Core\Facades\Rapidez::config('general/locale/code');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($price, $currency);
    }
}

if (! function_exists('vite_filename_with_chunkhash')) {
    function vite_filename_with_chunkhash($file)
    {
        $manifest = @json_decode(@file_get_contents(public_path('build/manifest.json')));
        if ($manifest) {
            foreach ($manifest as $path => $asset) {
                if (Str::endsWith($path, $file)) {
                    return $asset->file;
                }
            }
        }
    }
}

if (! function_exists('vite_filename_path')) {
    function vite_filename_path($file)
    {
        return vite_filename_paths($file)[0] ?? null;
    }
}

if (! function_exists('vite_filename_paths')) {
    function vite_filename_paths($file)
    {
        return Vite::getPathsByFilenames($file); // @phpstan-ignore-line This is a macro bind, which is not recognized by PHPStan.
    }
}

if (! function_exists('trans_fallback')) {
    function trans_fallback($key, $fallback, $replace = [], $locale = null)
    {
        $translator = app('translator');

        return $translator->has($key) ? $translator->get($key, $replace, $locale) : $fallback;
    }
}
