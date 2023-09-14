<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Block extends Model
{
    protected $table = 'cms_block';

    protected $primaryKey = 'block_id';

    public static function getCachedByIdentifier(string $identifier, array $replace = []): ?string
    {
        $cacheKey = 'block.' . config('rapidez.store') . '.' . $identifier;

        $block = Cache::rememberForever($cacheKey, function () use ($identifier) {
            return optional(self::where('identifier', $identifier)->first('content'))->content ?: false;
        });

        return empty($replace) ? $block : strtr($block, $replace);
    }

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope(new ForCurrentStoreScope);
    }
}
