<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Facades\Rapidez;

trait HasAlternatesThroughRewrites
{
    protected function alternates(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->rewrites()
                ->where('redirect_type', 0)
                ->whereHas('store', fn ($query) => $query->where('store.group_id', config('rapidez.group')))
                ->pluck('request_path', 'store_id')
                ->mapWithKeys(function ($url, $storeId) {
                    return Rapidez::withStore($storeId, function () use ($url) {
                        $locale = str(Rapidez::config('general/locale/code'))->replace('_', '-')->lower()->value();
                        $url = Rapidez::config('web/secure/base_url') . $url;

                        return [$locale => $url];
                    });
                })
        );
    }
}
