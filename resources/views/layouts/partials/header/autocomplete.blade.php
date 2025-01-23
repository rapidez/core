<autocomplete>

</autocomplete>


@return

@php $inputClasses = 'relative z-header-autocomplete border !font-sans !border-default !text-sm !min-h-0 outline-0 ring-0 !h-auto rounded-xl !pl-5 !pr-24 !py-3.5 !bg-white w-full focus:ring-transparent search-input' @endphp

<div v-if="!$root.loadAutocomplete" class="relative w-full">
    <label for="autocomplete-input" class="sr-only">@lang('Search')</label>
    <input
        id="autocomplete-input"
        placeholder="@lang('What are you looking for?')"
        class="{{ $inputClasses }}"
        v-on:focus="$root.loadAutocomplete = true"
    >
    <x-rapidez::autocomplete.magnifying-glass />
</div>

<autocomplete
    v-else
    v-on:mounted="() => window.document.getElementById('autocomplete-input').focus()"
    v-bind:additionals="{{ json_encode(config('rapidez.frontend.autocomplete.additionals')) }}"
    v-bind:debounce="{{ config('rapidez.frontend.autocomplete.debounce') }}"
    v-bind:size="{{ config('rapidez.frontend.autocomplete.size') }}"
    class="relative w-full"
    v-cloak
>
    {{-- <x-rapidez::reactive-base slot-scope="autocompleteScope"> --}}
        <div
            class="z-header-autocomplete-overlay pointer-events-none fixed inset-0 cursor-pointer bg-black/40 opacity-0 transition duration-500"
            :class="autocompleteScope.overlay ? 'pointer-events-auto opacity-100 prevent-scroll' : 'opacity-0 pointer-events-none '"
        ></div>
        <x-rapidez::autocomplete.magnifying-glass v-bind:class="{ 'bg-primary text-white': autocompleteScope.overlay }" />
        <data-search
            placeholder="@lang('What are you looking for?')"
            v-on:value-selected="search"
            component-id="autocomplete"
            :inner-class="{ input: '{{ $inputClasses }}' }"
            class="relative [&_*]:!m-0"
            :data-field="Object.keys(config.searchable)"
            :field-weights="Object.values(config.searchable)"
            :show-icon="false"
            fuzziness="AUTO"
            v-bind:debounce="autocompleteScope.debounce"
            v-on:blur="autocompleteScope.showOverlay(false)"
            v-on:focus="autocompleteScope.showOverlay(true)"
            v-bind:size="autocompleteScope.size"
            :highlight="true"
            v-on:value-change="autocompleteScope.searchAdditionals"
            v-on:key-down="autocompleteScope.startLoading"
            v-on:suggestions="autocompleteScope.stopLoading"
        >
            <div slot="render" slot-scope="dataSearchScope">
                <div
                    v-if="dataSearchScope.downshiftProps.isOpen && !autocompleteScope.searchLoading && !dataSearchScope.loading && dataSearchScope.value"
                    class="z-header-autocomplete absolute -inset-x-5 top-14 overflow-x-hidden overflow-y-auto scrollbar-hide pt-4 pb-7 bg-white max-md:h-[calc(100svh-150px)] max-md:max-h-[calc(100svh-150px)] md:top-14 md:max-h-[calc(100svh-150px)] md:rounded-xl md:border md:inset-x-0 md:w-full md:-translate-y-px"
                >
                    <div v-if="dataSearchScope.data.length || autocompleteScope.resultsCount">
                        <div class="flex flex-col prose-li:px-5 hover:prose-li:bg-muted">
                            {{-- The order can be changed with https://tailwindcss.com/docs/order --}}
                            <template v-for="(resultsData, resultsType) in autocompleteScope.results ?? {}" v-if="resultsData?.hits?.length">
                                @foreach (config('rapidez.frontend.autocomplete.additionals') as $key => $fields)
                                    <template v-if="resultsType == '{{ $key }}'">
                                        @includeIf('rapidez::layouts.partials.header.autocomplete.' . $key)
                                    </template>
                                @endforeach
                            </template>
                            @include('rapidez::layouts.partials.header.autocomplete.products')
                        </div>
                        @include('rapidez::layouts.partials.header.autocomplete.all-results')
                    </div>
                    <div v-else class="p-5">
                        @include('rapidez::layouts.partials.header.autocomplete.no-results')
                    </div>
                </div>
            </div>
        </data-search>
    {{-- </x-rapidez::reactive-base> --}}
</autocomplete>
