<?php

namespace Rapidez\Core\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TorMorten\Eventy\Facades\Eventy;

trait HasEventyGlobalScopeFilter
{
    public static function bootHasEventyGlobalScopeFilter(): void
    {
        $eventyName = strtolower(collect(explode('\\', get_called_class()))->last() ?? 'undefined');

        /** @var array<string> $scopes */
        // @phpstan-ignore-next-line
        $scopes = Eventy::filter($eventyName . '.scopes', []);

        foreach ($scopes as $scope) {
            /** @var \Illuminate\Database\Eloquent\Scope $scopeObj */
            $scopeObj = new $scope;
            static::addGlobalScope($scopeObj);
        }
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     * */
    public function scopeWithEventyGlobalScopes(Builder $query, string $scope): Builder
    {
        /** @var array<string> $scopes */
        // @phpstan-ignore-next-line
        $scopes = Eventy::filter($scope, []);

        foreach ($scopes as $scope) {
            /** @var \Illuminate\Database\Eloquent\Scope $scopeObj */
            $scopeObj = new $scope;
            $query->withGlobalScope($scope, $scopeObj);
        }

        return $query;
    }
}
