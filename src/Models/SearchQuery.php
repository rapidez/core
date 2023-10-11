<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    protected $guarded = [];
    
    const CREATED_AT = null;
}
