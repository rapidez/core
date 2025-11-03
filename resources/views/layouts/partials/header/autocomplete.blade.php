<autocomplete v-slot="autocompleteSlotProps" :hits-per-page="{{ config('rapidez.frontend.autocomplete.additionals.products.size', config('rapidez.frontend.autocomplete.size', 3)) }}">
    <toggler v-slot="autoCompleteToggler">
        <div class="w-full">
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
                                        $root.autocompleteFacadeQuery = null;
                                        autoCompleteToggler.toggle(true);
                                        window.setTimeout(() => window.requestAnimationFrame(() => window.document.getElementById('autocomplete-input-fullscreen').focus()));
                                    }"
                                    v-on:input="refine($event.currentTarget.value)"
                                    list="search-history"
                                    id="autocomplete-input"
                                />
                            </div>
                        </ais-autocomplete>
                        <div class="fixed inset-0 searchbox bg-white group z-header-autocomplete-popup flex flex-col h-full" v-if="autoCompleteToggler.isOpen">
                            <div class="py-3 bg">
                                <div class="container">
                                    <input checked type="checkbox" class="prevent-scroll hidden">
                                    <ais-autocomplete v-slot="{ currentRefinement, refine }">
                                        <div class="flex gap-x-3">
                                            <div class="h-12 shrink-0 xl:flex-1 max-sm:hidden">
                                                <img v-on:click="autoCompleteToggler.close(); refine('')" src="https://raw.githubusercontent.com/rapidez/art/master/r.svg" alt="Rapidez logo" height="50" width="50" class="h-full w-auto cursor-pointer block">
                                            </div>

                                            <div class="max-w-2xl w-full mx-auto">
                                                <x-rapidez::autocomplete.input
                                                    v-bind:value="currentRefinement"
                                                    v-on:focus="() => {
                                                        refine($root.autocompleteFacadeQuery || currentRefinement);
                                                        $root.autocompleteFacadeQuery = null;
                                                    }"
                                                    @keydown.escape="autoCompleteToggler.close()"
                                                    v-on:input="refine($event.currentTarget.value)"
                                                    id="autocomplete-input-fullscreen"
                                                    list="search-history"
                                                />
                                            </div>

                                            <div class="flex xl:flex-1 justify-end">
                                                <button v-on:click="autoCompleteToggler.close(); refine('')" class="flex items-center justify-center shrink-0 text-muted hover:text">
                                                    <x-heroicon-o-x-mark class="size-8" />
                                                </button>
                                            </div>
                                        </div>
                                    </ais-autocomplete>
                                </div>
                            </div>

                            <div class="h-full bg-white overflow-hidden relative z-10">
                                <div class="size-full overflow-y-auto">
                                    <div class="container pt-5 pb-8 overflow-y-auto gap-y-3 flex flex-col">
                                        @include('rapidez::layouts.partials.header.autocomplete.results')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ais-stats-analytics></ais-stats-analytics>
                    </div>
                </ais-instant-search>
            </template>

            <div class="relative w-full" v-else>
                <x-rapidez::autocomplete.input
                    v-model="$root.autocompleteFacadeQuery"
                    v-on:focus="autoCompleteToggler.toggle(true), window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                    id="autocomplete-input"
                />
            </div>
        </div>
    </toggler>
</autocomplete>
