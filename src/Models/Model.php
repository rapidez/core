<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Traits\Macroable;
use Rapidez\Core\Models\Traits\HasEventyGlobalScopeFilter;

class Model extends BaseModel
{
    use HasEventyGlobalScopeFilter;
    use Macroable {
        Macroable::__call as macroCall;
        Macroable::__callStatic as macroCallStatic;
    }

    // @phpstan-ignore-next-line
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCall($method, $parameters);
        }

        return parent::__call($method, $parameters);
    }

    // @phpstan-ignore-next-line
    public static function __callStatic($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCallStatic($method, $parameters);
        }

        return parent::__callStatic($method, $parameters);
    }
}
