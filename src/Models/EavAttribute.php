<?php

namespace Rapidez\Core\Models;

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

    public static function getCachedCatalog()
    {
        return Cache::memo()->rememberForever('catalog_eav_attributes', function () {
            return EavAttribute::query()
                ->leftJoin('catalog_eav_attribute', 'catalog_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                ->where('entity_type_id', self::ENTITY_TYPE_CATALOG_PRODUCT)
                ->get();
        });
    }

    public static function getCachedCustomer()
    {
        return Cache::memo()->rememberForever('customer_eav_attributes', function () {
            return EavAttribute::query()
                ->leftJoin('customer_eav_attribute', 'customer_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                ->where('entity_type_id', self::ENTITY_TYPE_CUSTOMER)
                ->get();
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
}
