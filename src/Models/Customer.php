<?php

namespace Rapidez\Core\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Customer extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $primaryKey = 'entity_id';

    protected $table = 'customer_entity';

    public function getRememberTokenName()
    {
        return '';
    }
}
