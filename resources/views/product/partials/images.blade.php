<div class="relative">
    @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
        <div class="absolute top-0 right-0 z-10 group p-2">
            @include('rapidez::wishlist.button')
        </div>
    @endif
    @if (count($product->images))
        <div class="absolute inset-0 flex h-[440px] items-center justify-center rounded border p-5">
            <img
                class="m-auto max-h-[400px] w-full object-contain"
                src="/storage/resizes/400/catalog/product{{ $product->images[0] }}.webp"
                alt="{{ $product->name }}"
                width="400"
                height="400"
            />
        </div>
    @endif

    <images v-cloak>
        <div slot-scope="{ images, active, zoomed, toggleZoom, change }">
            <div class="relative" v-if="images.length">
                <a
                    :href="config.media_url + '/catalog/product' + images[active]"
                    class="flex items-center justify-center"
                :class="zoomed ? 'fixed inset-0 bg-white !h-full {{ config('rapidez.z-indexes.lightbox')}} cursor-[zoom-out]' : 'border rounded p-5 h-[440px]'"
                    v-on:click.prevent="toggleZoom"
                >
                    <img
                        v-if="!zoomed"
                        :src="'/storage/resizes/400/magento/catalog/product' + images[active] + '.webp'"
                        alt="{{ $product->name }}"
                        class="object-contain w-full m-auto max-h-[400px]"
                        width="400"
                        height="400"
                    />
                    <img
                        v-else
                        :src="config.media_url + '/catalog/product' + images[active]"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full"
                        loading="lazy"
                    />
                </a>
                <button class="{{ config('rapidez.z-indexes.lightbox') }} top-1/2 left-3 -translate-y-1/2" :class="zoomed ? 'fixed' : 'absolute'" v-if="active" v-on:click="change(active-1)" aria-label="@lang('Prev')">
                    <x-heroicon-o-chevron-left class="h-8 w-8 text-gray-900" />
                </button>
                <button class="{{ config('rapidez.z-indexes.lightbox') }} top-1/2 right-3 -translate-y-1/2" :class="zoomed ? 'fixed' : 'absolute'" v-if="active != images.length-1" v-on:click="change(active+1)" aria-label="@lang('Next')">
                    <x-heroicon-o-chevron-right class="h-8 w-8 text-gray-900" />
                </button>
            </div>
            <x-rapidez::no-image v-else class="h-96 rounded" />

            <div v-if="images.length > 1" class="flex" :class="zoomed ? 'fixed bottom-0 left-3 {{ config('rapidez.z-indexes.lightbox')}}' : ' flex-wrap mt-3'">
                <a
                    href="#"
                    v-for="(image, imageId) in images"
                    class="flex items-center justify-center bg-white border rounded p-2 mr-3 mb-3 h-[100px] w-[100px]"
                    :class="active == imageId ? 'border-neutral' : ''"
                    @click.prevent="change(imageId)"
                >
                    <img
                        :src="'/storage/resizes/80x80/magento/catalog/product' + image + '.webp'"
                        alt="{{ $product->name }}"
                        class="object-contain w-full m-auto max-h-[80px]"
                        loading="lazy"
                        width="80"
                        height="80"
                    />
                </a>
            </div>
            <div v-if="zoomed" class="{{ config('rapidez.z-indexes.lightbox') }} pointer-events-none fixed top-3 right-3">
                <x-heroicon-o-x class="h-6 w-6" />
            </div>
        </div>
    </images>
</div>
