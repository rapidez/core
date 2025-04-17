<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class SearchQuery extends Model
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
            ->where('popularity', '>', 10);
    }
}
