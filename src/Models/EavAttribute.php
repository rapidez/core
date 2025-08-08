<?php

namespace Rapidez\Core\Models;

use Illuminate\Support\Facades\Cache;

class EavAttribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    public static function getCachedCatalog()
    {
        return Cache::rememberForever('catalog_eav_attributes', function () {
            return EavAttribute::query()
                ->leftJoin('catalog_eav_attribute', 'catalog_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                ->get();
        });
    }

    public static function getCachedCustomer()
    {
        return Cache::rememberForever('customer_eav_attributes', function () {
            return EavAttribute::query()
                ->leftJoin('customer_eav_attribute', 'customer_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                ->get();
        });
    }

    public static function getCachedIndexable()
    {
        return static::getCachedCatalog()->where(fn ($attribute) => $attribute->backend_type === 'static'
            || $attribute->is_used_for_promo_rules
            || $attribute->used_in_product_listing
            || $attribute->used_for_sort_by
            || in_array($attribute->attribute_code, ['status', 'required_options', 'tax_class_id', 'weight'])
        );
    }
}
