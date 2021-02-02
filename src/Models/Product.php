<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NumberFormatter;
use Rapidez\Core\Casts\DecodeHtmlEntities;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\Product\WithProductAttributesScope;
use Rapidez\Core\Models\Scopes\Product\WithProductCategoryIdsScope;
use Rapidez\Core\Models\Scopes\Product\WithProductChildrenScope;
use Rapidez\Core\Models\Scopes\Product\WithProductSuperAttributesScope;
use Rapidez\Core\Models\Traits\Product\CastMultiselectAttributes;
use Rapidez\Core\Models\Traits\Product\CastSuperAttributes;
use Rapidez\Core\Models\Traits\Product\SelectAttributeScopes;
use TorMorten\Eventy\Facades\Eventy;

class Product extends Model
{
    use CastSuperAttributes, CastMultiselectAttributes, SelectAttributeScopes;

    public array $attributesToSelect = [];

    protected $primaryKey = 'entity_id';

    protected $appends = ['formatted_price', 'url'];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new WithProductAttributesScope);
        static::addGlobalScope(new WithProductSuperAttributesScope);
        static::addGlobalScope(new WithProductCategoryIdsScope);
        static::addGlobalScope(new WithProductChildrenScope);
        static::addGlobalScope('defaults', function (Builder $builder) {
            $builder
                ->whereNotIn($builder->getQuery()->from.'.type_id', ['grouped', 'bundle'])
                ->groupBy($builder->getQuery()->from.'.entity_id');
        });

        $scopes = Eventy::filter('product.scopes') ?: [];
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope);
        }
    }

    public function getTable(): string
    {
        return 'catalog_product_flat_' . config('rapidez.store');
    }

    public function getCasts(): array
    {
        return array_merge(
            parent::getCasts(),
            [
                'name' => DecodeHtmlEntities::class,
                'children' => 'object',
            ],
            $this->getSuperAttributeCasts(),
            $this->getMultiselectAttributeCasts(),
            Eventy::filter('product.casts') ?: [],
        );
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductImage::class,
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

    public function getCategoryIdsAttribute(string $value): array
    {
        return explode(',', $value);
    }

    public function getFormattedPriceAttribute(): string
    {
        $currency  = Config::getCachedByPath('currency/options/default');
        $locale    = Config::getCachedByPath('general/locale/code', 'en_US');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->price, $currency);
    }

    public function getUrlAttribute(): string
    {
        return '/' . $this->url_key . Config::getCachedByPath('catalog/seo/product_url_suffix', '.html');
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
