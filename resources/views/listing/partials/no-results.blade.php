<div class="bg rounded-md mt-6 p-10">
    <h2 class="font-sans text-xl font-medium">@lang('No products found.')</h2>
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
                        <x-rapidez::button.outline class="mt-4 bg-white" href="/" aria-label="@lang('Go to home')">
                            @lang('Go to home')
                        </x-rapidez::button.outline>
                    </div>
                </ais-hits>
            </ais-instant-search>
        </search-suggestions>
    </search-suggestions>
</div>
