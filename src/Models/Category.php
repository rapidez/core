<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use TorMorten\Eventy\Facades\Eventy;

class Category extends Model
{
    protected $primaryKey = 'entity_id';

    protected $appends = ['url'];

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope('defaults', function (Builder $builder){
            $defaultColumnsToSelect = [
                'entity_id',
                'meta_title',
                'name',
                'is_anchor',
                'description',
                'is_active',
                'include_in_menu',
                'path',
                'parent_id',
                'children',
                'position',
                'url_path'
            ];

            $builder
                ->select(preg_filter('/^/', $builder->getQuery()->from.'.', $defaultColumnsToSelect))
                ->groupBy($builder->getQuery()->from . '.entity_id');
        });

        $scopes = Eventy::filter('category.scopes') ?: [];
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope());
        }
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_' . config('rapidez.store');
    }

    public function getUrlAttribute(): string
    {
        return '/' . $this->url_path . Config::getCachedByPath('catalog/seo/category_url_suffix', '.html');
    }
}
