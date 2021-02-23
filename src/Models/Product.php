<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NumberFormatter;
use Rapidez\Core\Casts\DecodeHtmlEntities;
use Rapidez\Core\Casts\ProductAttributeCast;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\Product\WithProductAttributesScope;
use Rapidez\Core\Models\Scopes\Product\WithProductCategoryIdsScope;
use Rapidez\Core\Models\Scopes\Product\WithProductChildrenScope;
use Rapidez\Core\Models\Scopes\Product\WithProductEssentialAttributesScope;
use Rapidez\Core\Models\Scopes\Product\WithProductSuperAttributesScope;
use Rapidez\Core\Models\Traits\Product\CastMultiselectAttributes;
use Rapidez\Core\Models\Traits\Product\CastSuperAttributes;
use Rapidez\Core\Models\Traits\Product\SelectAttributeScopes;
use TorMorten\Eventy\Facades\Eventy;

class Product extends Model
{
    protected $table = 'catalog_product_entity';

    protected $primaryKey = 'entity_id';

    protected $appends = ['formatted_price'];

    protected $with = ['attrs'];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('defaults', function (Builder $builder) {
            $from = $builder->getQuery()->from;
            $builder
                ->select([$from.'.entity_id', $from.'.sku'])
                ->whereNotIn($from.'.type_id', ['grouped', 'bundle'])
                ->groupBy($from.'.entity_id');
        });
        static::addGlobalScope(new WithProductCategoryIdsScope);
        static::addGlobalScope(new WithProductEssentialAttributesScope);

        // TODO: Implement these 2:
        // static::addGlobalScope(new WithProductSuperAttributesScope);
        // static::addGlobalScope(new WithProductChildrenScope);

        $scopes = Eventy::filter('product.scopes') ?: [];
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope);
        }
    }

    public function getCasts(): array
    {
        return array_merge(
            [
                'name' => DecodeHtmlEntities::class,
                'children' => 'object',
            ],
            // $this->getSuperAttributeCasts(),
            // $this->getMultiselectAttributeCasts(),
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
            'entity_id',
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

    public function scopeByIds(Builder $query, array $productIds): Builder
    {
        return $query->whereIn($this->getTable().'.entity_id', $productIds);
    }

    public function getAttribute($key)
    {
        if ($value = parent::getAttribute($key)) {
            return $value;
        }

        // TOOD: Not sure if this is very efficient, first we're
        // searching for the attribute by code for the id and
        // after that we're searching for the attribute id
        // between the product attributes for the value.
        $attribute = Attribute::getCachedWhereFirst(function ($attribute) use ($key) {
            return $attribute['code'] == $key;
        });

        if (!$attribute) {
            return null;
        }

        // TODO: Check for a custom value for a store. So if store 1 overwrites store 0.
        if (!$value = optional($this->attrs->firstWhere('attribute_id', $attribute['id']))->value) {
            return null;
        }

        if ($attribute['input'] == 'multiselect') {
            foreach (explode(',', $value) as $optionValueId) {
                $values[] = OptionValue::getCachedByOptionId($optionValueId);
            }
            return $values;
        }

        if ($attribute['input'] == 'select' && $attribute['type'] == 'int' && !$attribute['system']) {
            return OptionValue::getCachedByOptionId($value);
        }

        if ($key == 'url_key') {
            return '/' . $value . Config::getCachedByPath('catalog/seo/product_url_suffix', '.html');
        }

        return $value;
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
