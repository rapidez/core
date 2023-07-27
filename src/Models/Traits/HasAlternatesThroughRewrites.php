<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Facades\Rapidez;
use TorMorten\Eventy\Facades\Eventy;

trait HasAlternatesThroughRewrites
{
    protected function alternates(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->rewrites()
                ->where('redirect_type', 0)
                ->whereHas('store', fn ($query) => $query->where('store.group_id', config('rapidez.group')))
                ->whereNot('store_id', config('rapidez.store'))
                ->pluck('request_path', 'store_id')
                ->mapWithKeys(function ($url, $storeId) {
                    return Rapidez::withStore($storeId, function () use ($url) {
                        $locale = str(Rapidez::config('general/locale/code', 'en_US'))->replace('_', '-')->lower()->value();
                        $url = Rapidez::config('web/secure/base_url').$url;
                        return [$locale => $url];
                    });
                })
        );
    }
}
