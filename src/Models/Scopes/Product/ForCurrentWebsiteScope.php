<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ForCurrentWebsiteScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->leftJoin('catalog_product_website', 'catalog_product_website.product_id', '=', $model->getQualifiedKeyName());
        $builder->where('website_id', config('rapidez.website'));
    }
}
