<?php

namespace Rapidez\Core\Models;

class SearchQuery extends Model
{
    const CREATED_AT = null;

    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    protected $guarded = [];
}
