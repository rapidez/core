<ais-hits v-bind:index-name="config.index.product" v-bind:index-id="'autocomplete_' + config.index.product">
    <template v-slot="{ items, sendEvent }">
        <div v-if="items && items.length" class="py-2.5">
            <h2 class="font-medium text-2xl mb-5">
                @lang('Products')
            </h2>
            <div class="overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 -mx-5 -mb-px *:border-b">
                    <template v-for="(item, count) in items">
                        @include('rapidez::listing.partials.item')
                    </template>
                </div>
            </div>
        </div>
    </template>
</ais-hits>