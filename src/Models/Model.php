<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Rapidez\Core\Models\Traits\BaseEventyTrait;
use TorMorten\Eventy\Facades\Eventy;

class Model extends BaseModel
{
    use BaseEventyTrait;
}
