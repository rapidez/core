<?php

namespace Rapidez\Core\Actions;

use Carbon\FactoryImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class DecodeJwt
{
    public function __invoke($jwt)
    {
        return static::decode(...func_get_args());
    }

    public static function decode($jwt): Plain
    {
        foreach (static::getKeys() as $key) {
            try {
                return (new JwtFacade)->parse(
                    $jwt,
                    new SignedWith(new (config('rapidez.jwt.signed_with')), $key),
                    new LooseValidAt(new FactoryImmutable)
                );
            } catch (RequiredConstraintsViolated $exception) {
                if (! Arr::first($exception->violations, fn (ConstraintViolation $violation) => $violation->getMessage() === 'Token signature mismatch')) {
                    throw $exception;
                }
            }
        }

        throw $exception;
    }

    public static function getKeys()
    {
        return Str::of(config('rapidez.crypt_key'))
            ->trim()
            ->split('/\s+/s')
            ->map(fn ($key) => InMemory::plainText(str_pad($key, 2048, '&', STR_PAD_BOTH)));
    }

    public static function isJwt($jwt): bool
    {
        return preg_match('/^(?:[\w-]*\.){2}[\w-]*$/', $jwt);
    }
}
