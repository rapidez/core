<images v-cloak>
    <div slot-scope="{ images, active, zoomed, toggleZoom, change }">
        <div class="relative" v-if="images.length">
            <a
                :href="config.media_url + '/catalog/product' + images[active]"
                class="flex items-center justify-center"
                :class="zoomed ? 'fixed inset-0 bg-white !h-full z-10 cursor-[zoom-out]' : 'border rounded p-5 h-[450px]'"
                v-on:click.prevent="toggleZoom"
            >
                <picture v-if="!zoomed" class="contents">
                    <source :srcset="'/storage/resizes/450/catalog/product' + images[active] + '.webp'" type="image/webp">
                    <img
                        :src="'/storage/resizes/450/catalog/product' + images[active]"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full"
                        loading="lazy"
                    />
                </picture>
                <img
                    v-else
                    :src="config.media_url + '/catalog/product' + images[active]"
                    alt="{{ $product->name }}"
                    class="max-h-full max-w-full"
                    loading="lazy"
                />
            </a>

            <button class="top-1/2 z-10 -translate-y-1/2 left-3" :class="zoomed ? 'fixed' : 'absolute'" v-if="active" v-on:click="change(active-1)">
                <x-heroicon-s-chevron-left class="h-8 w-8"/>
            </button>

            <button class="top-1/2 z-10 -translate-y-1/2 right-3" :class="zoomed ? 'fixed' : 'absolute'" v-if="active != images.length-1" v-on:click="change(active+1)">
                <x-heroicon-s-chevron-right class="h-8 w-8"/>
            </button>
        </div>
        <x-rapidez::no-image v-else class="rounded h-96"/>

        <div v-if="images.length > 1" class="flex" :class="zoomed ? 'fixed z-10 bottom-0' : ' flex-wrap mt-3'">
            <a
                href="#"
                v-for="(image, imageId) in images"
                class="flex items-center justify-center border rounded p-2 mr-3 mb-3 h-[100px] w-[100px]"
                :class="active == imageId ? 'border-primary' : ''"
                @click.prevent="change(imageId)"
            >
                <picture class="contents">
                    <source :srcset="'/storage/resizes/100x100/catalog/product' + image + '.webp'" type="image/webp">
                    <img
                        :src="'/storage/resizes/100x100/catalog/product' + image"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full"
                        loading="lazy"
                    />
                </picture>
            </a>
        </div>

        <div v-if="zoomed" class="fixed top-3 right-3 z-10 pointer-events-none">
            <x-heroicon-o-x class="h-6 w-6"/>
        </div>
    </div>
</images>
