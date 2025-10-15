<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Facades\Rapidez;

trait HasAlternatesThroughRewrites
{
    abstract protected static function getEntityType(): string;

    public function rewrites(): HasMany
    {
        return $this
            ->hasMany(config('rapidez.models.rewrite'), 'entity_id')
            ->withoutGlobalScope('store')
            ->where('entity_type', static::getEntityType());
    }

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
                    return Rapidez::withStore($storeId, function () use ($url) {
                        $locale = str(Rapidez::config('general/locale/code'))->replace('_', '-')->lower()->value();
                        $url = Rapidez::config('web/secure/base_url') . $url;

                        return [$locale => $url];
                    });
                });
            }
        );
    }
}
