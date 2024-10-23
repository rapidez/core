<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;

/**
 * @property int $entity_id
 * @property string $path
 * @property string|null $url_path
 */
class Category extends Model
{
    use HasAlternatesThroughRewrites;

    protected $appends = ['url'];

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope('defaults', function (Builder $builder) {
            $defaultColumnsToSelect = $builder->qualifyColumns([
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
            ]);

            $builder
                ->addSelect($defaultColumnsToSelect)
                ->selectRaw('ANY_VALUE(request_path) AS url_path')
                ->leftJoin('url_rewrite', function ($join) use ($builder) {
                    $join->on($builder->qualifyColumn('entity_id'), '=', 'url_rewrite.entity_id')
                        ->where('entity_type', 'category')
                        ->where('url_rewrite.store_id', config('rapidez.store'));
                })
                ->groupBy($builder->qualifyColumn('entity_id'));
        });
    }

    public function getKeyName()
    {
        return $this->getTable() . '.entity_id';
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_' . config('rapidez.store');
    }

    public function getUrlAttribute(): string
    {
        return '/' . ($this->url_path ? $this->url_path : 'catalog/category/view/id/' . $this->entity_id);
    }

    /** @return HasMany<Category, Category> */
    public function subcategories(): HasMany
    {
        return $this->hasMany(config('rapidez.models.category'), 'parent_id', 'entity_id');
    }

    /** @return HasManyThrough<Product, CategoryProduct, Category> */
    public function products(): HasManyThrough
    {
        /** @var CategoryProduct $categoryProductObject */
        $categoryProductObject = new (config('rapidez.models.category_product'));

        return $this
            ->hasManyThrough(
                config('rapidez.models.product'),
                config('rapidez.models.category_product'),
                'category_id',
                'entity_id',
                'entity_id',
                'product_id'
            )
            ->withoutGlobalScopes()
            ->whereIn($categoryProductObject->qualifyColumn('visibility'), [2, 4]);
    }

    /** @return HasMany<Rewrite, Category> */
    public function rewrites(): HasMany
    {
        return $this
            ->hasMany(config('rapidez.models.rewrite'), 'entity_id', 'entity_id')
            ->withoutGlobalScope('store')
            ->where('entity_type', 'category');
    }

    /** @return iterable<int, Category> */
    public function getParentcategoriesAttribute(): iterable
    {
        $categoryIds = explode('/', $this->path);
        $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

        return ! $categoryIds ? [] : Category::whereIn($this->getTable() . '.entity_id', $categoryIds)
            ->orderByRaw('FIELD(' . $this->getTable() . '.entity_id,' . implode(',', $categoryIds) . ')')
            ->get();
    }
}
