<ais-hits v-bind:index-name="config.index.product" v-bind:index-id="'autocomplete_' + config.index.product">
    <template v-slot="{ items, sendEvent }">
        <div v-if="items && items.length" class="py-2.5">
            <x-rapidez::autocomplete.title>
                @lang('Products')
            </x-rapidez::autocomplete.title>
            <ul v-for="(item, count) in items" class="gap-2 flex flex-col">
                <li class="hover:bg-muted">
                    <a :href="item.url | url" v-on:click="sendEvent('click', item, 'Hit Clicked')" class="group relative flex flex-wrap p-2">
                        <img
                            v-if="item.thumbnail"
                            :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + item.thumbnail + '.webp'"
                            class="shrink-0 self-center object-contain size-16 mix-blend-multiply"
                            :alt="item.name"
                            width="200"
                            height="200"
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
