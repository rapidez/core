<div class="relative" v-if="images.length">
    <a
        v-on:click.prevent="toggleZoom"
        v-bind:href="config.media_url + '/catalog/product' + images[active]"
        class="flex h-[440px] cursor-zoom-in items-center justify-center rounded-sm border p-5"
    >
        <img
            {{-- src should always be above v-bind:src --}}
            src="/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product/{{ Arr::first($product->images) }}.webp"
            v-bind:src="'/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product' + images[active] + '.webp'"
            alt="{{ $product->name }}"
            class="max-h-full object-contain"
            width="400"
            height="400"
        />
    </a>

    @if (count($product->images ?? []) > 1)
        <button v-if="active" v-on:click="change(active-1)" class="z-10 top-1/2 left-3 -translate-y-1/2 absolute" aria-label="@lang('Prev')" v-cloak>
            <x-heroicon-o-chevron-left class="size-8 text-inactive" />
        </button>
        <button v-if="active != images.length-1" v-on:click="change(active+1)" class="z-10 top-1/2 right-3 -translate-y-1/2 absolute" aria-label="@lang('Next')">
            <x-heroicon-o-chevron-right class="size-8 text-inactive" />
        </button>
    @endif
</div>

<x-rapidez::no-image v-else class="h-96 rounded-sm" v-cloak />
