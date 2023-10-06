<?php

namespace Rapidez\Core\Models;

class CategoryProduct extends Model
{
    protected $primaryKey = 'entity_id';

    public function getTable()
    {
        return 'catalog_category_product_index_store' . config('rapidez.store');
    }
}
