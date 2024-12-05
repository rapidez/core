<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Scopes\ForCurrentStoreScope;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class CheckoutAgreement extends Model
{
    protected $table = 'checkout_agreement';

    protected $primaryKey = 'agreement_id';

    protected $guarded = [];

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope(new ForCurrentStoreScope);
    }
}
