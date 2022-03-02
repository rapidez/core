<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NumberFormatter;
use Rapidez\Core\Casts\Children;
use Rapidez\Core\Casts\CommaSeparatedToArray;
use Rapidez\Core\Casts\DecodeHtmlEntities;
use Rapidez\Core\Models\Scopes\Product\WithProductAttributesScope;
use Rapidez\Core\Models\Scopes\Product\WithProductCategoryIdsScope;
use Rapidez\Core\Models\Scopes\Product\WithProductChildrenScope;
use Rapidez\Core\Models\Scopes\Product\WithProductRelationIdsScope;
use Rapidez\Core\Models\Scopes\Product\WithProductStockScope;
use Rapidez\Core\Models\Scopes\Product\WithProductSuperAttributesScope;
use Rapidez\Core\Models\Traits\Product\CastMultiselectAttributes;
use Rapidez\Core\Models\Traits\Product\CastSuperAttributes;
use Rapidez\Core\Models\Traits\Product\SelectAttributeScopes;
use TorMorten\Eventy\Facades\Eventy;

class Product extends Model
{
    use CastSuperAttributes;
    use CastMultiselectAttributes;
    use SelectAttributeScopes;

    public array $attributesToSelect = [];

    protected $primaryKey = 'entity_id';

    protected $appends = ['formatted_price', 'url'];

    protected static function booting(): void
    {
        static::addGlobalScope(new WithProductAttributesScope());
        static::addGlobalScope(new WithProductSuperAttributesScope());
        static::addGlobalScope(new WithProductStockScope());
        static::addGlobalScope(new WithProductCategoryIdsScope());
        static::addGlobalScope(new WithProductRelationIdsScope());
        static::addGlobalScope(new WithProductChildrenScope());
        static::addGlobalScope('defaults', function (Builder $builder) {
            $builder
                ->whereNotIn($builder->getQuery()->from.'.type_id', ['grouped', 'bundle'])
                ->groupBy($builder->getQuery()->from.'.entity_id');
        });
    }

    public function getTable(): string
    {
        return 'catalog_product_flat_'.config('rapidez.store');
    }

    public function getCasts(): array
    {
        if (!$this->casts) {
            $this->casts = array_merge(
                parent::getCasts(),
                [
                    'name'           => DecodeHtmlEntities::class,
                    'category_ids'   => CommaSeparatedToArray::class,
                    'relation_ids'   => CommaSeparatedToArray::class,
                    'upsell_ids'     => CommaSeparatedToArray::class,
                    'children'       => Children::class,
                    'qty_increments' => 'int',
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
            config('rapidez.models.productimage'),
            'catalog_product_entity_media_gallery_value_to_entity',
            'entity_id',
            'value_id',
            'id'
        );
    }

    public function scopeByIds(Builder $query, array $productIds): Builder
    {
        return $query->whereIn($this->getTable().'.entity_id', $productIds);
    }

    public function getPriceAttribute($price)
    {
        if (!(array) $this->children) {
            return $price;
        }

        return collect($this->children)->min->price;
    }

    public function getSpecialPriceAttribute($specialPrice)
    {
        if (!(array) $this->children) {
            if ($this->special_from_date && $this->special_from_date > now()->toDateString()) {
                return null;
            }

            if ($this->special_to_date && $this->special_to_date < now()->toDateString()) {
                return null;
            }

            return $specialPrice !== $this->price ? $specialPrice : null;
        }

        return collect($this->children)->filter(function ($child) {
            if (!$child->special_price) {
                return false;
            }

            if ($child->special_from_date && $child->special_from_date > now()->toDateString()) {
                return false;
            }

            if ($child->special_to_date && $child->special_to_date < now()->toDateString()) {
                return false;
            }

            return true;
        })->min->special_price;
    }

    public function getFormattedPriceAttribute(): string
    {
        $configModel = config('rapidez.models.config');
        $currency = $configModel::getCachedByPath('currency/options/default');
        $locale = $configModel::getCachedByPath('general/locale/code', 'en_US');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->price, $currency);
    }

    public function getUrlAttribute(): string
    {
        $configModel = config('rapidez.models.config');

        return '/'.$this->url_key.$configModel::getCachedByPath('catalog/seo/product_url_suffix', '.html');
    }

    public function getBreadcrumbCategoriesAttribute()
    {
        if (!$path = session('latest_category_path')) {
            return [];
        }

        $categoryIds = explode('/', $path);
        $categoryIds = array_slice($categoryIds, array_search(config('rapidez.root_category_id'), $categoryIds) + 1);

        if (!in_array(end($categoryIds), $this->category_ids)) {
            return [];
        }

        return Category::whereIn('entity_id', $categoryIds)
            ->orderByRaw('FIELD(entity_id,'.implode(',', $categoryIds).')')
            ->get();
    }

    public function getImagesAttribute(): array
    {
        return $this->gallery->pluck('value')->toArray();
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

    public static function exist($productId): bool
    {
        return self::withoutGlobalScopes()->where('entity_id', $productId)->exists();
    }
}
