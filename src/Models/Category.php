<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use TorMorten\Eventy\Facades\Eventy;

class Category extends Model
{
    protected $primaryKey = 'entity_id';

    protected $appends = ['url'];

    protected static function booted()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope('defaults', function (Builder $builder) {
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
                'url_path',
            ];

            $builder
                ->addSelect(preg_filter('/^/', $builder->getQuery()->from.'.', $defaultColumnsToSelect))
                ->groupBy($builder->getQuery()->from.'.entity_id');
        });
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_'.config('rapidez.store');
    }

    public function getUrlAttribute(): string
    {
        $configModel = config('rapidez.models.config');

        return '/'.$this->url_path.$configModel::getCachedByPath('catalog/seo/category_url_suffix', '.html');
    }

    public function getSubcategoriesAttribute()
    {
        $categoryIds = explode('/', $this->path);
        $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

        return Category::whereIn('entity_id', $categoryIds)
            ->orderByRaw('FIELD(entity_id,'.implode(',', $categoryIds).')')
            ->get();
    }
}
