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
use Rapidez\Core\Models\Traits\Product\Searchable;
use Rapidez\Core\Models\Traits\Product\SelectAttributeScopes;
use TorMorten\Eventy\Facades\Eventy;

class Product extends Model
{
    use CastMultiselectAttributes;
    use CastSuperAttributes;
    use HasAlternatesThroughRewrites;
    use Searchable;
    use SelectAttributeScopes;

    public const VISIBILITY_NOT_VISIBLE = 1;
    public const VISIBILITY_IN_CATALOG = 2;
    public const VISIBILITY_IN_SEARCH = 3;
    public const VISIBILITY_BOTH = 4;

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
                    'qty_increments' => 'float',
                    'min_sale_qty'   => 'float',
                    'max_sale_qty'   => 'float',
                ],
                $this->getSuperAttributeCasts(),
                $this->getMultiselectAttributeCasts(),
                Eventy::filter(static::getModelName() . '.casts', []),
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

    protected function price(): Attribute
    {
        return Attribute::make(
            get: function (?float $price): ?float {
                if ($this->type_id == 'configurable') {
                    return collect($this->children)->min->price;
                }

                if ($this->type_id == 'grouped') {
                    return collect($this->grouped)->min->price;
                }

                return $price;
            }
        );
    }

    protected function specialPrice(): Attribute
    {
        return Attribute::make(
            get: function (?float $specialPrice): ?float {
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
        );
    }

    protected function minSaleQty(): Attribute
    {
        return Attribute::make(
            get: function (?float $minSaleQty): ?float {
                $increments = $this->qty_increments ?: 1;

                return ($minSaleQty - fmod($minSaleQty, $increments)) ?: $increments;
            }
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/' . ($this->url_key ? $this->url_key . Rapidez::config('catalog/seo/product_url_suffix') : 'catalog/product/view/id/' . $this->entity_id)
        );
    }

    protected function images(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->gallery->sortBy('productImageValue.position')->pluck('value')->toArray()
        )->shouldCache();
    }

    private function getImageFrom(?string $image): ?string
    {
        return $image !== 'no_selection' ? $image : null;
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: $this->getImageFrom(...)
        );
    }

    protected function smallImage(): Attribute
    {
        return Attribute::make(
            get: $this->getImageFrom(...)
        );
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: $this->getImageFrom(...)
        );
    }

    public function attrs(): HasMany
    {
        return $this->hasMany(
            ProductAttribute::class,
            'entity_id',
            'entity_id',
        );
    }

    public function getAttribute($key)
    {
        if (($value = parent::getAttribute($key)) !== null || $this->hasAttribute($key)) {
            return $value;
        }

        // TOOD: Not sure if this is very efficient, first we're
        // searching for the attribute by code for the id and
        // after that we're searching for the attribute id
        // between the product attributes for the value.
        $attributeModel = config('rapidez.models.attribute');
        $attributes = $attributeModel::getCachedWhere(function ($attribute) use ($key) {
            return $attribute['code'] == $key;
        });

        if (! count($attributes) || ! $attribute = reset($attributes)) {
            return null;
        }

        $this->loadMissing('attrs');
        // TODO: Check for a custom value for a store. So if store 1 overwrites store 0.
        if (! $value = optional($this->attrs->firstWhere('attribute_id', $attribute['id']))->value) {
            return null;
        }

        if ($attribute['input'] == 'multiselect') {
            foreach (explode(',', $value) as $optionValueId) {
                $values[] = OptionValue::getCachedByOptionId($optionValueId, $attribute['id'], $optionValueId);
            }
            $this->setAttribute($key, $values);

            return $values;
        }

        if ($attribute['input'] == 'select' && $attribute['type'] == 'int' && ! ($attribute['system'] ?? false)) {
            $value = OptionValue::getCachedByOptionId($value, $attribute['id'], $value);
        }

        if ($key == 'url_key') {
            return '/' . ($value ? $value . Rapidez::config('catalog/seo/product_url_suffix') : 'catalog/product/view/id/' . $this->entity_id);
        }

        $this->setAttribute($key, $value);

        return $value;
    }

    protected function breadcrumbCategories(): Attribute
    {
        return Attribute::make(
            get: function (): iterable {
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
