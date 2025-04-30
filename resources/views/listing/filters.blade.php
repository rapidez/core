@php($id = uniqid('filters-'))
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
<x-rapidez::slideover.mobile :$id :title="__('Filters')">
    <div class="max-lg:container max-lg:py-6">
        <p class="text-xl/9 font-medium mb-3">@lang('Filters')</p>

        @include('rapidez::listing.partials.filter.selected')
        @include('rapidez::listing.partials.filter.search')
        @include('rapidez::listing.partials.filter.category')

        <template v-for="filter in config.filterable_attributes">
            @include('rapidez::listing.partials.filter.price')
            @include('rapidez::listing.partials.filter.swatch')
            @include('rapidez::listing.partials.filter.boolean')
            @include('rapidez::listing.partials.filter.select')
        </template>

        <x-rapidez::button.primary for="{{ $id }}" class="w-full text-sm lg:hidden">
            @lang('Show results')
        </x-rapidez::button.primary>
    </div>
</x-rapidez::slideover.mobile>

<x-rapidez::button.secondary :for="$id" class="w-full lg:hidden">
    @lang('Filters')
</x-rapidez::button.secondary>
