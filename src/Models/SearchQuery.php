<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    public $timestamps = true;

    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    protected $guarded = [];

    public function getCreatedAtColumn()
    {
        return null;
    }
}
