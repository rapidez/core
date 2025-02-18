<div v-if="!$root.loadAutocomplete" class="relative w-full">
    <form method="get" action="{{ route('search') }}">
        <x-rapidez::input
            type="search"
            name="q"
            :placeholder="__('What are you looking for?')"
            v-on:focus="$root.loadAutocomplete = true"
            v-on:mouseover="$root.loadAutocomplete = true"
        />
    </form>
    <x-rapidez::autocomplete.magnifying-glass />
</div>
<autocomplete v-else>
    <div slot-scope="{ searchClient, loaded }" class="relative w-full" v-if="loaded">
        <ais-instant-search
            class="contents"
            :search-client="searchClient"
            :index-name="config.index"
        >
            <div class="contents">
                <!-- TODO: This is a Vue 3 thing -->
                <ais-configure :hits-per-page.camel="3"/>
                <div class="searchbox">
                    <ais-search-box>
                        <template v-slot="{ currentRefinement, isSearchStalled, refine }">
                            <form name="autocomplete-form" id="autocomplete-form" method="get" action="{{ route('search') }}" class="flex flex-row relative">
                                <x-rapidez::input
                                    type="search"
                                    focus="true"
                                    autocomplete="off"
                                    autocorrect="off"
                                    autocapitalize="none"
                                    spellcheck="false"
                                    name="q"
                                    v-bind:value="currentRefinement"
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
    </div>
</autocomplete>
