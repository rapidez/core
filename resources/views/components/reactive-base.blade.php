<reactive-base
    {{ $attributes->merge([
        ':app' => "config.es_prefix + '_products_' + config.store",
        ':url' => "config.es_url",
        ':set-search-params' => "(url) => {window.history.pushState({ path: url }, '', url); window.Turbo.navigator.history.push(url);}",
    ]) }}>
    {{ $slot }}
</reactive-base>
