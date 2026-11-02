<div id="products" class="flex flex-col max-lg:flex-wrap">
    <div class="pb-4 border-b">
        @include('rapidez::listing.partials.toolbar')
    </div>
    <ais-hits v-show="listingSlotProps.requestCount > 0" :transform-items="listingSlotProps.transformItems">
        <template v-slot="{ items, sendEvent }">
            @include('rapidez::listing.partials.products-grid')
        </template>
    </ais-hits>

    @include('rapidez::listing.partials.pagination')
</div>
