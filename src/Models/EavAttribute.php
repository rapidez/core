<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Enums\EntityType;

class EavAttribute extends Model
{
    protected $table = 'eav_attribute';

    protected $primaryKey = 'attribute_id';

    protected static function booted(): void
    {
        static::addGlobalScope('scoped_label', function ($builder) {
            $builder
                ->addSelect(['frontend_label' => fn (Builder $q) => $q->selectRaw('COALESCE(eav_attribute_label.value, frontend_label, eav_attribute.attribute_code)')])
                ->leftJoin('eav_attribute_label', function ($join) {
                    $join->on('eav_attribute.attribute_id', '=', 'eav_attribute_label.attribute_id')
                        ->where('eav_attribute_label.store_id', config('rapidez.store'));
                });
        });
    }

    public static function getCachedCatalog()
    {
        return Cache::memo()->rememberForever('catalog_eav_attributes', function () {
            return EavAttribute::query()
                ->select('*')
                ->leftJoin('catalog_eav_attribute', 'catalog_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                ->where('entity_type_id', EntityType::Product->value)
                ->orderBy('position')
                ->orderBy('eav_attribute.attribute_id')
                ->get();
        });
    }

    public static function getCachedCustomer()
    {
        return Cache::memo()->rememberForever('customer_eav_attributes', function () {
            return EavAttribute::query()
                ->select('*')
                ->leftJoin('customer_eav_attribute', 'customer_eav_attribute.attribute_id', '=', 'eav_attribute.attribute_id')
                ->where('entity_type_id', EntityType::Customer->value)
                ->orderBy('sort_order')
                ->orderBy('eav_attribute.attribute_id')
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
