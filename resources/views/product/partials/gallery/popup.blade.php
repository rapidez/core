<div v-if="images.length && zoomed" class="fixed inset-0 bg-white cursor-zoom-out flex z-popup">
    <div class="flex flex-1 items-center justify-center" v-on:click.prevent="toggleZoom">
        <img
            :src="config.media_url + '/catalog/product' + images[active]"
            alt="{{ $product->name }}"
            class="object-contain max-h-full mx-auto block"
            loading="lazy"
        />
        <div class="z-popup-actions pointer-events-none fixed top-3 right-3">
            <x-heroicon-o-x-mark class="size-6" />
        </div>
    </div>
    <button class="z-popup-actions top-1/2 left-3 -translate-y-1/2 absolute" v-if="active" v-on:click="change(active-1)" aria-label="@lang('Prev')">
        <x-heroicon-o-chevron-left class="size-8 text-muted" />
    </button>
    <button class="z-popup-actions top-1/2 right-3 -translate-y-1/2 absolute" v-if="active != images.length-1" v-on:click="change(active+1)" aria-label="@lang('Next')">
        <x-heroicon-o-chevron-right class="size-8 text-muted" />
    </button>
</div>