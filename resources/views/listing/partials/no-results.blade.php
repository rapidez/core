<div class="bg rounded-md mt-6 p-10">
    <h2 class="font-sans text-xl font-medium">@lang('No products found.')</h2>

    <ais-index v-bind:index-name="config.index.category" v-bind:index-id="'listing_' + config.index.category">
        <ais-hits v-slot="{ items }">
            <div class="border-b py-2.5" v-if="items && items.length">
                <strong>@lang('Categories'):</strong>
                <ul class="flex flex-col font-sans">
                    <li v-for="(item, count) in items" class="flex flex-1 items-center w-full">
                        <a v-bind:href="item.url" class="flex items-center group py-1 gap-x-0.5 hover:underline">
                            <x-heroicon-o-chevron-right class="text-primary size-3.5 shrink-0"/> <x-rapidez::highlight attribute="name" class="line-clamp-2"/>
                        </a>
                    </li>
                </ul>
            </div>
        </ais-hits>
    </ais-index>

    <ais-state-results v-slot="{ state: { query: searchQuery } }">
        <search-suggestions v-slot="searchSuggestions">
            <ais-instant-search
                v-if="searchSuggestions.searchClient"
                :index-name="config.index.search_query"
                :search-client="searchSuggestions.searchClient"
            >
                <ais-configure
                    :query="searchQuery"
                    :hits-per-page.camel="5"
                />
                <ais-hits v-slot="{ items }">
                    <div class="mt-1" v-if="items && items.length">
                        <div class="font-sans text mb-2">@lang('Here are some suggestions:')</div>
                        <ul class="flex flex-col font-sans">
                            <li v-for="(item, count) in items" class="flex flex-1 items-center w-full">
                                <a
                                    v-bind:href="window.url(item.redirect || '{{ route('search', ['q' => 'searchPlaceholder']) }}'.replace('searchPlaceholder', encodeURIComponent(item.query_text)))"
                                    class="flex items-center group py-1 gap-x-0.5 hover:underline"
                                >
                                    <x-heroicon-o-chevron-right class="text-primary size-3.5 shrink-0"/> <span v-text="item.query_text"></span>
                                </a>
                            </li>
                        </ul>
                        <x-rapidez::button.outline href="/" class="mt-4 bg-white" aria-label="@lang('Go to home')">
                            @lang('Go to home')
                        </x-rapidez::button.outline>
                    </div>
                </ais-hits>
            </ais-instant-search>
        </search-suggestions>
    </search-suggestions>
</div>
