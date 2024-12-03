@php $inputClasses = 'border !border-default !text-sm !min-h-0 outline-none !h-auto rounded !p-2 !bg-white w-full focus:!border-emphasis' @endphp

<label for="autocomplete-input" class="sr-only">@lang('Search')</label>
<input
    id="autocomplete-input"
    placeholder="@lang('Search')"
    class="{{ $inputClasses }}"
    v-on:focus="$root.loadAutocomplete = true"
    v-if="!$root.loadAutocomplete"
>

<autocomplete
    v-if="$root.loadAutocomplete"
    v-on:mounted="() => window.document.getElementById('autocomplete-input').focus()"
    v-bind:additionals="{{ json_encode(config('rapidez.frontend.autocomplete.additionals')) }}"
    v-bind:debounce="{{ config('rapidez.frontend.autocomplete.debounce') }}"
    v-bind:size="{{ config('rapidez.frontend.autocomplete.size') }}"
    class="w-full"
    v-cloak
>
    <x-rapidez::reactive-base slot-scope="{ results, resultsCount, searchAdditionals, debounce, size, highlight }">
        <data-search
            placeholder="@lang('Search')"
            v-on:value-selected="search"
            component-id="autocomplete"
            :inner-class="{ input: '{{ $inputClasses }}' }"
            {{-- These classes are only used when you come from a page with a product listing, --}}
            {{-- click on a link that leads to a 404 page and try to use the search there --}}
            class="relative [&_*]:!m-0 [&_[isclearicon=]]:!mr-2 [&_.cancel-icon]:!fill-[#595959] [&_[groupposition=right]]:!absolute [&_[groupposition=right]]:!top-2/4 [&_[groupposition=right]]:!right-0 [&_[groupposition=right]]:!-translate-y-2/4"
            :data-field="Object.keys(config.searchable)"
            :field-weights="Object.values(config.searchable)"
            :show-icon="false"
            fuzziness="AUTO"
            :debounce="debounce"
            :size="size"
            :highlight="true"
            v-on:value-change="searchAdditionals($event)"
        >
            <div
                slot="render"
                slot-scope="{ downshiftProps: { isOpen }, data: suggestions }"
            >
                <div
                    class="{{ config('rapidez.frontend.z-indexes.header-dropdowns') }} absolute -inset-x-10 top-full max-h-[600px] overflow-auto rounded-b-xl border bg-white p-2 md:p-5 shadow-xl md:inset-x-0 md:w-full md:-translate-y-px"
                    v-if="isOpen && (suggestions.length || resultsCount)"
                >
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
