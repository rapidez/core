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
                src="/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product{{ $product->images[0] }}.webp"
                alt="{{ $product->name }}"
                width="400"
                height="400"
            />
        </div>
    @endif

    <images v-cloak>
        <div slot-scope="imagesScope">
            <div class="relative" v-if="imagesScope.images.length">
                <a
                    :href="config.media_url + '/catalog/product' + imagesScope.images[imagesScope.active]"
                    class="flex items-center justify-center"
                    :class="imagesScope.zoomed ? 'fixed inset-0 bg-white !h-full {{ config('rapidez.frontend.z-indexes.lightbox') }} cursor-[zoom-out]' : 'border rounded p-5 h-[440px]'"
                    v-on:click.prevent="imagesScope.toggleZoom"
                >
                    <img
                        v-if="!imagesScope.zoomed"
                        :src="'/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product' + imagesScope.images[imagesScope.active] + '.webp'"
                        alt="{{ $product->name }}"
                        class="object-contain w-full m-auto max-h-[400px]"
                        width="400"
                        height="400"
                    />
                    <img
                        v-else
                        :src="config.media_url + '/catalog/product' + imagesScope.images[imagesScope.active]"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full"
                        loading="lazy"
                    />
                </a>
                <button class="{{ config('rapidez.frontend.z-indexes.lightbox') }} top-1/2 left-3 -translate-y-1/2" :class="imagesScope.zoomed ? 'fixed' : 'absolute'" v-if="imagesScope.active" v-on:click="imagesScope.change(imagesScope.active-1)" aria-label="@lang('Prev')">
                    <x-heroicon-o-chevron-left class="h-8 w-8 text-gray-900" />
                </button>
                <button class="{{ config('rapidez.frontend.z-indexes.lightbox') }} top-1/2 right-3 -translate-y-1/2" :class="imagesScope.zoomed ? 'fixed' : 'absolute'" v-if="imagesScope.active != imagesScope.images.length-1" v-on:click="imagesScope.change(imagesScope.active+1)" aria-label="@lang('Next')">
                    <x-heroicon-o-chevron-right class="h-8 w-8 text-gray-900" />
                </button>
            </div>
            <x-rapidez::no-image v-else class="h-96 rounded" />

            <div v-if="imagesScope.images.length > 1" class="flex" :class="imagesScope.zoomed ? 'fixed bottom-0 left-3 {{ config('rapidez.z-indexes.lightbox') }}' : ' flex-wrap mt-3'">
                <button
                    v-for="(image, imageId) in imagesScope.images"
                    class="flex items-center justify-center bg-white border rounded p-2 mr-3 mb-3 h-[100px] w-[100px]"
                    :class="imagesScope.active == imageId ? 'border-neutral' : ''"
                    @click="imagesScope.change(imageId)"
                >
                    <img
                        :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + image + '.webp'"
                        alt="{{ $product->name }}"
                        class="object-contain w-full m-auto max-h-[80px]"
                        loading="lazy"
                        width="80"
                        height="80"
                    />
                </button>
            </div>
            <div v-if="imagesScope.zoomed" class="{{ config('rapidez.frontend.z-indexes.lightbox') }} pointer-events-none fixed top-3 right-3">
                <x-heroicon-o-x-mark class="h-6 w-6" />
            </div>
        </div>
    </images>
</div>
