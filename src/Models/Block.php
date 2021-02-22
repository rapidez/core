<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\HasContentAttributeWithVariables;

class Block extends Model
{
    use HasContentAttributeWithVariables;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_block';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'block_id';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope(new ForCurrentStoreScope);
    }

    public static function getCachedByIdentifier(string $identifier): ?string
    {
        $cacheKey = 'block.'.config('rapidez.store').'.'.$identifier;

        return Cache::rememberForever($cacheKey, function () use ($identifier) {
            return optional(self::where('identifier', $identifier)->first('content'))->content;
        });
    }
}
