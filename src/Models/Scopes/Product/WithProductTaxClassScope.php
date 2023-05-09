<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WithProductTaxClassScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->addSelect($model->getTable().'.tax_class_id');
    }
}
