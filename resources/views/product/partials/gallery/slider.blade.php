<div class="relative" v-if="media.length">
    <a
        v-on:click.prevent="toggleZoom"
        v-bind:href="config.media_url + '/catalog/product' + media[active].image"
        class="flex h-[440px] cursor-zoom-in items-center justify-center rounded border p-5"
    >
        <img
            {{-- src should always be above v-bind:src --}}
            v-if="media[active].media_type === 'image'"
            v-bind:src="window.url('/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product' + media[active].image + '.webp')"
            @if (count($selectedChild->media))
                src="{{ url('/storage/'.config('rapidez.store').'/resizes/400/magento/catalog/product'.Arr::first($selectedChild->media)['image'].'.webp') }}"
            @endif
            alt="{{ $product->name }}"
            class="max-h-full object-contain"
            style="view-transition-name: image-{{ $product->sku }}"
            fetchpriority="high"
            width="400"
            height="400"
        />
        <iframe
            v-else-if="media[active].media_type === 'external-video'"
            v-cloak
            class="h-full w-full"
            :src="media[active].video_url"
            allow="autoplay"
            width="100%"
            height="100%"
            frameborder="0"
            loading="lazy"
        >
        </iframe>
    </a>

    @if (count($selectedChild->media ?? []) > 1)
        <button v-if="active" v-on:click="change(active-1)" class="z-10 top-1/2 left-3 -translate-y-1/2 absolute" aria-label="@lang('Prev')" v-cloak>
            <x-heroicon-o-chevron-left class="size-8 text" />
        </button>
        <button v-if="active != media.length-1" v-on:click="change(active+1)" class="z-10 top-1/2 right-3 -translate-y-1/2 absolute" aria-label="@lang('Next')">
            <x-heroicon-o-chevron-right class="size-8 text" />
        </button>
    @endif
</div>

<x-rapidez::no-image v-else class="h-96 rounded" v-cloak />
