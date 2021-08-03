<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\HasContentAttributeWithVariables;

class Page extends Model
{
    use HasContentAttributeWithVariables;

    protected $table = 'cms_page';

    protected $primaryKey = 'page_id';

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope(new ForCurrentStoreScope());
    }
}
