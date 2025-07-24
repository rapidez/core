<?php

namespace Rapidez\Core\Models\Product\Eav;

class EavVarchar extends AbstractEav
{
    use Traits\HasMultiselect;

    protected $table = 'catalog_product_entity_varchar';

    protected $guarded = [];
}
