<x-rapidez::slideover.mobile id="category-filters-slideover" closedby="any">
    <x-rapidez::slideover.mobile.header>
        @lang('Filters')
        <x-rapidez::slideover.close commandfor="category-filters-slideover" command="close" :aria-label="__('Close')" />
    </x-rapidez::slideover.mobile.header>
    <x-rapidez::slideover.mobile.content class="max-h-none">
        @include('rapidez::listing.partials.filter.selected')
        @include('rapidez::listing.partials.filter.search')
        @include('rapidez::listing.partials.filter.category')

        <template v-for="filter in config.filterable_attributes">
            @include('rapidez::listing.partials.filter.price')
            @include('rapidez::listing.partials.filter.swatch')
            @include('rapidez::listing.partials.filter.boolean')
            @include('rapidez::listing.partials.filter.select')
        </template>
    </x-rapidez::slideover.mobile.content>
    <x-rapidez::slideover.mobile.footer>
        <x-rapidez::button.primary commandfor="category-filters-slideover" command="close" class="w-full text-sm">
            @lang('Show results')
        </x-rapidez::button.primary>
    </x-rapidez::slideover.mobile.footer>
</x-rapidez::slideover.mobile>

<x-rapidez::button.secondary commandfor="category-filters-slideover" command="show-modal" class="w-full lg:hidden" data-testid="listing-filters-toggle">
    @lang('Filters')
</x-rapidez::button.secondary>
