<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class IsActiveScope implements Scope
{
    public string $column;

    public function __construct($column = 'is_active')
    {
        $this->column = $column;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->where($builder->getQuery()->from.'.'.$this->column, 1);
    }
}
