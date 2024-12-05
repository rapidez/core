@php $inputClasses = 'border !border-default !text-sm !min-h-0 outline-none !h-auto rounded !p-2 !bg-white w-full focus:!border-emphasis' @endphp

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
    <x-rapidez::reactive-base slot-scope="{ results, resultsCount, searchAdditionals, overlay, showOverlay, debounce, size, highlight, searchLoading, startLoading, stopLoading }">
        <div
            class="pointer-events-none fixed z-10 inset-0 cursor-pointer bg-neutral/40 opacity-0 transition duration-500"
            :class="{ 'pointer-events-auto opacity-100 prevent-scroll': overlay, 'opacity-0 pointer-events-none ': !overlay }"
        ></div>
        <x-rapidez::autocomplete.magnifying-glass v-bind:class="{ 'bg-primary text-white': overlay }" />
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
            :debounce="debounce"
            @blur="showOverlay(false)"
            @focus="showOverlay(true)"
            :size="size"
            :highlight="true"
            v-on:value-change="searchAdditionals"
            v-on:key-down="startLoading"
            v-on:suggestions="stopLoading"
        >
            <div slot="render" slot-scope="{ downshiftProps: { isOpen }, data: suggestions, value, loading }">
                <div
                    class="z-header-autocomplete absolute -inset-x-10 top-full max-h-[600px] overflow-auto rounded-b-xl border bg-white p-2 md:p-5 shadow-xl md:inset-x-0 md:w-full md:-translate-y-px"
                    v-if="isOpen && (suggestions.length || resultsCount)"
                >
                    <div v-if="suggestions.length || resultsCount">
                        <div class="flex flex-col prose-li:px-5 hover:prose-li:bg-inactive-100">
                            {{--
                                Additionals will always be shown above the products because of this v-for.
                                With the flex-col above, we can change the order of the items. For example, if we add a blog.blade.php and we add an order-1
                                like this: `<div class="border-t pb-2 order-1" v-if="resultsType == 'blogs'">`, blogs will be shown below the products.
                            --}}
                            <template v-for="(resultsData, resultsType) in results ?? {}" v-if="resultsData?.hits?.length">
                                @foreach (config('rapidez.frontend.autocomplete.additionals') as $key => $fields)
                                    @includeIf('rapidez::layouts.partials.header.autocomplete.' . $key)
                                @endforeach
                            </template>
                            @include('rapidez::layouts.partials.header.autocomplete.products')
                        </div>
                        @include('rapidez::layouts.partials.header.autocomplete.all-results')
                    </div>
                    <div v-else class="p-5">
                        {{-- This will be shown when there are no results in the autocomplete. If you type something
                        that won't have results, for example: "kmerkemrke", this will be shown. --}}
                        @include('rapidez::layouts.partials.header.autocomplete.no-results')
                    </div>
                </div>
            </div>
        </data-search>
    </x-rapidez::reactive-base>
</autocomplete>
