<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Rapidez\Core\Facades\Rapidez;

trait HasAlternatesThroughRewrites
{
    protected function alternates(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rewrites = $this->rewrites()
                    ->where('redirect_type', 0)
                    ->whereHas('store', fn ($query) => $query->where('store.group_id', config('rapidez.group')))
                    ->pluck('request_path', 'store_id');

                if (! $rewrites->keys()->contains(fn ($store) => $store !== config('rapidez.store'))) {
                    return collect();
                }

                return $rewrites->mapWithKeys(function ($url, $storeId) {
                    return Rapidez::withStore($storeId, function () use ($url, $storeId) {
                        $locale = str(Rapidez::config('general/locale/code', 'en_US'))->replace('_', '-')->lower()->value();
                        $url = Rapidez::config('web/secure/base_url') . $url;

                        if ($storeId === config('rapidez.system.store')) {
                            return [
                                $locale     => $url,
                                'x-default' => $url,
                            ];
                        }

                        return [$locale => $url];
                    });
                });
            }
        );
    }
}
