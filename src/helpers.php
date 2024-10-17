<?php

if (! function_exists('price')) {
    function price(float $price): string|false
    {
        $configModel = config('rapidez.models.config');
        $currency = $configModel::getCachedByPath('currency/options/default');
        $locale = $configModel::getCachedByPath('general/locale/code', 'en_US');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($price, $currency);
    }
}

if (! function_exists('vite_filename_with_chunkhash')) {
    function vite_filename_with_chunkhash(string $file): ?string
    {
        $manifest = @json_decode(@file_get_contents(public_path('build/manifest.json')) ?: '');
        if ($manifest) {
            foreach ($manifest as $path => $asset) {
                if (Str::endsWith($path, $file)) {
                    return $asset->file;
                }
            }
        }

        return null;
    }
}

if (! function_exists('vite_filename_path')) {
    function vite_filename_path(string $file): ?string
    {
        $manifest = @json_decode(@file_get_contents(public_path('build/manifest.json')) ?: '');
        if ($manifest) {
            foreach ($manifest as $path => $asset) {
                if (Str::endsWith($path, $file)) {
                    return $path;
                }
            }
        }

        return null;
    }
}
