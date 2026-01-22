<?php

namespace Rapidez\Core\Models\Scopes\Category;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Rapidez\Core\Models\Category;

class IsActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereAttribute('is_active', Category::STATUS_ACTIVE);
    }
}
