<?php

namespace Rapidez\Core\Models\Product\Eav;

class EavText extends AbstractEav
{
    use Traits\HasMultiselect;

    protected $table = 'catalog_product_entity_text';

    protected $guarded = [];
}
