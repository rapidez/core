<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Page extends Model
{
    protected $table = 'cms_page';

    protected $primaryKey = 'page_id';

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope(new ForCurrentStoreScope());
    }
}
