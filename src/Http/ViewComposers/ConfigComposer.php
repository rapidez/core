<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\Support\Arr;
use Illuminate\View\View;
use Rapidez\Core\Models\Attribute;
use Rapidez\Core\Models\Config;

class ConfigComposer
{
    public function compose(View $view)
    {
        $exposedFrontendConfigValues = Arr::only(
            config('rapidez'),
            array_merge(config('rapidez.exposed'), ['store_code'])
        );

        config(['frontend' => array_merge(
            config('frontend') ?: [],
            $exposedFrontendConfigValues
        )]);

        config(['frontend.locale' => Config::getCachedByPath('general/locale/code', 'en_US')]);
        config(['frontend.currency' => Config::getCachedByPath('currency/options/default')]);

        config(['frontend.searchable' => Arr::pluck(Attribute::getCachedWhere(function ($attribute) {
            return $attribute['search'];
        }), 'code')]);
    }
}
