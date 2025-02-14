<ais-hits>
    <template v-slot="{ items }">
        <div v-if="items && items.length" class="p-2">
            <x-rapidez::autocomplete.title>@lang('Products')</x-rapidez::autocomplete.title>
            <ul v-for="(item, count) in items" class="gap-2 flex flex-col">
                <li>
                    <a :href="item.url | url" class="group relative flex flex-wrap py-2">
                        <img
                            v-if="item.thumbnail"
                            :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + item.thumbnail + '.webp'"
                            class="shrink-0 self-center object-contain size-16 mix-blend-multiply" :alt="item.name" :loading="config.category && count <= 4 ? 'eager' : 'lazy'"
                            width="200"
                            height="200"
                        />
                        <x-rapidez::no-image v-else class="mb-3 h-48 rounded-t" />
                        <div class="flex flex-1 justify-center flex-col px-2">
                            <span>
                                <ais-highlight
                                    attribute="name"
                                    :hit="item"
                                    highlightedTagName="mark"
                                />
                            </span>

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
