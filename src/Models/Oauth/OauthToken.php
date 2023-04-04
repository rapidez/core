<?php

namespace Rapidez\Core\Models\Oauth;

use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Customer\CustomerEntity;

class OauthToken extends Model
{
    protected $table = 'oauth_token';

    protected $primaryKey = 'entity_id';

    public $timestamps = false;

    protected $casts = [
        'consumer_id' => 'int',
        'admin_id' => 'int',
        'customer_id' => 'int',
        'revoked' => 'int',
        'authorized' => 'int',
        'user_type' => 'int',
    ];

    protected $hidden = [
        'token',
        'secret',
    ];

    public function customer_entity()
    {
        return $this->belongsTo(config('rapidez.models.customer.entity'), 'customer_id');
    }
}
