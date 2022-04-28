<?php

use NumberFormatter;

if (! function_exists('price')) {
    function price($price)
    {
        $configModel = config('rapidez.models.config');
        $currency = $configModel::getCachedByPath('currency/options/default');
        $locale = $configModel::getCachedByPath('general/locale/code', 'en_US');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($price, $currency);
    }
}
