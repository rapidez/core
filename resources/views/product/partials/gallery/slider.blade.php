<div v-if="images.length" class="relative">
    <a
        v-on:click.prevent="toggleZoom"
        v-bind:href="config.media_url + '/catalog/product' + images[active]"
        class="flex h-[440px] cursor-zoom-in items-center justify-center rounded border p-5"
    >
        <img
            src="/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product/{{ Arr::first($product->images) }}.webp"
            {{-- src should always be above v-bind:src --}}
            v-bind:src="'/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product' + images[active] + '.webp'"
            alt="{{ $product->name }}"
            class="max-h-full object-contain"
            width="400"
            height="400"
        />
    </a>
    @if (count($product->images) > 1)
        <button v-if="active" v-cloak v-on:click="change(active-1)" class="absolute left-3 top-1/2 z-10 -translate-y-1/2" aria-label="@lang('Prev')">
            <x-heroicon-o-chevron-left class="size-8 text-inactive" />
        </button>
        <button v-if="active != images.length-1" v-on:click="change(active+1)" class="absolute right-3 top-1/2 z-10 -translate-y-1/2" aria-label="@lang('Next')">
            <x-heroicon-o-chevron-right class="size-8 text-inactive" />
        </button>
    @endif
</div>

<x-rapidez::no-image v-else v-cloak class="h-96 rounded" />
