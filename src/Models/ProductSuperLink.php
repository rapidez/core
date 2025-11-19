<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSuperLink extends Pivot
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'catalog_product_super_link';

    protected $primaryKey = 'link_id';
}
