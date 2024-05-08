<?php

namespace Rapidez\Core\Models;

class ProductLink extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'catalog_product_super_link';

    protected $primaryKey = 'link_id';

    protected $guarded = [];
}
