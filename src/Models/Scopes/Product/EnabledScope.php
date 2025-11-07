<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Rapidez\Core\Models\Product;

class EnabledScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereAttribute('status', Product::STATUS_ENABLED);
    }
}
