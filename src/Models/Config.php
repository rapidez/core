<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Rapidez\Core\Exceptions\DecryptionException;

class Config extends Model
{
    protected $table = 'core_config_data';

    protected $primaryKey = 'config_id';

    protected static function booted()
    {
        static::addGlobalScope('scope-fallback', function (Builder $builder) {
            $builder
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('scope', 'stores')->where('scope_id', config('rapidez.store'));
                    })->orWhere(function ($query) {
                        $query->where('scope', 'websites')->where('scope_id', config('rapidez.website'));
                    })->orWhere(function ($query) {
                        $query->where('scope', 'default')->where('scope_id', 0);
                    });
                })
                ->orderByRaw('FIELD(scope, "stores", "websites", "default") ASC')
                ->limit(1);
        });
    }

    public static function getCachedByPath(string $path, $default = false, bool $sensitive = false): ?string
    {
        $cacheKey = 'config.'.config('rapidez.store').'.'.str_replace('/', '.', $path);

        $value = Cache::rememberForever($cacheKey, function () use ($path, $default) {
            $value = ($config = self::where('path', $path)->first('value')) ? $config->value : $default;
            return !is_null($value) ? $value : false;
        });

        return $sensitive && $value ? self::decrypt($value) : $value;
    }

    public static function decrypt(string $value): string
    {
        throw_unless(
            config('rapidez.crypt_key'),
            DecryptionException::class,
            'No crypt key set, set the CRYPT_KEY from Magento in the .env'
        );

        $parts = explode(':', $value);
        $partsCount = count($parts);
        $value = end($parts);

        throw_unless(
            $partsCount == 3,
            DecryptionException::class,
            'Unsupported crypt.'
        );

        $value = base64_decode($value);
        $nonce = mb_substr($value, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES, '8bit');
        $payload = mb_substr($value, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES, null, '8bit');

        return sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $payload,
            $nonce,
            $nonce,
            config('rapidez.crypt_key')
        );
    }
}
