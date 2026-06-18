<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\Searchable;

class SearchQuery extends Model
{
    use Searchable;

    const CREATED_AT = null;

    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    protected static function booting()
    {
        static::addGlobalScope('ForCurrentStore', fn (Builder $query) => $query->where('store_id', config('rapidez.store')));
        static::addGlobalScope(new IsActiveScope);
    }

    protected function makeAllSearchableUsing(Builder $query)
    {
        // Decide on a minimum popularity to make sure we don't index too much.
        // Simply using an orderBy with a limit on the query doesn't work as the limit gets stripped off.
        $minPopularity = Cache::driver('array')->rememberForever(
            'minPopularity',
            fn () => DB::table('search_query')
                ->orderByDesc('popularity')
                ->limit(1)
                ->offset(10000)
                ->first()
                ->popularity ?? 0
        );

        return $query
            ->where(
                fn ($subQuery) => $subQuery
                    ->where(fn ($subSubQuery) => $subSubQuery->where('num_results', '>', 0)->where('popularity', '>', $minPopularity))
                    ->orWhereNotNull('redirect')
            );
    }
}
