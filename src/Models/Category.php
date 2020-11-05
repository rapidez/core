<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Category extends Model
{
    protected $primaryKey = 'entity_id';

    protected $appends = ['url'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsActiveScope);
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_' . config('shop.store');
    }

    public function getUrlAttribute(): string
    {
        return '/' . $this->url_path . Config::getCachedByPath('catalog/seo/category_url_suffix', '.html');
    }
}
