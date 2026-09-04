@props(['frame', 'viewPath', 'isRoute' => false])
<turbo-frame
    id="menu"
    @unless ($isRoute)
        src="{{ route('turbo-frame', ['frame' => $frame, 'cachekey' => Rapidez::getCacheKey()]) }}"
        loading="lazy"
        target="_top"
    @endunless
>
    @includeCached($viewPath, ['complete' => $isRoute])
</turbo-frame>
