<?php

namespace Rapidez\Core\Models;

class SearchSynonym extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'search_synonyms';

    protected $primaryKey = 'group_id';

    protected $guarded = [];
}
