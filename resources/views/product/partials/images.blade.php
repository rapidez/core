<images v-cloak>
    <div slot-scope="{ images, active, zoomed, toggleZoom, change }">
        <div class="relative" v-if="images.length">
            <a
                :href="config.media_url + '/catalog/product' + images[active]"
                class="flex items-center justify-center"
                :class="zoomed ? 'fixed inset-0 bg-white !h-full {{ config('rapidez.z-indexes.lightbox')}} cursor-[zoom-out]' : 'border rounded p-5 h-[440px]'"
                v-on:click.prevent="toggleZoom"
            >
                <picture v-if="!zoomed" class="contents">
                    <source :srcset="'/storage/resizes/400/catalog/product' + images[active] + '.webp'" type="image/webp">
                    <img
                        :src="'/storage/resizes/400/catalog/product' + images[active]"
                        alt="{{ $product->name }}"
                        class="object-contain w-full m-auto max-h-[400px]"
                        loading="lazy"
                        width="400"
                        height="400"
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

            <button class="top-1/2 -translate-y-1/2 left-3 {{ config('rapidez.z-indexes.lightbox')}}" :class="zoomed ? 'fixed' : 'absolute'" v-if="active" v-on:click="change(active-1)" aria-label="@lang('Prev')">
                <x-heroicon-s-chevron-left class="h-12 w-12"/>
            </button>

            <button class="top-1/2 -translate-y-1/2 right-3 {{ config('rapidez.z-indexes.lightbox')}}" :class="zoomed ? 'fixed' : 'absolute'" v-if="active != images.length-1" v-on:click="change(active+1)" aria-label="@lang('Next')">
                <x-heroicon-s-chevron-right class="h-12 w-12"/>
            </button>
        </div>
        <x-rapidez::no-image v-else class="rounded h-96"/>

        <div v-if="images.length > 1" class="flex" :class="zoomed ? 'fixed bottom-0 left-3 {{ config('rapidez.z-indexes.lightbox')}}' : ' flex-wrap mt-3'">
            <a
                href="#"
                v-for="(image, imageId) in images"
                class="flex items-center justify-center bg-white border rounded p-2 mr-3 mb-3 h-[100px] w-[100px]"
                :class="active == imageId ? 'border-primary' : ''"
                @click.prevent="change(imageId)"
            >
                <picture class="contents">
                    <source :srcset="'/storage/resizes/80x80/catalog/product' + image + '.webp'" type="image/webp">
                    <img
                        :src="'/storage/resizes/80x80/catalog/product' + image"
                        alt="{{ $product->name }}"
                        class="object-contain w-full m-auto max-h-[80px]"
                        loading="lazy"
                        width="80"
                        height="80"
                    />
                </picture>
            </a>
        </div>

        <div v-if="zoomed" class="fixed top-3 right-3 pointer-events-none {{ config('rapidez.z-indexes.lightbox')}}">
            <x-heroicon-o-x class="h-6 w-6"/>
        </div>
    </div>
</images>
