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

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('attribute', function (Builder $builder) {
            $builder
                ->leftJoin('eav_attribute', $builder->qualifyColumn('attribute_id'), '=', 'eav_attribute.attribute_id')
                ->leftJoin('catalog_eav_attribute', $builder->qualifyColumn('attribute_id'), '=', 'catalog_eav_attribute.attribute_id');
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
