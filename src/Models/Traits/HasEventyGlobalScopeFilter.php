<?php

namespace Rapidez\Core\Models\Traits;

use TorMorten\Eventy\Facades\Eventy;

trait HasEventyGlobalScopeFilter
{
    public static function bootHasEventyGlobalScopeFilter()
    {
        $eventyName = strtolower(collect(explode('\\', get_called_class()))->last());
        $scopes = Eventy::filter($eventyName.'.scopes', []);
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope());
        }
    }
}
