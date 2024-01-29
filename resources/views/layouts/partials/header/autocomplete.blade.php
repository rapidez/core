@php $inputClasses = 'border !border-gray-200 !text-sm !min-h-0 outline-none !h-auto rounded !p-2 !bg-white w-full focus:!border-inactive' @endphp

<label for="autocomplete-input" class="sr-only">@lang('Search')</label>
<input
    id="autocomplete-input"
    placeholder="@lang('Search')"
    class="{{ $inputClasses }}"
    v-on:focus="$root.loadAutocomplete = true"
    v-if="!$root.loadAutocomplete">

<autocomplete
    v-if="$root.loadAutocomplete"
    v-on:mounted="() => window.document.getElementById('autocomplete-input').focus()"
    v-cloak
    :additionals="{ categories: ['name^3', 'meta_description^1'] }"
    class="w-full"
>
    <x-rapidez::reactive-base slot-scope="{ results, searchAdditionals, additionals }">
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
            :debounce="100"
            :size="10"
            v-on:value-change="searchAdditionals($event)"
        >
            <div
                slot="render"
                slot-scope="{ downshiftProps: { isOpen }, data: suggestions }"
            >
                <div
                    class="{{ config('rapidez.frontend.z-indexes.header-dropdowns') }} absolute -inset-x-10 top-full max-h-[600px] overflow-auto rounded-b-xl border bg-white p-2 md:p-5 shadow-xl md:inset-x-0 md:w-full md:-translate-y-px"
                    v-if="isOpen && (suggestions.length || results.count)"
                >
                    <div class="flex gap-5 pb-5 my-2" v-if="results.categories && results.categories.hits.length">
                        <div class="font-bold">
                            @lang('Categories')
                        </div>
                        <ul class="flex flex-col gap-1">
                            <li v-for="hit in results.categories.hits" class="w-full">
                                <a :href="hit._source.url" class="w-full hover:text-primary flex gap-1">
                                    <span class="ml-2">@{{ hit._source.name }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <ul class="gap-5 grid md:grid-cols-2">
                        <li
                            v-for="suggestion in suggestions"
                            :key="suggestion.source._id">
                            <a :href="suggestion.source.url | url" class="flex flex-wrap flex-1" key="suggestion.source._id">
                                <img :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + suggestion.source.thumbnail + '.webp'" class="self-center object-contain w-14 aspect-square" />
                                <div class="flex flex-1 flex-wrap px-2">
                                    <strong class="hyphens block w-full">@{{ suggestion.source.name }}</strong>
                                    <div class="self-end">@{{ suggestion.source.price | price }}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </data-search>
    </x-rapidez::reactive-base>
</autocomplete>
