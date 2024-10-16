<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Actions\DecodeJwt;
use Rapidez\Core\Casts\CommaSeparatedToIntegerArray;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Quote extends Model
{
    protected $table = 'quote';

    protected $primaryKey = 'entity_id';

    protected $guarded = [];

    protected $casts = [
        'cross_sells' => CommaSeparatedToIntegerArray::class,
    ];

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
    }

    /** @return BelongsTo<Store, Quote> */
    public function store(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.store'));
    }

    /** @return HasMany<QuoteIdMask> */
    public function quote_id_masks(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.quote_id_mask'), 'quote_id');
    }

    /** @return HasMany<OauthToken> */
    public function oauth_tokens(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.oauth_token'), 'customer_id', 'customer_id');
    }

    /** @return BelongsTo<SalesOrder, Quote> */
    public function sales_order(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.sales_order'));
    }

    /** @return HasMany<QuoteItem> */
    public function items(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.quote_item'), 'quote_id');
    }

    /** @param Builder<Quote> $query */
    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, string $quoteIdMaskOrCustomerToken): void
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
                ->whereHas('quote_id_masks', fn (Builder $query) => $query
                    ->where('masked_id', $quoteIdMaskOrCustomerToken)
                )->orWhereHas('oauth_tokens', fn (Builder $query) => $query
                ->where('token', $quoteIdMaskOrCustomerToken)
                )
        );
    }
}
