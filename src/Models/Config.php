<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Exceptions\DecryptionException;
use Rapidez\Core\Facades\Rapidez;

enum ConfigScopes
{
    case SCOPE_STORE;
    case SCOPE_WEBSITE;
    case SCOPE_DEFAULT;
}

class Config extends Model
{
    protected $table = 'core_config_data';

    protected $primaryKey = 'config_id';

    protected static function booting()
    {
        static::addGlobalScope('scope-fallback', function (Builder $builder) {
            $builder
                ->where(fn ($query) => $query
                    ->where(fn ($query) => $query->whereStore(config('rapidez.store')))
                    ->orWhere(fn ($query) => $query->whereWebsite(config('rapidez.website')))
                    ->orWhere(fn ($query) => $query->whereDefault())
                )
                ->limit(1);
        });

        static::addGlobalScope('scope-default-sorting', function (Builder $builder) {
            $builder
                ->orderByRaw('FIELD(scope, "stores", "websites", "default") ASC');
        });
    }

    public function scopeWhereStore(Builder $query, ?int $storeId): void
    {
        $query->where('scope', 'stores')->where('scope_id', $storeId);
    }

    public function scopeWhereWebsite(Builder $query, ?int $websiteId): void
    {
        $query->where('scope', 'websites')->where('scope_id', $websiteId);
    }

    public function scopeWhereDefault(Builder $query): void
    {
        $query->where('scope', 'default')->where('scope_id', 0);
    }

    /**
     * @deprecated see: Config::getValue
     */
    public static function getCachedByPath(string $path, $default = false, bool $sensitive = false): string|bool
    {
        return static::getValue($path, options: ['cache' => true, 'decrypt' => $sensitive]) ?? $default;
    }

    /**
     * Magento compatible Config getValue method.
     */
    public static function getValue(
        string $path,
        ConfigScopes $scope = ConfigScopes::SCOPE_STORE,
        ?int $scopeId = null,
        array $options = ['cache' => true, 'decrypt' => false]
    ): mixed {
        $scopeId ??= match ($scope) {
            ConfigScopes::SCOPE_WEBSITE => config('rapidez.website') ?? Rapidez::getStore(config('rapidez.store'))['website_id'],
            ConfigScopes::SCOPE_STORE   => config('rapidez.store'),
            default                     => 0
        };
        $websiteId = $scope === ConfigScopes::SCOPE_STORE ? Rapidez::getStore($scopeId)['website_id'] : $scopeId;

        $query = static::query()
            ->withoutGlobalScope('scope-fallback')
            ->where('path', $path)
            ->where(fn ($query) => $query
                ->when($scope === ConfigScopes::SCOPE_STORE, fn ($query) => $query->whereStore($scopeId))
                ->when($scope !== ConfigScopes::SCOPE_DEFAULT, fn ($query) => $query->orWhere(fn ($query) => $query->whereWebsite($websiteId)))
                ->orWhere(fn ($query) => $query->whereDefault())
            )
            ->limit(1);

        $result = ((bool) $options['cache'] ? $query->getCachedForever() : $query->get())->value('value');

        return (bool) $options['decrypt'] && is_string($result) ? static::decrypt($result) : $result;
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
