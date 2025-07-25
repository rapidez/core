<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\Searchable;
use TorMorten\Eventy\Facades\Eventy;

class Category extends Model
{
    use HasAlternatesThroughRewrites;
    use Searchable;

    protected $primaryKey = 'entity_id';

    protected $casts = [
        self::UPDATED_AT => 'datetime',
        self::CREATED_AT => 'datetime',
    ];

    protected $appends = ['url'];

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
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
                self::UPDATED_AT,
            ];

            $builder
                ->addSelect(preg_filter('/^/', $builder->getQuery()->from . '.', $defaultColumnsToSelect))
                ->selectRaw('ANY_VALUE(request_path) AS url_path')
                ->leftJoin('url_rewrite', function ($join) use ($builder) {
                    $join->on($builder->getQuery()->from . '.entity_id', '=', 'url_rewrite.entity_id')
                        ->where('entity_type', 'category')
                        ->where('url_rewrite.store_id', config('rapidez.store'));
                })
                ->groupBy($builder->getQuery()->from . '.entity_id');
        });
    }

    public function getTable()
    {
        return 'catalog_category_flat_store_' . config('rapidez.store');
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => '/' . ($this->url_path ? $this->url_path : 'catalog/category/view/id/' . $this->entity_id)
        );
    }

    public function subcategories()
    {
        return $this->hasMany(config('rapidez.models.category'), 'parent_id', 'entity_id');
    }

    public function products(): HasManyThrough
    {
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
            ->whereIn((new (config('rapidez.models.category_product')))->qualifyColumn('visibility'), [2, 4]);
    }

    public function rewrites(): HasMany
    {
        return $this
            ->hasMany(config('rapidez.models.rewrite'), 'entity_id', 'entity_id')
            ->withoutGlobalScope('store')
            ->where('entity_type', 'category');
    }

    protected function parentcategories(): Attribute
    {
        return Attribute::make(
            get: function () {
                $categoryIds = explode('/', $this->path);
                $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

                return ! $categoryIds ? collect() : Category::whereIn($this->getQualifiedKeyName(), $categoryIds)
                    ->orderByRaw('FIELD(' . $this->getQualifiedKeyName() . ',' . implode(',', $categoryIds) . ')')
                    ->get();
            }
        )->shouldCache();
    }

    /**
     * {@inheritdoc}
     */
    protected function makeAllSearchableUsing(Builder $query)
    {
        return $query->withEventyGlobalScopes('index.' . static::getModelName() . '.scopes')
            ->select((new (config('rapidez.models.category')))->qualifyColumns(['entity_id', 'name']))
            ->whereNotNull('url_key')
            ->whereNot('url_key', 'default-category')
            ->has('products');
    }

    /**
     * {@inheritdoc}
     */
    public function toSearchableArray(): array
    {
        $data = $this->toArray();
        $data['parents'] = $this->parentcategories->pluck('name')->slice(0, -1)->toArray();

        return Eventy::filter('index.' . static::getModelName() . '.data', $data, $this);
    }

    public static function synonymFields(): array
    {
        return ['name'];
    }
}
