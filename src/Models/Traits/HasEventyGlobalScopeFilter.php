<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use TorMorten\Eventy\Facades\Eventy;

trait HasEventyGlobalScopeFilter
{
    public static function bootHasEventyGlobalScopeFilter()
    {
        $eventyName = strtolower(collect(explode('\\', get_called_class()))->last());
        $scopes = Eventy::filter($eventyName . '.scopes', []);

        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope);
        }
    }

    public function scopeWithEventyGlobalScopes(Builder $query, string $scope)
    {
        $scopes = Eventy::filter($scope, []);

        foreach ($scopes as $scope) {
            $query->withGlobalScope($scope, new $scope);
        }

        return $query;
    }
}
