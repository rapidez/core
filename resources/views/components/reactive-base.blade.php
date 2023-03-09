<reactive-base
    {{ $attributes->merge([
        'v-bind:app' => "config.es_prefix + '_products_' + config.store",
        'v-bind:url' => "config.es_url",
        'v-bind:set-search-params' => "(url) => {window.history.pushState({ path: url }, '', url); window.Turbo.navigator.history.push(url);}",
    ]) }}>
    {{ $slot }}
</reactive-base>
