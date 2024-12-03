<div class="relative" v-if="images.length">
    <a
        :href="config.media_url + '/catalog/product' + images[active]"
        class="flex items-center justify-center border rounded p-5 h-[440px] cursor-zoom-in"
        v-on:click.prevent="toggleZoom"
    >
        <img
            :src="'/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product' + images[active] + '.webp'"
            alt="{{ $product->name }}"
            class="object-contain max-h-full"
            width="400"
            height="400"
        />
    </a>
    <button class="z-10 top-1/2 left-3 -translate-y-1/2 absolute" v-if="active" v-on:click="change(active-1)" aria-label="@lang('Prev')">
        <x-heroicon-o-chevron-left class="size-8 text-inactive" />
    </button>
    <button class="z-10 top-1/2 right-3 -translate-y-1/2 absolute" v-if="active != images.length-1" v-on:click="change(active+1)" aria-label="@lang('Next')">
        <x-heroicon-o-chevron-right class="size-8 text-inactive" />
    </button>
</div>

<x-rapidez::no-image v-else class="h-96 rounded" />
