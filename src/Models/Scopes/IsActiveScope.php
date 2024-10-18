<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class IsActiveScope implements Scope
{
    public function __construct(public string $column = 'is_active') {}

    /** @param Builder<Model> $builder */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($builder->qualifyColumn($this->column), 1);
    }
}
