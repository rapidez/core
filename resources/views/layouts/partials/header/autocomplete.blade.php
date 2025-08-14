<autocomplete v-slot="autocompleteSlotProps" :hits-per-page="{{ config('rapidez.frontend.autocomplete.size', 3) }}">
    <toggler>
        <div class="relative w-full" slot-scope="{ isOpen, toggle }">
            <ais-instant-search
                v-if="autocompleteSlotProps.searchClient && isOpen"
                :search-client="autocompleteSlotProps.searchClient"
                :middlewares="autocompleteSlotProps.middlewares"
                :index-name="config.index.product"
                class="contents"
                v-cloak
            >
                <div class="fixed inset-0 h-full bg-white/90 backdrop-blur-sm searchbox group/autocomplete z-header-autocomplete-popup flex flex-col pt-14 px-10 overflow-y-auto">
                    <div class="relative">
                        <div class="flex items-start relative">
                            <x-rapidez::button.outline v-on:click="toggle(); refine('')" class="absolute left-0 top-0">
                                <x-heroicon-o-chevron-left class="size-5" />
                            </x-rapidez::button.outline>
                            <img src="https://raw.githubusercontent.com/rapidez/art/master/r.svg" alt="Rapidez logo" height="50" width="50" class="mx-auto mb-11">
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <div class="max-w-xl bg-white rounded-3xl shadow-xl mx-auto border border-muted w-full px-9 py-8 mb-10">
                                <ais-autocomplete v-slot="{ currentRefinement, refine }">
                                    <x-rapidez::autocomplete.input
                                        class="rounded-full focus:ring-primary focus:border-primary transition h-14 focus:ring-2"
                                        v-bind:value="currentRefinement"
                                        v-on:focus="() => {
                                            refine($root.autocompleteFacadeQuery || currentRefinement);
                                            $root.autocompleteFacadeQuery = null;
                                        }"
                                        v-on:input="refine($event.currentTarget.value)"
                                        list="search-history"
                                    />
                                    <div v-bind:class="{hidden: !currentRefinement}" class="group-has-[:focus]/autocomplete:block">
                                        @include('rapidez::layouts.partials.header.autocomplete.results')
                                    </div>
                                    {{-- <div v-bind:class="{hidden: !currentRefinement}" v-on:click="refine('')" class="fixed inset-0 bg-backdrop z-header-autocomplete-overlay group-has-[:focus]/autocomplete:block"></div> --}}
                                </ais-autocomplete>
                            </div>
                        </div>
                        @includeWhen(config('rapidez.frontend.autocomplete.additionals.products') === null,'rapidez::layouts.partials.header.autocomplete.products')
                        <ais-stats-analytics></ais-stats-analytics>
                    </div>
                </div>
            </ais-instant-search>

            <div class="relative w-full">
                <x-rapidez::autocomplete.input
                    v-model="$root.autocompleteFacadeQuery"
                    v-on:focus="window.document.dispatchEvent(new window.Event('loadAutoComplete')), toggle()"
                    v-on:mouseover="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                />
            </div>
        </div>
    </toggler>
</autocomplete>
