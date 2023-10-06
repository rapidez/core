<?php

namespace Rapidez\Core\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Customer extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $primaryKey = 'entity_id';

    protected $table = 'customer_entity';

    protected $hidden = [
        'password_hash',
        'rp_token',
        'rp_token_created_at',
        'confirmation'
    ];
    
    public function getRememberTokenName()
    {
        return '';
    }
}
