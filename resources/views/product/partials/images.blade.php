<div class="relative flex flex-col flex-1">
    @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
        <div class="absolute top-0 right-0 z-10 group p-2">
            @include('rapidez::wishlist.button')
        </div>
    @endif

    <images>
        <div class="flex-1" slot-scope="{ images, active, zoomed, toggleZoom, change }">
            <div class="sticky top-5 bg-white">
                @include('rapidez::product.partials.gallery.slider')
                @includeWhen(count($product->images) > 1, 'rapidez::product.partials.gallery.thumbnails')
            </div>

            @include('rapidez::product.partials.gallery.popup')
        </div>
    </images>
</div>
