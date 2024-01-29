<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

class OauthToken extends Model
{
    protected $table = 'oauth_token';

    protected static function booting(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder
                ->where('revoked', 0);
        });
    }
}
