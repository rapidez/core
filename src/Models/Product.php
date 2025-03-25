<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Rapidez\Core\Casts\Children;
use Rapidez\Core\Casts\CommaSeparatedToArray;
use Rapidez\Core\Casts\CommaSeparatedToIntegerArray;
use Rapidez\Core\Casts\DecodeHtmlEntities;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Scopes\Product\WithProductAttributesScope;
use Rapidez\Core\Models\Scopes\Product\WithProductCategoryInfoScope;
use Rapidez\Core\Models\Scopes\Product\WithProductChildrenScope;
use Rapidez\Core\Models\Scopes\Product\WithProductGroupedScope;
use Rapidez\Core\Models\Scopes\Product\WithProductRelationIdsScope;
use Rapidez\Core\Models\Scopes\Product\WithProductStockScope;
use Rapidez\Core\Models\Scopes\Product\WithProductSuperAttributesScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\Product\CastMultiselectAttributes;
use Rapidez\Core\Models\Traits\Product\CastSuperAttributes;
use Rapidez\Core\Models\Traits\Product\SelectAttributeScopes;
use TorMorten\Eventy\Facades\Eventy;

class Product extends Model
{
    use CastMultiselectAttributes;
    use CastSuperAttributes;
    use HasAlternatesThroughRewrites;
    use SelectAttributeScopes;

    public array $attributesToSelect = [];

    protected $primaryKey = 'entity_id';

    protected $casts = [
        self::UPDATED_AT => 'datetime',
        self::CREATED_AT => 'datetime',
    ];

    protected $appends = ['url'];

    protected static function booting(): void
    {
        static::addGlobalScope(new WithProductAttributesScope);
        static::addGlobalScope(new WithProductSuperAttributesScope);
        static::addGlobalScope(new WithProductStockScope);
        static::addGlobalScope(new WithProductCategoryInfoScope);
        static::addGlobalScope(new WithProductRelationIdsScope);
        static::addGlobalScope(new WithProductChildrenScope);
        static::addGlobalScope(new WithProductGroupedScope);
        static::addGlobalScope('defaults', function (Builder $builder) {
            $builder
                ->whereNotIn($builder->getQuery()->from . '.type_id', ['bundle'])
                ->groupBy($builder->getQuery()->from . '.entity_id');
        });
    }

    public function getTable(): string
    {
        return 'catalog_product_flat_' . config('rapidez.store');
    }

    public function getCasts(): array
    {
        if (! isset($this->casts['name'])) {
            $this->casts = array_merge(
                parent::getCasts(),
                [
                    'name'           => DecodeHtmlEntities::class,
                    'category_ids'   => CommaSeparatedToIntegerArray::class,
                    'category_paths' => CommaSeparatedToArray::class,
                    'relation_ids'   => CommaSeparatedToIntegerArray::class,
                    'upsell_ids'     => CommaSeparatedToIntegerArray::class,
                    'children'       => Children::class,
                    'grouped'        => Children::class,
                    'qty_increments' => 'int',
                    'min_sale_qty'   => 'int',
                    'max_sale_qty'   => 'int',
                ],
                $this->getSuperAttributeCasts(),
                $this->getMultiselectAttributeCasts(),
                Eventy::filter('product.casts', []),
            );
        }

        return $this->casts;
    }

    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(
            config('rapidez.models.product_image'),
            'catalog_product_entity_media_gallery_value_to_entity',
            'entity_id',
            'value_id',
        );
    }

    public function views(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_view'),
            'product_id',
        );
    }

    public function options(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_option'),
            'product_id',
        );
    }

    public function categoryProducts(): HasMany
    {
        return $this
            ->hasMany(
                config('rapidez.models.category_product'),
                'product_id',
            );
    }

    public function reviewSummary(): HasOne
    {
        return $this->hasOne(
            config('rapidez.models.product_review_summary', \Rapidez\Core\Models\ProductReviewSummary::class),
            'entity_pk_value'
        );
    }

    public function rewrites(): HasMany
    {
        return $this
            ->hasMany(config('rapidez.models.rewrite'), 'entity_id')
            ->withoutGlobalScope('store')
            ->where('entity_type', 'product');
    }

    public function parent(): HasOneThrough
    {
        return $this->hasOneThrough(
            config('rapidez.models.product'),
            config('rapidez.models.product_link'),
            'product_id', 'entity_id',
            'entity_id', 'parent_id'
        )->withoutGlobalScopes();
    }

    public function scopeByIds(Builder $query, array $productIds): Builder
    {
        return $query->whereIn($this->getQualifiedKeyName(), $productIds);
    }

    public function getPriceAttribute($price)
    {
        if ($this->type_id == 'configurable') {
            return collect($this->children)->min->price;
        }

        if ($this->type_id == 'grouped') {
            return collect($this->grouped)->min->price;
        }

        return $price;
    }

    public function getSpecialPriceAttribute($specialPrice)
    {
        if (! in_array($this->type_id, ['configurable', 'grouped'])) {
            if ($this->special_from_date && $this->special_from_date > now()->toDateTimeString()) {
                return null;
            }

            if ($this->special_to_date && $this->special_to_date < now()->toDateTimeString()) {
                return null;
            }

            return $specialPrice < $this->price ? $specialPrice : null;
        }

        return collect($this->type_id == 'configurable' ? $this->children : $this->grouped)->filter(function ($child) {
            if (! $child->special_price) {
                return false;
            }

            if (isset($child->special_from_date) && $child->special_from_date > now()->toDateTimeString()) {
                return false;
            }

            if (isset($child->special_to_date) && $child->special_to_date < now()->toDateTimeString()) {
                return false;
            }

            return true;
        })->min->special_price;
    }

    public function getUrlAttribute(): string
    {
        return '/' . ($this->url_key ? $this->url_key . Rapidez::config('catalog/seo/product_url_suffix') : 'catalog/product/view/id/' . $this->entity_id);
    }

    public function getImagesAttribute(): array
    {
        return $this->gallery->sortBy('productImageValue.position')->pluck('value')->toArray();
    }

    public function getImageAttribute($image): ?string
    {
        return $image !== 'no_selection' ? $image : null;
    }

    public function getSmallImageAttribute($image): ?string
    {
        return $this->getImageAttribute($image);
    }

    public function getThumbnailAttribute($image): ?string
    {
        return $this->getImageAttribute($image);
    }

    protected function breadcrumbCategories(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $path = session('latest_category_path')) {
                    return [];
                }

                $categoryIds = explode('/', $path);
                $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

                if (! in_array(end($categoryIds), $this->category_ids)) {
                    return [];
                }

                $categoryModel = config('rapidez.models.category');
                $categoryTable = (new $categoryModel)->getTable();

                return Category::whereIn($categoryTable . '.entity_id', $categoryIds)
                    ->orderByRaw('FIELD(' . $categoryTable . '.entity_id,' . implode(',', $categoryIds) . ')')
                    ->get();
            },
        )->shouldCache();
    }

    public static function exist($productId): bool
    {
        return self::withoutGlobalScopes()->where('entity_id', $productId)->exists();
    }
}
