<?php

namespace Rapidez\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

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
