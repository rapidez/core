<?php

namespace Rapidez\Core\Auth;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rapidez\Core\Actions\DecodeJwt;

class MagentoCustomerTokenGuard extends TokenGuard implements Guard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenForRequest();

        return $this->user = empty($token) ? null : $this->retrieveByToken($token);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        return (bool)$this->retrieveByToken($credentials[$this->inputKey]);
    }

    protected function retrieveByToken($token) {
        return config('rapidez.models.customer')::whereToken($token)->first();
    }
}
