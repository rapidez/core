<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Actions\DecodeJwt;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Quote extends Model
{
    protected $table = 'quote';

    protected $primaryKey = 'entity_id';

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
    }

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales_order'));
    }

    public function items()
    {
        return $this->hasMany(config('rapidez.models.quote_item'), 'quote_id');
    }

    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, string $quoteIdMaskOrCustomerToken)
    {
        $query->when(
            DecodeJwt::isJwt($quoteIdMaskOrCustomerToken),
            fn (Builder $query) => $query
                ->where(
                    $this->qualifyColumn('customer_id'),
                    DecodeJwt::decode($quoteIdMaskOrCustomerToken)
                        ->claims()
                        ->get('uid')
                ),
            fn (Builder $query) => $query
                ->where('masked_id', $quoteIdMaskOrCustomerToken)
                ->orWhere('token', $quoteIdMaskOrCustomerToken)
        );
    }
}
