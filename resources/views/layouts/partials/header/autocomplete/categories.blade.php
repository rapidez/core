<ais-index :index-name="config.index_prefix + '_categories_' + config.store">
    <ais-hits v-slot="{ items }">
        <div class="border-b p-2" v-if="items && items.length">
            <x-rapidez::autocomplete.title>@lang('Categories')</x-rapidez::autocomplete.title>
            <ul class="flex flex-col font-sans">
                <li v-for="(item, count) in items" class="flex flex-1 items-center w-full">
                    <a v-bind:href="item.url" class="relative flex items-center group w-full py-2 text-sm gap-x-4">
                        <span class="ml-2 line-clamp-2">
                            <ais-highlight
                                attribute="name"
                                :hit="item"
                                highlightedTagName="mark"
                            />
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </ais-hits>
</ais-index>
