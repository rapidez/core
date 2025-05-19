<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\Searchable;

class SearchQuery extends Model implements Aliased, Explored, IndexSettings
{
    use Searchable;

    const CREATED_AT = null;

    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    protected $guarded = [];

    protected static function booting()
    {
        static::addGlobalScope('ForCurrentStore', fn (Builder $query) => $query->where('store_id', config('rapidez.store')));
        static::addGlobalScope(new IsActiveScope);
    }

    protected function makeAllSearchableUsing(Builder $query)
    {
        return $query
            ->where(fn ($subQuery) => $subQuery
                ->where('num_results', '>', 0)
                ->orWhereNotNull('redirect')
            );
    }
}
