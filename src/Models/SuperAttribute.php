<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SuperAttribute extends Model
{
    protected $table = 'catalog_product_super_attribute';
    protected $primaryKey = 'product_super_attribute_id';

    protected $casts = [
        'additional_data' => 'json',
    ];

    protected $visible = [
        'attribute_id',
        'attribute_code',
        'default_value',
        'frontend_input',
        'frontend_label',
        'additional_data',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('attribute', function (Builder $builder) {
            $builder
                ->select($builder->qualifyColumn('*'), 'eav_attribute.*', 'catalog_eav_attribute.*')
                ->selectRaw('COALESCE(eav_attribute_label.value, eav_attribute.frontend_label, eav_attribute.attribute_code) AS frontend_label')
                ->leftJoin('eav_attribute', $builder->qualifyColumn('attribute_id'), '=', 'eav_attribute.attribute_id')
                ->leftJoin('catalog_eav_attribute', $builder->qualifyColumn('attribute_id'), '=', 'catalog_eav_attribute.attribute_id')
                ->leftJoin('eav_attribute_label', function ($join) use ($builder) {
                    $join->on($builder->qualifyColumn('attribute_id'), '=', 'eav_attribute_label.attribute_id')
                        ->where('eav_attribute_label.store_id', config('rapidez.store'));
                });
        });
    }

    /**
     * @deprecated please use attribute_code
     */
    protected function code(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->attribute_code
        );
    }
}
