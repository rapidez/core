<ais-state-results v-slot="{ state: { query: searchQuery } }">
    <div>
        <div v-if="searchHistory && searchHistory?.filter(([query, metadata]) => query.includes(searchQuery.toLowerCase())).length" class="border-b py-2">
            <x-rapidez::autocomplete.title>
                @lang('Previous Searches')
            </x-rapidez::autocomplete.title>
            <ul class="flex flex-col font-sans">
                <li
                    v-for="[query, metadata] in searchHistory
                        .filter(([query, metadata]) => query.includes(searchQuery.toLowerCase()))
                        .slice(0, {{ Arr::get($fields, 'size', config('rapidez.frontend.autocomplete.size', 3)) }})"
                    class="flex flex-1 items-center w-full hover:bg-muted"
                >
                    <a
                        v-bind:href="'{{ route('search', ['q' => '%query%']) }}'.replaceAll('%25query%25', query)"
                        class="relative flex items-center group w-full px-5 py-2 text-sm gap-x-4"
                    >
                        @{{ query }}
                    </a>
                </li>
            </ul>
            {{-- Add search suggestions to the phone's keyboard --}}
            <datalist id="search-history" v-if="window.matchMedia('(pointer:coarse)').matches">
                <option v-bind:value="query" v-for="[query, metadata] in searchHistory"></option>
            </datalist>
        </div>
    </div>
</ais-state-results>
