@props(['frame', 'viewPath', 'isRoute' => false)

<turbo-frame id="menu" @unless ($isRoute) src="/turbo/{{ $frame }}/{{ Rapidez::getCacheKey() }}" loading="lazy" target="_top" @endunless>
    @includeCached($viewPath, ['deferred' => $isRoute])
</turbo-frame>
