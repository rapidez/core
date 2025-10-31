<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class SuperAttribute extends Model
{
    protected $table = 'catalog_product_super_attribute';
    protected $primaryKey = 'product_super_attribute_id';

    protected $appends = ['attribute_code'];

    protected function attribute(): Attribute
    {
        return Attribute::get(function () {
            return EavAttribute::getCachedCatalog()[$this->attribute_id];
        });
    }

    protected function attributeCode(): Attribute
    {
        return Attribute::get(function () {
            return EavAttribute::getAttributeCode($this->attribute_id);
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

    protected function throwMissingAttributeExceptionIfApplicable($key)
    {
        return $this->attribute->$key;
    }
}
