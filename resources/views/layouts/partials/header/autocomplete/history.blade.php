<ais-state-results v-slot="{ state: { query: searchQuery } }" class="max-w-2xl w-full mx-auto">
    <div>
        <div v-if="autocompleteSlotProps.searchHistory && autocompleteSlotProps.searchHistory?.filter(([query, metadata]) => query.includes(searchQuery.toLowerCase())).length">
            <x-rapidez::autocomplete.title>
                @lang('Previous Searches')
            </x-rapidez::autocomplete.title>
            <ul class="flex flex-col font-sans">
                <li
                    v-for="[query, metadata] in autocompleteSlotProps.searchHistory
                        .filter(([query, metadata]) => query.includes(searchQuery.toLowerCase()))
                        .slice(0, {{ Arr::get($fields, 'size', config('rapidez.frontend.autocomplete.size', 3)) }})"
                    class="flex flex-1 items-center w-full hover:bg rounded"
                >
                    <a
                        v-bind:href="'{{ route('search', ['q' => 'searchPlaceholder']) }}'.replace('searchPlaceholder', encodeURIComponent(query))"
                        class="relative flex items-center group w-full px-5 py-2 text-sm gap-x-4"
                        data-turbo="false"
                    >
                        @{{ query }}
                    </a>
                </li>
            </ul>
            {{-- Add search suggestions to the phone's keyboard --}}
            <datalist id="search-history" v-if="window.matchMedia('(pointer:coarse)').matches">
                <option v-bind:value="query" v-for="[query, metadata] in autocompleteSlotProps.searchHistory"></option>
            </datalist>
        </div>
    </div>
</ais-state-results>
