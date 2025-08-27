<?php

namespace Rapidez\Core\Auth;

use BadMethodCallException;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\Middleware\EncryptCookies;

class MagentoCustomerTokenGuard extends TokenGuard implements StatefulGuard
{
    public static function register()
    {
        app()->afterResolving(EncryptCookies::class, function (EncryptCookies $middleware) {
            $middleware->disableFor('token');
        });

        auth()->extend('magento-customer', function (Application $app, string $name, array $config) {
            return new self(auth()->createUserProvider($config['provider']), request(), 'token', 'token');
        });

        config([
            'auth.guards.magento-customer' => [
                'driver'   => 'magento-customer',
                'provider' => 'users',
            ],
        ]);
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest()
    {
        $token = parent::getTokenForRequest();

        if (empty($token)) {
            $token = $this->request->cookie($this->inputKey);
        }

        return $token;
    }

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
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        return (bool) $this->retrieveByToken($credentials[$this->inputKey]);
    }

    protected function retrieveByToken($token)
    {
        return config('rapidez.models.customer')::whereToken($token)->first();
    }

    // The following methods are not implemented for token-based authentication.
    // @see: https://github.com/laravel/framework/pull/56785

    public function attempt(array $credentials = [], $remember = false)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function once(array $credentials = [])
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function login(Authenticatable $user, $remember = false)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function loginUsingId($id, $remember = false)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function onceUsingId($id)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function viaRemember()
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function logout()
    {
        throw new BadMethodCallException('Method not implemented.');
    }
}
