
<autocomplete v-on:mounted="() => window.document.getElementById('autocomplete-input').focus()" v-slot="{ searchClient}">
    <div class="relative w-full">
        <ais-instant-search
            v-if="searchClient"
            v-cloak
            class="contents"
            :search-client="searchClient"
            :index-name="config.index_prefix + '_product_' + config.store"
        >
            <div class="contents">
                <!-- TODO: This is a Vue 3 thing -->
                <ais-configure :hits-per-page.camel="3"/>
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
                        </template>
                    </ais-search-box>
                </div>
                <div class="absolute inset-x-0 bg-white border">
                    @include('rapidez::layouts.partials.header.autocomplete.results')
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
