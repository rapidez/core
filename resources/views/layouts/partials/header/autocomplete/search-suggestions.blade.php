<search-suggestions v-slot="searchSuggestions" :force-results="false">
    <ais-instant-search
        v-if="searchSuggestions.searchClient"
        :index-name="config.index.search_query"
        :search-client="searchSuggestions.searchClient"
    >
        <ais-configure
            :query="currentRefinement || ' '"
            :hits-per-page.camel="{{ Arr::get($fields, 'size', config('rapidez.frontend.autocomplete.size', 3)) }}"
            filters="display_in_terms:1"
        />
        <ais-hits v-slot="{ items }">
            <div v-if="items && items.length" v-bind:class="{ 'border-b': currentRefinement }" class="py-2.5">
                <x-rapidez::autocomplete.title>
                    @lang('Suggestions')
                </x-rapidez::autocomplete.title>
                <ul class="flex flex-col font-sans">
                    <li v-for="(item, count) in items" class="flex flex-1 items-center w-full hover:bg-muted">
                        <a
                            v-bind:href="window.url(item.redirect || '{{ route('search', ['q' => 'searchPlaceholder']) }}'.replace('searchPlaceholder', encodeURIComponent(item.query_text)))"
                            class="relative flex items-center group w-full px-5 py-2 text-sm gap-x-4"
                        >
                            <x-rapidez::highlight attribute="query_text" class="line-clamp-2"/>
                        </a>
                    </li>
                </ul>
            </div>
        </ais-hits>
    </ais-instant-search>
</search-suggestions>
