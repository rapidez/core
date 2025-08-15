<ais-hits v-bind:index-name="config.index.product" v-bind:index-id="'autocomplete_' + config.index.product">
    <template v-slot="{ items, sendEvent }">
        <div v-if="items && items.length" class="py-2.5">
            <x-rapidez::autocomplete.title>
                @lang('Products')
            </x-rapidez::autocomplete.title>
            <ul class="grid grid-cols-5 gap-x-5 gap-y-8">
                <li
                    v-for="(item, count) in items"
                    class="hover:bg-muted"
                    data-testid="autocomplete-item"
                >
                    <a :href="item.url | url" v-on:click="sendEvent('click', item, 'Hit Clicked')" class="group relative flex flex-col flex-wrap p-2 bg-white border border-muted rounded-xl shadow-xl">
                        <img
                            v-if="item.thumbnail"
                            :src="'/storage/{{ config('rapidez.store') }}/resizes/400/magento/catalog/product' + item.thumbnail + '.webp'"
                            class="shrink-0 self-center object-contain mix-blend-multipl h-64 w-auto"
                            :alt="item.name"
                            width="400"
                            height="400"
                        />
                        <x-rapidez::no-image v-else class="mb-3 h-48 rounded-t" />
                        <div class="flex flex-1 justify-center flex-col px-2">
                            <x-rapidez::highlight attribute="name"/>

                            <div class="flex items-center gap-x-0.5 mt-0.5">
                                <div v-if="item.special_price" class="text-muted font-sans line-through text-xs">
                                    @{{ item.price | price }}
                                </div>
                                <div class="text-sm text font-sans font-bold">
                                    @{{ (item.special_price || item.price) | price }}
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </template>
</ais-hits>
