<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use TorMorten\Eventy\Facades\Eventy;

class Model extends BaseModel
{
    protected static function booted()
    {
        $eventyName = strtolower(collect(explode('\\', get_called_class()))->last());
        $scopes = Eventy::filter($eventyName.'.scopes', []);
        foreach ($scopes as $scope) {
            static::addGlobalScope(new $scope());
        }
    }
}
