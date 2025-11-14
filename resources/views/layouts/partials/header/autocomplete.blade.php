<Suspense>
    <template #default>
        <autocomplete v-slot="autocompleteSlotProps" :hits-per-page="{{ config('rapidez.frontend.autocomplete.size', 3) }}">
            <div class="relative w-full" ref="root">
                <ais-instant-search
                    v-if="autocompleteSlotProps.searchClient"
                    :future="{ preserveSharedStateOnUnmount: true }"
                    :search-client="autocompleteSlotProps.searchClient"
                    :middlewares="autocompleteSlotProps.middlewares"
                    :index-name="config.index.product"
                    class="contents"
                    v-cloak
                >
                    <div class="contents">
                        <div class="searchbox group/autocomplete">
                            <ais-autocomplete>
                                <template v-slot="{ currentRefinement, refine }">
                                    <x-rapidez::autocomplete.input
                                        v-bind:value="currentRefinement"
                                        v-on:focus="() => {
                                            refine(autocompleteFacadeQuery || currentRefinement);
                                            autocompleteFacadeQuery = null;
                                        }"
                                        v-on:input="refine($event.currentTarget.value)"
                                        list="search-history"
                                    />
                                    <div v-on:click="refine('')" class="fixed inset-0 bg-backdrop z-header-autocomplete-overlay hidden group-has-[input:not(:placeholder-shown)]/autocomplete:block group-has-[:focus]/autocomplete:block"></div>
                                </template>
                            </ais-autocomplete>
                            <div class="absolute inset-x-0 top-full mt-1 bg-white rounded-md z-header-autocomplete hidden group-has-[input:not(:placeholder-shown)]/autocomplete:block group-has-[:focus]/autocomplete:block hover:block">
                                @include('rapidez::layouts.partials.header.autocomplete.results')
                            </div>
                            <ais-stats-analytics></ais-stats-analytics>
                        </div>
                    </div>
                </ais-instant-search>
            </div>
        </autocomplete>
    </template>
    <template #fallback>
        <div class="relative w-full">
            <x-rapidez::autocomplete.input
                v-model="autocompleteFacadeQuery"
                v-on:focus="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                v-on:mouseover="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
            />
        </div>
    </template>
</Suspense>
