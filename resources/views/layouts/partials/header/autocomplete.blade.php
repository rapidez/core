<autocomplete v-slot="{ searchClient, middlewares, searchHistory }" :hits-per-page="{{ config('rapidez.frontend.autocomplete.size', 3) }}">
    <div class="relative w-full">
        <ais-instant-search
            v-if="searchClient"
            :search-client="searchClient"
            :middlewares="middlewares"
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
                                    refine($root.autocompleteFacadeQuery || currentRefinement);
                                    $root.autocompleteFacadeQuery = null;
                                }"
                                v-on:input="refine($event.currentTarget.value)"
                                list="search-history"
                            />
                            <div v-bind:class="{hidden: !currentRefinement}" class="absolute inset-x-0 top-full mt-1 bg-white rounded-md z-header-autocomplete group-has-[:focus]/autocomplete:block hover:block">
                                @include('rapidez::layouts.partials.header.autocomplete.results')
                            </div>
                            <div v-bind:class="{hidden: !currentRefinement}" v-on:click="refine('')" class="fixed inset-0 bg-backdrop z-header-autocomplete-overlay group-has-[:focus]/autocomplete:block"></div>
                        </template>
                    </ais-autocomplete>
                    <ais-stats-analytics></ais-stats-analytics>
                </div>
            </div>
        </ais-instant-search>
        <div v-else class="relative w-full">
            <x-rapidez::autocomplete.input
                v-model="$root.autocompleteFacadeQuery"
                v-on:focus="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                v-on:mouseover="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
            />
        </div>
    </div>
</autocomplete>
