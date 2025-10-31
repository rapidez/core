<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class EavAttribute extends Model
{
    // TODO: Maybe create a model for this and cache it?
    // But most likely it's never going to change...
    public const ENTITY_TYPE_CUSTOMER = 1;
    public const ENTITY_TYPE_CUSTOMER_ADDRESS = 2;
    public const ENTITY_TYPE_CATALOG_CATEGORY = 3;
    public const ENTITY_TYPE_CATALOG_PRODUCT = 4;

    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected $casts = [
        'additional_data' => 'json',
    ];

    public static function getCachedMapping()
    {
        return Cache::memo()->rememberForever('eav_attributes_mapping', function () {
            return self::query()
                ->pluck('attribute_code', 'attribute_id')
                ->toArray();
        });
    }

    public static function getCachedCatalog()
    {
        return Cache::memo()->rememberForever('catalog_eav_attributes', function () {
            return self::query()
                ->leftJoin('catalog_eav_attribute', 'eav_attribute.attribute_id', '=', 'catalog_eav_attribute.attribute_id')
                ->where('entity_type_id', self::ENTITY_TYPE_CATALOG_PRODUCT)
                ->get()
                ->keyBy('attribute_id');
        });
    }

    public static function getCachedCustomer()
    {
        return Cache::memo()->rememberForever('customer_eav_attributes', function () {
            return self::query()
                ->leftJoin('customer_eav_attribute', 'customer_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                // TODO: This also needs an entity type check or
                // it should just join instead of left join
                // but not sure if it's used currently?
                ->get()
                ->keyBy('attribute_id');
        });
    }

    public static function getCachedIndexable()
    {
        return Cache::memo()->rememberForever('indexable_eav_attributes', function () {
            return static::getCachedCatalog()->where(fn ($attribute) => $attribute->backend_type === 'static'
                || $attribute->is_used_for_promo_rules
                || $attribute->used_in_product_listing
                || $attribute->used_for_sort_by
                || in_array($attribute->attribute_code, ['status', 'required_options', 'tax_class_id', 'weight']));
        });
    }

    public static function getAttributeCode(int $attributeId): string
    {
        return self::getCachedMapping()[$attributeId];
    }

    public static function getAttributeId(string $attributeCode): int
    {
        return array_search($attributeCode, self::getCachedMapping());
    }
}
