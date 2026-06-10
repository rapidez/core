<?php

namespace Rapidez\Core\Models\Scopes\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Rapidez\Core\Enums\ProductStatus;

class EnabledScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereAttribute('status', ProductStatus::Enabled->value);
    }
}
