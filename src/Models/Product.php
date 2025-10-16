<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Scopes\Product\ForCurrentWebsiteScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\HasCustomAttributes;
use Rapidez\Core\Models\Traits\Product\HasSuperAttributes;
use Rapidez\Core\Models\Traits\Product\Searchable;

class Product extends Model
{
    use HasAlternatesThroughRewrites;
    use HasCustomAttributes;
    use HasSuperAttributes;
    use Searchable;

    public const VISIBILITY_NOT_VISIBLE = 1;
    public const VISIBILITY_IN_CATALOG = 2;
    public const VISIBILITY_IN_SEARCH = 3;
    public const VISIBILITY_BOTH = 4;

    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;

    protected $table = 'catalog_product_entity';
    protected $primaryKey = 'entity_id';

    protected $with = ['stock', 'superAttributes', 'categoryProducts.category'];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(ForCurrentWebsiteScope::class);
        static::withCustomAttributes();
        static::addGlobalScope('onlyEnabled', fn (Builder $builder) => $builder->whereAttribute('status', static::STATUS_ENABLED));
    }

    protected function modifyRelation(HasMany $relation): HasMany
    {
        return $relation->leftJoin('catalog_eav_attribute', 'catalog_eav_attribute.attribute_id', '=', $relation->qualifyColumn('attribute_id'));
    }

    protected static function getEntityType(): string
    {
        return 'product';
    }

    public function only($attributes)
    {
        $data = parent::only($attributes);

        if (array_key_exists('children', $data)) {
            $data['children'] = $data['children']->map(fn ($child) => $child->only($attributes));
        }

        return $data;
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

    protected function images(): Attribute
    {
        return Attribute::get(
            fn (): array => $this->gallery->sortBy('productImageValue.position')->pluck('value')->toArray()
        )->shouldCache();
    }

    public function parents(): BelongsToMany
    {
        return $this
            ->belongsToMany(config('rapidez.models.product'), 'catalog_product_super_link', 'product_id', 'parent_id')
            ->using(config('rapidez.models.product_super_link'));
    }

    public function children(): BelongsToMany
    {
        return $this->relations();

        // TODO: Double check? Do we need this one?
        // catalog_product_relation is smaller
        // and only contains configurable
        // and grouped product relations
        return $this
            ->belongsToMany(config('rapidez.models.product'), 'catalog_product_super_link', 'parent_id', 'product_id')
            ->using(config('rapidez.models.product_super_link'));
    }

    public function grouped(): BelongsToMany
    {
        return $this->relations();
    }

    public function relations(): BelongsToMany
    {
        // To query grouped en configurable product
        // parent/child relations fast.
        return $this->belongsToMany(config('rapidez.models.product'), 'catalog_product_relation', 'parent_id', 'child_id');
    }

    public function getChildrenAttribute(): Collection
    {
        return $this->getRelationValue('children')->keyBy('entity_id');
    }

    public function productLinks(): HasMany
    {
        return $this->hasMany(
            ProductLink::class,
            'product_id', 'entity_id',
        );
    }

    public function productLinkParents(): HasMany
    {
        return $this->hasMany(
            ProductLink::class,
            'linked_product_id', 'entity_id',
        );
    }

    public function getLinkedProducts(string $type): Collection
    {
        return $this->productLinks()
            ->with('linkedProduct')
            ->where('code', $type)
            ->get()
            ->pluck('linkedProduct');
    }

    public function getLinkedParents(string $type): Collection
    {
        return $this->productLinkParents()
            ->with('linkedParent')
            ->where('code', $type)
            ->get()
            ->pluck('linkedParent');
    }

    public function categoryProducts(): HasMany
    {
        return $this
            ->hasMany(
                config('rapidez.models.category_product'),
                'product_id',
            )
            ->whereHas('category');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(
            config('rapidez.models.product_stock'),
            'entity_id',
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

    public function reviewSummary(): HasOne
    {
        return $this->hasOne(
            config('rapidez.models.product_review_summary'),
            'entity_pk_value'
        );
    }

    public function views(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_view'),
            'product_id',
        );
    }

    public function tierPrices(): HasMany
    {
        return $this->hasMany(
            ProductTierPrice::class,
            'entity_id',
            'entity_id'
        )->whereIn('website_id', [0, config('rapidez.website')]);
    }

    public function getUnitPrice(int $quantity = 1, int $customerGroup = 0)
    {
        $tierPrice = $this->tierPrices()
            ->where(function ($query) use ($customerGroup) {
                $query->where('customer_group_id', $customerGroup)
                    ->orWhere('all_groups', '1');
            })
            ->where('qty', '<=', $quantity)
            ->orderBy('value')
            ->first()?->value ?? null;
        // NOTE: We always need the option with the lowest matching value, *not* the one with the highest matching qty!
        // It wouldn't make sense to select a tier with a higher qty if the price is higher.

        return $tierPrice ?? $this->price;
    }

    public function getPrice(int $quantity = 1, int $customerGroup = 0)
    {
        return $this->getUnitPrice($quantity, $customerGroup) * $quantity;
    }

    private function getImageFrom(?string $image): ?string
    {
        return $image !== 'no_selection' ? $image : null;
    }

    protected function image(): Attribute
    {
        return Attribute::get($this->getImageFrom(...));
    }

    protected function smallImage(): Attribute
    {
        return Attribute::get($this->getImageFrom(...));
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::get($this->getImageFrom(...));
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => '/' . ($this->url_key ? $this->url_key . Rapidez::config('catalog/seo/product_url_suffix') : 'catalog/product/view/id/' . $this->entity_id)
        );
    }

    protected function price(): Attribute
    {
        return Attribute::get(function () {
            if (in_array($this->type_id, ['configurable', 'grouped'])) {
                return $this->prices->min();
            }

            return $this->getCustomAttributeValue('price');
        });
    }

    protected function prices(): Attribute
    {
        return Attribute::get(function (): ?Collection {
            if (! in_array($this->type_id, ['configurable', 'grouped'])) {
                return null;
            }

            return collect($this->type_id == 'configurable' ? $this->children : $this->grouped)->pluck('price');
        })->shouldCache();
    }

    protected function specialPrice(): Attribute
    {
        return Attribute::get(function (?float $specialPrice): ?float {
            if (! in_array($this->type_id, ['configurable', 'grouped'])) {
                if (! now()->isBetween(
                    $this->special_from_date ?? now()->subHour(),
                    $this->special_to_date ?? now()->addHour(),
                )) {
                    return null;
                }

                return $specialPrice < $this->price ? $specialPrice : null;
            }

            return collect($this->type_id == 'configurable' ? $this->children : $this->grouped)
                ->filter(fn ($child) => $child->specialPrice !== null)
                ->min->special_price;
        });
    }

    protected function minSaleQty(): Attribute
    {
        return Attribute::get(function (): ?float {
            $increments = $this->stock->qty_increments ?: 1;
            $minSaleQty = $this->stock->min_sale_qty ?: 1;

            return ($minSaleQty - fmod($minSaleQty, $increments)) ?: $increments;
        });
    }

    protected function maxSaleQty(): Attribute
    {
        return Attribute::get(fn () => $this->stock->max_sale_qty);
    }

    protected function qtyIncrements(): Attribute
    {
        return Attribute::get(fn () => $this->stock->qty_increments);
    }

    protected function breadcrumbCategories(): Attribute
    {
        return Attribute::get(function (): Collection {
            return $this->categoryProducts
                ->where('category_id', '!=', config('rapidez.root_category_id'))
                ->pluck('category');
        })->shouldCache();
    }

    protected function inStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock->is_in_stock);
    }
}
