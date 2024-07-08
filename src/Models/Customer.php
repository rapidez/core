<?php

namespace Rapidez\Core\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
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

    public function oauthTokens()
    {
        return $this->hasMany(config('rapidez.models.oauth_token'), 'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'group_id');
    }

    public function getRememberTokenName()
    {
        return '';
    }

    public function scopeWhereToken(Builder $query, string $token)
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
