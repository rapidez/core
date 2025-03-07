
<autocomplete inline-template v-on:mounted="() => window.document.getElementById('autocomplete-input').focus()">
    <div class="relative w-full">
        <ais-instant-search
            v-if="searchClient"
            v-cloak
            class="contents"
            :search-client="searchClient "
            :index-name="config.index_prefix + '_products_' + config.store"
        >
            <div class="contents">
                <!-- TODO: This is a Vue 3 thing -->
                <ais-configure :hits-per-page.camel="3"/>
                <div class="searchbox">
                    <ais-search-box>
                        <template v-slot="{ currentRefinement, isSearchStalled, refine }">
                            <form name="autocomplete-form" id="autocomplete-form" method="get" action="{{ route('search') }}" class="flex flex-row relative">
                                <x-rapidez::input
                                    id="autocomplete-input"
                                    type="search"
                                    focus="true"
                                    autocomplete="off"
                                    autocorrect="off"
                                    autocapitalize="none"
                                    spellcheck="false"
                                    name="q"
                                    v-bind:value="currentRefinement"
                                    v-on:focus="() => {
                                        refine($root.autocompleteFacadeQuery || currentRefinement);
                                        $root.autocompleteFacadeQuery = null;
                                    }
                                    "
                                    v-on:input="refine($event.currentTarget.value)"
                                    :placeholder="__('What are you looking for?')"
                                />
                                <x-rapidez::button class="absolute right-0 bg-none border-none" type="submit">
                                    <x-rapidez::autocomplete.magnifying-glass />
                                </x-rapidez::button>
                            </form>
                        </template>
                    </ais-search-box>
                </div>
                <div class="absolute inset-x-0 bg-white border">
                    @include('rapidez::layouts.partials.header.autocomplete.results')
                </div>
            </div>
        </ais-instant-search>
        <div v-else class="relative w-full">
            {{-- TODO: Do we still need this double input? --}}
            <form name="autocomplete-form" id="autocomplete-form" method="get" action="{{ route('search') }}" class="flex flex-row relative">
                <x-rapidez::input
                    type="search"
                    name="q"
                    :placeholder="__('What are you looking for?')"
                    v-model="$root.autocompleteFacadeQuery"
                    v-on:focus="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                    v-on:mouseover="window.document.dispatchEvent(new window.Event('loadAutoComplete'))"
                />
            </form>
            <x-rapidez::autocomplete.magnifying-glass />
        </div>
    </div>
</autocomplete>
