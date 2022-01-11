<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use TorMorten\Eventy\Facades\Eventy;

trait HasEventyGlobalScopeFilter
{
    public static function bootHasEventyGlobalScopeFilter()
    {
        $eventyName = strtolower(collect(explode('\\', get_called_class()))->last());

        static::withEventyGlobalScopes($eventyName.'.scopes');
    }

    public function scopeWithEventyGlobalScopes(Builder $query, string $scope)
    {
        $scopes = Eventy::filter($scope, []);

        foreach ($scopes as $scope) {
            $query->withGlobalScope($scope, new $scope());
        }

        return $query;
    }
}
