<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use TorMorten\Eventy\Facades\Eventy;

trait HasEventyGlobalScopeFilter
{
    abstract public static function getModelName(): string;

    public static function bootHasEventyGlobalScopeFilter()
    {
        $scopes = Eventy::filter(static::getModelName() . '.scopes', []);

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
