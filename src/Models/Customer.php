<?php

namespace Rapidez\Core\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rapidez\Core\Actions\DecodeJwt;

class Customer extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $primaryKey = 'entity_id';

    protected $table = 'customer_entity';

    protected $hidden = [
        'password_hash',
        'rp_token',
        'rp_token_created_at',
        'confirmation',
    ];

    /** @return HasMany<OauthToken, Customer> */
    public function oauthTokens(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.oauth_token'), 'customer_id');
    }

    /** @return BelongsTo<CustomerGroup, Customer> */
    public function group(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.customer_group'), 'group_id');
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * @param  Builder<Customer>  $query
     */
    public function scopeWhereToken(Builder $query, string $token): void
    {
        $query->when(
            DecodeJwt::isJwt($token),
            fn (Builder $query) => $query
                ->where(
                    $this->getQualifiedKeyName(),
                    DecodeJwt::decode($token)
                        ->claims()
                        ->get('uid')
                ),
            fn (Builder $query) => $query
                ->whereHas('oauthTokens', fn (Builder $query) => $query->where('token', $token))
        );
    }
}
