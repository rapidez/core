<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;

class SearchSynonym extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'search_synonyms';

    protected $primaryKey = 'group_id';

    protected $guarded = [];
}
