@pushOnce('head', 'listing-filters')
    @vite(vite_filename_paths([
        'ClearRefinements.vue',
        'CurrentRefinements.vue',
        'SearchBox.vue',
        'RangeInput.vue',
        'RangeSlider.vue',
        'RefinementList.vue',
        'HierarchicalMenu.vue',
    ]))
@endPushOnce
<x-rapidez::slideover.mobile id="category-filters-slideover" :title="__('Filters')">
    <div class="max-lg:container max-lg:pt-6">
        <div class="text-right max-md:mb-3 md:hidden">
            @include('rapidez::listing.partials.toolbar.sorting')
        </div>
        <p class="text-xl/9 font-medium mb-3 max-lg:hidden">@lang('Filters')</p>

        @include('rapidez::listing.partials.filter.selected')
        @include('rapidez::listing.partials.filter.search')
        @include('rapidez::listing.partials.filter.category')

        <template v-for="filter in config.filterable_attributes">
            @include('rapidez::listing.partials.filter.price')
            @include('rapidez::listing.partials.filter.swatch')
            @include('rapidez::listing.partials.filter.boolean')
            @include('rapidez::listing.partials.filter.select')
        </template>

        <div class="bg-white -mx-5 p-5 sticky bottom-0 lg:hidden">
            <x-rapidez::button.primary for="category-filters-slideover" class="w-full text-sm">
                @lang('Show results')
            </x-rapidez::button.primary>
        </div>
    </div>
</x-rapidez::slideover.mobile>


