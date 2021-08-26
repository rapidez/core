<?php

namespace Rapidez\Core\Models;

use TorMorten\Eventy\Facades\Eventy;

class ProductImage extends Model
{
    protected $table = 'catalog_product_entity_media_gallery';

    protected $primaryKey = 'value_id';
}
