<?php

if (!function_exists('price')) {
    function price($price)
    {
        $configModel = config('rapidez.models.config');
        $currency = $configModel::getCachedByPath('currency/options/default');
        $locale = $configModel::getCachedByPath('general/locale/code', 'en_US');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($price, $currency);
    }
}

if (!function_exists('webpack_filename_with_chunkhash')) {
    function webpack_filename_with_chunkhash($file)
    {
        $webpackStats = @json_decode(@file_get_contents(public_path('webpack-stats.json')));
        if ($webpackStats) {
            foreach (array_keys((array) $webpackStats->assets) as $filenameWithChunkHash) {
                if (Str::startsWith($filenameWithChunkHash, $file)) {
                    return $filenameWithChunkHash;
                }
            }
        }
    }
}
