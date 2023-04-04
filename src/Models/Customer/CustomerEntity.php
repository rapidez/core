<?php

namespace Rapidez\Core\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Oauth\OauthToken;
use Rapidez\Core\Models\Sales\SalesOrder;

class CustomerEntity extends Model
{
    protected $table = 'customer_entity';

    protected $primaryKey = 'entity_id';

    protected $casts = [
        'website_id' => 'int',
        'group_id' => 'int',
        'store_id' => 'int',
        'is_active' => 'int',
        'disable_auto_group_change' => 'int',
        'default_billing' => 'int',
        'default_shipping' => 'int',
        'gender' => 'int',
        'failures_num' => 'int',
    ];

    protected $dates = [
        'dob',
        'rp_token_created_at',
        'first_failure',
        'lock_expires',
    ];

    protected $hidden = [
        'rp_token',
    ];

    public function oauth_tokens()
    {
        return $this->hasMany(config('rapidez.models.oauth.token'), 'customer_id');
    }

    public function sales_orders()
    {
        return $this->hasMany(config('rapidez.models.sales.order'), 'customer_id');
    }
}
