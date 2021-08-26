<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use TorMorten\Eventy\Facades\Eventy;

class Config extends Model
{
    protected $table = 'core_config_data';

    protected $primaryKey = 'config_id';

    protected static function booted()
    {
        static::addGlobalScope('scope-fallback', function (Builder $builder) {
            $builder
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('scope', 'stores')->where('scope_id', config('rapidez.store'));
                    })->orWhere(function ($query) {
                        $query->where('scope', 'websites')->where('scope_id', config('rapidez.website'));
                    })->orWhere(function ($query) {
                        $query->where('scope', 'default')->where('scope_id', 0);
                    });
                })
                ->orderByRaw('FIELD(scope, "stores", "websites", "default") ASC')
                ->limit(1);
        });
    }

    public static function getCachedByPath(string $path, $default = null): ?string
    {
        $cacheKey = 'config.'.config('rapidez.store').'.'.str_replace('/', '.', $path);

        return Cache::rememberForever($cacheKey, function () use ($path, $default) {
            return ($config = self::where('path', $path)->first('value')) ? $config->value : $default;
        });
    }
}
