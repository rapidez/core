<?php

namespace Rapidez\Core\Auth;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\Middleware\EncryptCookies;

class MagentoCartTokenGuard extends MagentoCustomerTokenGuard implements Guard
{
    public static function register()
    {
        app()->afterResolving(EncryptCookies::class, function (EncryptCookies $middleware) {
            $middleware->disableFor('mask');
        });

        auth()->extend('magento-cart', function (Application $app, string $name, array $config) {
            return new self(auth()->createUserProvider($config['provider']), request(), 'mask', 'mask');
        });

        config([
            'auth.guards.magento-cart' => [
                'driver'   => 'magento-cart',
                'provider' => 'users',
            ],
        ]);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        Authenticate::redirectUsing(fn ($request) => route('cart'));
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
        return config('rapidez.models.quote')::whereQuoteIdOrCustomerToken($token)->first();
    }
}
