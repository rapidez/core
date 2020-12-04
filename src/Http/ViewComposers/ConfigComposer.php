<?php

namespace Rapidez\Core\Http\ViewComposers;

use Illuminate\View\View;

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
