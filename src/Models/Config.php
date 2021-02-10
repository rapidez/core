<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Config extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core_config_data';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'config_id';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

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
