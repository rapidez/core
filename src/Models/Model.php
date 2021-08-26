<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Rapidez\Core\Models\Traits\HasEventyGlobalScopeFilter;

class Model extends BaseModel
{
    use HasEventyGlobalScopeFilter;
}
