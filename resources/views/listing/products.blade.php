{{-- TODO: Is this wrapper with these classes still the cleanest option? --}}
<div id="products" class="flex flex-col gap-3 *:flex-wrap *:gap-3 *:max-sm:gap-y-3 *:max-md:justify-end">
    @include('rapidez::listing.partials.toolbar')

    <ais-hits>
        <template v-slot="{ items }">
            <div v-if="items" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 md:gap-5 overflow-hidden">
                <template v-for="(item, count) in items">
                    @include('rapidez::listing.partials.item')
                </template>
            </div>
            <div v-else>
                @include('rapidez::listing.partials.no-results')
            </div>
        </template>
    </ais-hits>

    @include('rapidez::listing.partials.pagination')
</div>
