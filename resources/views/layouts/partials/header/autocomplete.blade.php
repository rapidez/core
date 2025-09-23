<autocomplete v-slot="autocompleteSlotProps" :hits-per-page="{{ config('rapidez.frontend.autocomplete.additionals.products.size', config('rapidez.frontend.autocomplete.size', 3)) }}">
    <toggler>
        <div class="w-full" slot-scope="{ isOpen, toggle }">
            <template v-if="autocompleteSlotProps.searchClient">
                <ais-instant-search
                    :search-client="autocompleteSlotProps.searchClient"
                    :middlewares="autocompleteSlotProps.middlewares"
                    :index-name="config.index.product"
                    class="contents"
                    v-cloak
                >
                    <div>
                        <ais-autocomplete v-slot="{ currentRefinement, refine }">
                            <div>
                                <x-rapidez::autocomplete.input
                                    v-bind:value="currentRefinement"
                                    v-on:focus="() => {
                                        refine($root.autocompleteFacadeQuery || currentRefinement);
                                        $root.autocomeleteFacadeQuery = null;
                                        toggle();
                                    }"
                                    v-on:input="refine($event.currentTarget.value)"
                                    list="search-history"
                                    id="autocomplete-input"
                                />

                                <div class="fixed inset-0 searchbox group z-header-autocomplete-popup flex flex-col h-full" v-if="isOpen">
                                    <div class="py-3 bg">
                                        <div class="container">
                                            <div class="flex gap-x-3 justify-between">
                                                <img src="https://raw.githubusercontent.com/rapidez/art/master/r.svg" alt="Rapidez logo" height="50" width="50" class="w-14 h-12 object-contain">

                                                <div class="max-w-2xl w-full">
                                                    <x-rapidez::autocomplete.input
                                                        v-bind:value="currentRefinement"
                                                        v-on:focus="() => {
                                                            refine($root.autocompleteFacadeQuery || currentRefinement);
                                                            $root.autocomeleteFacadeQuery = null;
                                                        }"
                                                        v-on:input="refine($event.currentTarget.value)"
                                                        list="search-history"
                                                    />
                                                </div>

                                                <x-rapidez::button.outline v-on:click="toggle(); refine('')" class="bg-white h-12 w-14 px-0 shrink-0">
                                                    <x-heroicon-o-x-mark class="size-5" />
                                                </x-rapidez::button.outline>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="max-h-full bg-white overflow-hidden relative z-10">
                                        <div class="size-full overflow-y-auto">
                                            <div class="container pt-4 pb-8 overflow-y-auto">
                                                @include('rapidez::layouts.partials.header.autocomplete.results')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 bg-backdrop cursor-pointer" v-on:click="toggle(); refine('')"></div>
                                </div>
                            </div>
                        </ais-autocomplete>

                        <ais-stats-analytics></ais-stats-analytics>
                    </div>
                </ais-instant-search>
            </template>

            <div class="relative w-full" v-else>
                <x-rapidez::autocomplete.input
                    v-model="$root.autocompleteFacadeQuery"
                    v-on:focus="toggle(), window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                    id="autocomplete-input"
                />
            </div>
        </div>
    </toggler>
</autocomplete>
