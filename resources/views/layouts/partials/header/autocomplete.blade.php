<autocomplete v-on:mounted="() => window.document.getElementById('autocomplete-input').focus()" v-slot="{ searchClient, middlewares }">
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
                <ais-configure :hits-per-page.camel="{{ config('rapidez.frontend.autocomplete.size', 3) }}" />
                <div class="searchbox">
                    <ais-search-box>
                        <template v-slot="{ currentRefinement, isSearchStalled, refine }">
                            <x-rapidez::autocomplete.input
                                v-bind:value="currentRefinement"
                                v-on:focus="() => {
                                    refine($root.autocompleteFacadeQuery || currentRefinement);
                                    $root.autocompleteFacadeQuery = null;
                                }"
                                v-on:input="refine($event.currentTarget.value)"
                            />
                            <div v-if="currentRefinement" class="absolute inset-x-0 top-14 bg-white border rounded-md z-header-autocomplete">
                                @include('rapidez::layouts.partials.header.autocomplete.results')
                            </div>
                            <div v-if="currentRefinement" v-on:click="refine('')" class="fixed inset-0 bg-backdrop z-header-autocomplete-overlay"></div>
                        </template>
                    </ais-search-box>
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
