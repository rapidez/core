<reactive-base
    {{ $attributes->merge([
        ':app' => "config.es_prefix + '_products_' + config.store",
        ':url' => "config.es_url",
        ':set-search-params' => "(url) => setSearchParams(url)",
    ]) }}
    v-cloak>
    {{ $slot }}
</reactive-base>

