<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Scopes\Product\EnabledScope;
use Rapidez\Core\Models\Scopes\Product\ForCurrentWebsiteScope;
use Rapidez\Core\Models\Scopes\Product\SupportedScope;
use Rapidez\Core\Models\Traits\HasAlternatesThroughRewrites;
use Rapidez\Core\Models\Traits\HasCustomAttributes;
use Rapidez\Core\Models\Traits\Product\BackwardsCompatibleAccessors;
use Rapidez\Core\Models\Traits\Product\HasSuperAttributes;
use Rapidez\Core\Models\Traits\Product\Searchable;

class Product extends Model
{
    use BackwardsCompatibleAccessors;
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

    protected $with = [
        'stock',
        'superAttributes',
        'categoryProducts.category',
        'children',
        'options',
        'gallery',
        'prices',
    ];

    protected $appends = [
        'price',
        'special_price',
        'url',

        // TODO: Double check; do we really want all accessors
        // defined here so they will show up in the indexer?
        // See the BackwardsCompatibleAccessors
        'in_stock',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(EnabledScope::class);
        static::addGlobalScope(SupportedScope::class);
        static::addGlobalScope(ForCurrentWebsiteScope::class);
        static::withCustomAttributes();
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
        return $this->belongsToMany(config('rapidez.models.product'), 'catalog_product_relation', 'child_id', 'parent_id');
    }

    public function getParentsAttribute(): Collection
    {
        return $this->getRelationValue('parents')->keyBy('entity_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(config('rapidez.models.product'), 'catalog_product_relation', 'parent_id', 'child_id');
    }

    public function getChildrenAttribute(): Collection
    {
        return $this->getRelationValue('children')->keyBy('entity_id');
    }

    protected function grouped(): Attribute
    {
        return Attribute::get(fn () => $this->children);
    }

    public function productLinks(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_link', ProductLink::class),
            'product_id', 'entity_id',
        );
    }

    public function productLinkParents(): HasMany
    {
        return $this->hasMany(
            config('rapidez.models.product_link', ProductLink::class),
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

    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(
            config('rapidez.models.product_review', Review::class), 'review',
            'entity_pk_value', 'review_id',
            'entity_id', 'review_id',
        )->where('review.entity_id', Review::REVIEW_ENTITY_PRODUCT);
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
            config('rapidez.models.product_tier_price', ProductTierPrice::class),
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

    // Keep in mind this is the base price and
    // the price could be something else
    // based on the customer group
    // or by any tier pricing.
    protected function price(): Attribute
    {
        return Attribute::get(function () {
            $customerGroupId = auth('magento-customer')
                ->user()
                ?->group_id ?: 0;

            $price = $this
                ->prices
                ->firstWhere('customer_group_id', $customerGroupId);

            return $price->price ?: $price->min_price;
        });
    }

    // We're not applying the customer group here so
    // all the prices for customer groups are
    // returned for the indexer.
    public function prices(): hasMany
    {
        // TODO: Double check the prices with the indexer.
        // We should index all customer group prices
        // as we can only verify that on the
        // frontend. But currenlty this
        // is also loading all prices
        // for just a product page.
        return $this
            ->hasMany(
                config('rapidez.models.product_price', ProductPrice::class),
                'entity_id',
                'entity_id'
            );
    }

    protected function specialPrice(): Attribute
    {
        return Attribute::get(function (): ?float {
            $customerGroupId = auth('magento-customer')
                ->user()
                ?->group_id ?: 0;

            // We're using the price index table that
            // handles the special from/to dates.
            $specialPrice = $this
                ->prices
                ->firstWhere('customer_group_id', $customerGroupId)
                ->min_price;

            return $specialPrice < $this->price ? $specialPrice : null;
        });
    }

    protected function breadcrumbCategories(): Attribute
    {
        return Attribute::get(function (): Collection {
            return $this->categoryProducts
                ->where('category_id', '!=', config('rapidez.root_category_id'))
                ->pluck('category')
                ->whereNotNull();
        })->shouldCache();
    }
}
