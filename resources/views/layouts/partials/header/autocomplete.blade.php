@php $inputClasses = 'relative z-20 border !font-sans !border-border !text-sm !min-h-0 outline-0 ring-0 !h-auto rounded-xl !pl-4 !pr-24 !py-3.5 !bg-white w-full focus:ring-transparent search-input' @endphp

<div v-if="!$root.loadAutocomplete" class="relative w-full">
    <label for="autocomplete-input" class="sr-only">@lang('Search')</label>
    <input
        id="autocomplete-input"
        placeholder="@lang('Search')"
        class="{{ $inputClasses }}"
        v-on:focus="$root.loadAutocomplete = true"
    >
    <div class="rounded-r-xl border-l absolute right-0 inset-y-0 w-14 pointer-events-none flex items-center justify-center {{ config('rapidez.frontend.z-indexes.search-autocomplete') }}">
        <x-heroicon-o-magnifying-glass class="size-5 text-neutral" />
    </div>
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
    <x-rapidez::reactive-base slot-scope="{ results, resultsCount, searchAdditionals, overlay, showOverlay, debounce, size, highlight }">
        <div
            class="pointer-events-none fixed z-10 inset-0 cursor-pointer bg-black/40 opacity-0 transition duration-500"
            :class="{ 'pointer-events-auto opacity-100': overlay, 'opacity-0 pointer-events-none': !overlay }"
        ></div>
        <div class="rounded-r-xl border-l absolute right-0 inset-y-0 w-14 pointer-events-none flex items-center justify-center {{ config('rapidez.frontend.z-indexes.button-autocomplete') }}">
            <x-heroicon-o-magnifying-glass class="size-5 text-neutral" />
        </div>
        <data-search
            placeholder="@lang('Search')"
            v-on:value-selected="search"
            component-id="autocomplete"
            :inner-class="{ input: '{{ $inputClasses }}' }"
            class="relative [&_*]:!m-0"
            :data-field="Object.keys(config.searchable)"
            :field-weights="Object.values(config.searchable)"
            :show-icon="false"
            fuzziness="AUTO"
            :debounce="debounce"
            @blur="showOverlay(false)"
            @focus="showOverlay(true)"
            :size="size"
            :highlight="true"
            v-on:value-change="searchAdditionals($event)"
        >
            <div
                slot="render"
                slot-scope="{ downshiftProps: { isOpen }, data: suggestions, value }"
            >
                <div
                    class="{{ config('rapidez.frontend.z-indexes.search-autocomplete') }} absolute -inset-x-5 top-14 max-h-svh overflow-x-hidden overflow-y-auto md:top-14 md:max-h-[600px] md:rounded-xl md:border bg-white shadow-xl max-md:pt-0 p-5 md:pt-0 md:inset-x-0 md:w-full md:-translate-y-px"
                    v-if="isOpen && (suggestions.length || resultsCount)"
                >
                    @include('rapidez::layouts.partials.header.autocomplete.all-results')

                    <template v-for="(resultsData, resultsType) in results ?? {}" v-if="resultsData?.hits?.length">
                        @foreach (config('rapidez.frontend.autocomplete.additionals') as $key => $fields)
                            @includeIf('rapidez::layouts.partials.header.autocomplete.' . $key)
                        @endforeach
                    </template>

                    @include('rapidez::layouts.partials.header.autocomplete.products')
                </div>
            </div>
        </data-search>
    </x-rapidez::reactive-base>
</autocomplete>
