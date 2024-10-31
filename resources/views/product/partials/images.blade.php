<div class="relative flex flex-col flex-1">
    @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
        <div class="absolute top-0 right-0 z-10 group p-2">
            @include('rapidez::wishlist.button')
        </div>
    @endif
    @if (count($product->images))
        <div class="absolute inset-0 flex">
            <div class="h-[440px] items-center justify-center rounded p-5 border sticky top-5 w-full">
                <img
                    class="max-h-full w-full object-contain"
                    src="/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product{{ $product->images[0] }}.webp"
                    alt="{{ $product->name }}"
                    width="400"
                    height="400"
                />
            </div>
        </div>
    @endif

    <images v-cloak>
        <div class="flex-1" slot-scope="{ images, active, zoomed, toggleZoom, change }">
            <div class="sticky top-5 bg-white">
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

                <div v-if="images.length > 1" class="flex mt-3 gap-2">
                    <button
                        v-for="(image, imageId) in images.slice(0, {{ max(config('rapidez.frontend.product_gallery_thumbnails.xs'), config('rapidez.frontend.product_gallery_thumbnails.sm'), config('rapidez.frontend.product_gallery_thumbnails.md'), config('rapidez.frontend.product_gallery_thumbnails.lg'), config('rapidez.frontend.product_gallery_thumbnails.xl')) + 1 }})"
                        class="flex items-center justify-center bg-white border rounded p-1.5 aspect-square max-w-24 flex-1 transition-all outline-transparent overflow-hidden relative"
                        :class="{
                            'outline outline-1 !outline-neutral border-neutral': active == imageId,
                            'xl:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.xl') }},
                            'lg:max-xl:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.lg') }},
                            'md:max-lg:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.md') }},
                            'sm:max-md:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.sm') }},
                            'max-sm:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.xs') }}
                        }"
                        @click="change(imageId)"
                    >
                        <img
                            :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + image + '.webp'"
                            alt="{{ $product->name }}"
                            class="object-contain block w-auto max-h-full"
                            width="80"
                            height="80"
                        />
                        <span
                            v-if="(imageId + 1) < images.length"
                            v-on:click="toggleZoom()"
                            class="absolute inset-0 items-center justify-center bg-neutral/20 hidden"
                            :class="{
                                'xl:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.xl') }},
                                'lg:max-xl:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.lg') }},
                                'md:max-lg:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.md') }},
                                'sm:max-md:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.sm') }},
                                'max-sm:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.xs') }}
                            }"
                        >
                            <span class="size-9 flex items-center justify-center rounded-full shadow-lg bg-white text-sm font-bold text-neutral">
                                +@{{ images.length - (imageId + 1) }}
                            </span>
                        </span>
                    </button>
                </div>
            </div>

            <div v-if="images.length && zoomed" class="fixed inset-0 bg-white cursor-zoom-out z-popup flex">
                <div class="flex flex-1 items-center justify-center" v-on:click.prevent="toggleZoom">
                    <img
                        :src="config.media_url + '/catalog/product' + images[active]"
                        alt="{{ $product->name }}"
                        class="object-contain max-h-full mx-auto block"
                        loading="lazy"
                    />
                    <div class="z-10 pointer-events-none fixed top-3 right-3">
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </div>
                </div>
                <button class="z-10 top-1/2 left-3 -translate-y-1/2 absolute" v-if="active" v-on:click="change(active-1)" aria-label="@lang('Prev')">
                    <x-heroicon-o-chevron-left class="size-8 text-inactive" />
                </button>
                <button class="z-10 top-1/2 right-3 -translate-y-1/2 absolute" v-if="active != images.length-1" v-on:click="change(active+1)" aria-label="@lang('Next')">
                    <x-heroicon-o-chevron-right class="size-8 text-inactive" />
                </button>
            </div>
        </div>
    </images>
</div>