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

    protected $table = 'cms_block';

    protected $primaryKey = 'block_id';

    protected static function booted()
    {
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
