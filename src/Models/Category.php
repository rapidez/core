<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Category extends Model
{
    protected $appends = ['url'];

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope('defaults', function (Builder $builder) {
            $defaultColumnsToSelect = [
                'entity_id',
                'meta_title',
                'meta_description',
                'name',
                'is_anchor',
                'description',
                'is_active',
                'include_in_menu',
                'path',
                'parent_id',
                'children',
                'children_count',
                'position',
            ];

            $builder
                ->addSelect(preg_filter('/^/', $builder->getQuery()->from.'.', $defaultColumnsToSelect))
                ->selectRaw('ANY_VALUE(request_path) AS url_path')
                ->leftJoin('url_rewrite', function ($join) use ($builder) {
                    $join->on($builder->getQuery()->from.'.entity_id', '=', 'url_rewrite.entity_id')
                        ->where('entity_type', 'category')
                        ->where('url_rewrite.store_id', config('rapidez.store'));
                })
                ->groupBy($builder->getQuery()->from.'.entity_id');
        });
    }

    public function getKeyName()
    {
        return $this->getTable().'.entity_id';
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_'.config('rapidez.store');
    }

    public function getUrlAttribute(): string
    {
        return '/'.$this->url_path;
    }

    public function subcategories()
    {
        return $this->hasMany(self::class, 'parent_id', 'entity_id');
    }

    public function getParentcategoriesAttribute()
    {
        $categoryIds = explode('/', $this->path);
        $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

        return !$categoryIds ? [] : Category::whereIn($this->getTable().'.entity_id', $categoryIds)
            ->orderByRaw('FIELD('.$this->getTable().'.entity_id,'.implode(',', $categoryIds).')')
            ->get();
    }
}
