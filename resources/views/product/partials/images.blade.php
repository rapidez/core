<div class="relative flex flex-col flex-1">
    <images>
        <div class="flex-1" slot-scope="{ images, active, zoomed, toggleZoom, change }">
            <div class="sticky top-5 bg-white">
                @include('rapidez::product.partials.gallery.slider')
                @include('rapidez::product.partials.gallery.thumbnails')
            </div>

            @include('rapidez::product.partials.gallery.popup')
        </div>
    </images>
</div>
