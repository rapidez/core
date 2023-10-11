<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    public $timestamps = false;

    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    protected $fillable = [
        'query_text',
        'store_id',
    ];
}
