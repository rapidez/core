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
                @include('rapidez::product.partials.gallery.slider')
                @include('rapidez::product.partials.gallery.thumbnails')
            </div>

            @include('rapidez::product.partials.gallery.popup')
        </div>
    </images>
</div>